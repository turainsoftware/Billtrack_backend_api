<?php
namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ApiController extends Controller
{
    public function chk_username(Request $request)
    {
        try {
            if (!$request->isMethod('get')) {
                return response()->json(["status" => false, "message" => "Invalid request method."]);
            }
    
            $username = $request->query('user_name');
    
            // Validate phone number format
            if (!isset($username) || !preg_match('/^[6-9][0-9]{9}$/', $username)) {
                return response()->json(["status" => false, "message" => "Invalid phone number format."]);
            }
    
            // Optimize query by selecting only the necessary column
            $exists = DB::table('user')
                ->where('user_name', $username)
                ->where('active_status', 1)
                ->where('login_status', 1)
                ->exists();
    
            return response()->json([
                "status" => $exists,
                "message" => $exists ? "Phone number is valid." : "Phone number not registered."
            ], 200, [], JSON_UNESCAPED_UNICODE);
    
        } catch (\Exception $e) {
            return response()->json(["status" => false, "message" => "An error occurred."]);
        }
    }
    


    public function mobile_otp(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|digits:10'
            ]);
    
            $phone_number = $request->input('phone_number');
    
            // Generate OTP
            // $otp = random_int(1000, 9999);
            $otp = 1234;
    
            Session::put('phone_number', $phone_number);
    
            $user_exists = DB::table('user')->where('contact_no1', $phone_number)->first();
    
            $data = [
                'mobile_otp' => $otp,
                'contact_no1' => $phone_number,
                'active_status' => 1,
                'login_status' => 1,
            ];
    
            if ($user_exists) {
                DB::table('user')->where('contact_no1', $phone_number)->update($data);
            }
    
            Session::put('otp', $otp);
    
            return response()->json([
                'success' => true,
                'message' => 'OTP has been generated successfully',
                'phone_number' => $phone_number,
                // 'otp' => $otp
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    

    public function verify_otp(Request $request)
    {
        try {

            $get_otp = $request->input('getotp');
            $phone_number = $request->input('phone_number');

            $user_details = DB::table('user')->where('contact_no1', $phone_number)->first();

            if (!$user_details) {
                throw new \Exception("User not found.");
            }

            $user_otp = $user_details->mobile_otp;
            

            $company_details = DB::table('company_name')
                ->where('company_name_id', $user_details->company_name_id)
                ->first();

            if (!$company_details) {
                throw new \Exception("Company details not found.");
            }

            $session_data = [
                'user_id' => $user_details->user_id,
                'user_type' => $user_details->type,
                'company_name' => $company_details->company_name,
                'company_name_id' => $company_details->company_name_id,
                'logged_in' => true,
                'logged_in_as' => "admin"
            ];

            Session::put($session_data);

            if ($user_details->type == 'A') {
                Session::put('permission_id', '1,2,3,4,5,6,7,8,9');
            } else {
                Session::put('permission_id', $user_details->user_role_id);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
