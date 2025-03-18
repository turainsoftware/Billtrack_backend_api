<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('chk_username',[ApiController::class,'chk_username']);
Route::post('mobile_otp',[ApiController::class,'mobile_otp']);
Route::get('verify_otp', [ApiController::class, 'verify_otp']);

// Protected routes (Require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user-profile', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });
});



