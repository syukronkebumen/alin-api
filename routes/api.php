<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Role\RoleController;
use App\Http\Controllers\API\Permission\PermissionController;
use App\Http\Controllers\API\RolePermission\RolePermissionController;
use App\Http\Controllers\API\Subscription\SubscriptionController;

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
    // Route::post('login', 'API\User\UserController@login');
    // Code disini
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});

Route::group([
    'prefix' => 'v2/role',
], function () {
    Route::get('/all', [RoleController::class, 'getallrole']);
    Route::get('/{roleCode}/one', [RoleController::class, 'getonerole']);
    Route::post('/{roleCode}/delete', [RoleController::class, 'deleterole']);
    Route::post('/add', [RoleController::class, 'createrole']);
    Route::post('/{roleCode}/update', [RoleController::class, 'updaterole']);
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});

Route::group([
    'prefix' => 'v2/permission',
], function () {
    // Route::get('/all', [RoleController::class, 'getallrole']);
    // Route::post('/{roleCode}/one', [RoleController::class, 'getonerole']);
    Route::post('/{permissionCode}/delete', [PermissionController::class, 'deletepermission']);
    // Route::post('/add', [RoleController::class, 'createrole']);
    // Route::post('/{roleCode}/update', [RoleController::class, 'updaterole']);
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});

Route::group([
    'prefix' => 'v2/rolepermission',
], function () {
    Route::post('/{roleCode}/{permissionCode}/delete', [RolePermissionController::class, 'deleterp']);
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});

Route::group([
    'prefix' => 'v2/subscription',
], function () {
    Route::get('/{subscriptionCode}/one', [SubscriptionController::class, 'getonesub']);
    Route::post('/add', [SubscriptionController::class, 'addsub']);
    Route::post('/{subscriptionCode}/update', [SubscriptionController::class, 'updatesub']);
    Route::post('/{subscriptionCode}/delete', [SubscriptionController::class, 'deletesub']);
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});
