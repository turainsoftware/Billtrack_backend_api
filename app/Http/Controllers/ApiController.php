<?php
namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * @OA\Info(
 *     title="BillTrack API",
 *     version="1.0",
 *     description="This is a sample API documentation using Swagger in Laravel."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */

class ApiController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/chk_username",
 *     summary="Check if a username exists",
 *     tags={"Login Api"},
 *     @OA\Parameter(
 *         name="user_name",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Username exists or not"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request"
 *     )
 * )
 */
    public function chk_username(Request $request)
{
    if (!$request->isMethod('get')) {
        return response()->json(["status" => false, "message" => "Invalid request method."], 400);
    }

    $username = $request->query('user_name');

    // Validate phone number format
    if (!isset($username) || !preg_match('/^[6-9]\d{9}$/', $username)) {
        return response()->json(["status" => false, "message" => "Invalid phone number format."]);
    }

    try {
        // Optimized query: Fetch only user_name to minimize data retrieval
        $exists = DB::table('user')
            ->select('user_name') // Select only required column
            ->where('user_name', $username) // No need for BINARY unless case-sensitive
            ->where('active_status', 1)
            ->where('login_status', 1)
            ->limit(1) // Stops execution once a match is found
            ->first();

        return response()->json([
            "status" => (bool) $exists,
            "message" => $exists ? "Phone number is valid." : "Phone number not registered."
        ]);
    } catch (\Exception $e) {
        return response()->json([
            "status" => false, 
            "message" => config('app.debug') ? $e->getMessage() : "An error occurred."
        ]);
    }
}

    
/**
 * @OA\Post(
 *     path="/api/mobile_otp",
 *     summary="Generate OTP for mobile verification",
 *     tags={"Login Api"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"phone_number"},
 *             @OA\Property(property="phone_number", type="string", example="9876543210")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OTP sent successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid phone number format"
 *     )
 * )
 */

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
    
 /**
 * @OA\Get(
 *     path="/api/verify_otp",
 *     summary="Validate OTP for mobile verification",
 *     tags={"Login Api"},
 *     @OA\Parameter(
 *         name="phone_number",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", example="7003906943")
 *     ),
 *     @OA\Parameter(
 *         name="getotp",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", example="1234")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OTP Verified successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid phone number format"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid OTP or user not found"
 *     )
 * )
 */
    public function verify_otp(Request $request)
    {
        $get_otp = $request->input('getotp');
        $phone_number = $request->input('phone_number');
    
        $user_details = DB::table('user')
            ->select('user_id', 'contact_no1', 'mobile_otp', 'company_name_id', 'type', 'name', 'created_at')
            ->where('contact_no1', $phone_number)
            ->first();
    
        if (!$user_details || $get_otp != $user_details->mobile_otp) {
            return response()->json(['status' => 'error', 'message' => 'Invalid OTP'], 401);
        }

        $company_details = DB::table('company_name')
            ->select('company_name', 'company_name_id')
            ->where('company_name_id', $user_details->company_name_id)
            ->first();
    
        $user = Users::find($user_details->user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User model not found'], 500);
        }
    
        // Generate token efficiently
        $token = Str::after($user->createToken('authToken')->plainTextToken, '|');
    
        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully',
            'token' => $token,
            'user_data' => [
                'user_id' => $user->user_id,
                'name' => $user->name,
            ]
        ]);
    }

    
     

}
