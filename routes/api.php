<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\User\PermissionController;
use App\Http\Controllers\API\Role\RoleController;
use App\Http\Controllers\API\RolePermission\RolePermissionController;
use App\Http\Controllers\API\Subscription\SubscriptionController;
use App\Http\Controllers\API\User\RefreshController;

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
    'prefix' => 'v2/auth',
], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/otp', [UserController::class, 'otp']);
    Route::post('/checkotp', [UserController::class, 'checkOtp']);
    Route::post('/sendotp', [UserController::class, 'sendOtp']);
    Route::get('/authenticate', [UserController::class, 'authenticate'])->name('unauthorized');
    Route::group([
        'middleware' => 'header',
    ], function () {
        Route::get('/refresh', [RefreshController::class, 'index']);
        // Route::post('checkin', 'API\User\UserController@checkin');
        // Code disini
    });
});
Route::group([
    'prefix' => 'v2/user',
], function () {
    Route::get('/all', [UserController::class, 'getAlluser']);
    Route::post('/{userCode}/one', [UserController::class, 'getOneuser']);
    Route::post('/{userCode}/addPermission/{permissionCode}', [UserController::class, 'addPermission']);
    Route::post('/{userCode}/delPermission/{permissionCode}', [UserController::class, 'deletePermission']);
    Route::post('/{userCode}/addRole', [UserController::class, 'addRole']);
    Route::post('/{userCode}/delRole', [UserController::class, 'deleteRole']);
    Route::post('/{userCode}/create', [UserController::class, 'createUser']);
    Route::post('/update/{userCode}', [UserController::class, 'editUser']);
    Route::post('/delete/{userCode}', [UserController::class, 'deleteUser']);
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
    Route::get('/all', [PermissionController::class, 'getAllpermission']);
    Route::post('/{permissionCode}/delete', [PermissionController::class, 'deletepermission']);
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
