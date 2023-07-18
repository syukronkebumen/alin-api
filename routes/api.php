<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\User\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// start
Route::group([
    'prefix' => 'v2/user',
], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/otp', [UserController::class, 'otp']);
    Route::post('/checkotp', [UserController::class, 'checkOtp']);
    Route::post('/sendotp', [UserController::class, 'sendOtp']);
    Route::get('/all', [UserController::class, 'getAlluser']);
    Route::post('/{userCode}/one', [UserController::class, 'getOneuser']);
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});
