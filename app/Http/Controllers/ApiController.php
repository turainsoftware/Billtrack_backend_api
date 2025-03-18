<?php
namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Sanctum\HasApiTokens;

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

            if (!isset($username) || !preg_match('/^[6-9][0-9]{9}$/', $username)) {
                return response()->json(["status" => false, "message" => "Invalid phone number format."]);
            }

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

        if ($get_otp != $user_details->mobile_otp) {
            throw new \Exception("Invalid OTP.");
        }

        $company_details = DB::table('company_name')
            ->where('company_name_id', $user_details->company_name_id)
            ->first();

        if (!$company_details) {
            throw new \Exception("Company details not found.");
        }

        // Find user model to generate Sanctum token
        $user = \App\Models\Users::find($user_details->user_id);
        if (!$user) {
            throw new \Exception("User model not found.");
        }

        // Generate a Sanctum token
        // $token = $user->createToken('authToken')->plainTextToken;
        $token = explode('|', $user->createToken('authToken')->plainTextToken)[1] ?? '';


        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully',
            'token' => $token,
            'user_data' => [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'phone_number' => $user->contact_no1,
                'user_type' => $user->type,
                'company_name' => $company_details->company_name,
                'company_name_id' => $company_details->company_name_id,
                'created_at' => $user->created_at,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
    

}
