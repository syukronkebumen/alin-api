<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Agency\Agency;
use App\Models\Permission\Permission as PermissionPermission;
use App\Models\Subscription\Subscription;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User\User;
use App\Models\Permission\Permission;
use App\Models\User\userPermission;
use App\Models\User\Role;
use App\Utils\AlinLogger;
use Illuminate\Support\Str;
use App\Models\User\RoleUser;
// use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Laravel\Passport\Passport;
use Mail;
use App\Mail\SendMail;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $name = Str::random(10);
            $logger = new AlinLogger();
            $logger->runLogDB();
            $dataUser = [
                'name' => $name,
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'isActive' => '0',
                'otp' => rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9),
                'status' => 'private',
                'createAt' => Carbon::now()->round(microtime(true) * 1000),
            ];

            $logger->stopLogDB();
            Log::info("User Register", $dataUser);
            $user = User::create($dataUser);
            $dataUser['token'] = $user->createToken('tokenLogin')->accessToken->token;
            $response = [
                "status" => 200,
                "message" => 'Berhasil Register',
                "data" => $dataUser,
                "error" => []
            ];

            $content = [
                'title' => 'OTP From Newus Technology',
                'body' =>  $dataUser['otp']
            ];
            Mail::to($dataUser['email'])->send(new SendMail($content));
            $logger->writeLogDB('LOG DB', storage_path('logs/database.log'), ['additional_info' => 'data'], Logger::INFO);
            return response()->json($response, 200);
        } catch (\Exception  $e) {
            $response = [
                "status" => 400,
                "message" => $e->getMessage(),
                "data" => [],
                "error" => []
            ];

            return response()->json($response, 400);
        }
    }
    public function login(Request $request)
    {
        try {
            $startTime = microtime(true);
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'data' => [],
                    'message' => $validator->errors(),
                ];
                return response()->json($response, 404);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $userAuth = Auth::user();
                $dataLogin['email'] = $userAuth->email;
                $dataLogin['token'] =  $userAuth->createToken('tokenLogin')->accessToken->token;

                //select user
                $user = new User();
                $selectUser = $user->getUser($userAuth->email);

                $agency = [];
                $app    = [];
                if ($selectUser->agencyCode) {
                    $newAgency = new Agency();
                    $agency = $newAgency->getAgency($selectUser->agencyCode);

                    $newSubscription = new Subscription();
                    $selectSubscription = $newSubscription->getSubscription($selectUser->agencyCode);

                    $tempApp = [];
                    foreach ($selectSubscription as $key => $val) {
                        $temp = $val;
                        $val->setting = json_decode($val->setting, TRUE);
                        $tempApp[] = $temp;
                    }
                    $app = $tempApp;
                }

                $newPermission = new Permission();
                $selectPermission = $newPermission->getPermission($selectUser->userCode);
                $specialPermission = $newPermission->getSpecialPermission($selectUser->userCode);

                $token = $dataLogin['token'];
                $dataToken = [
                    "userCode" => $selectUser->userCode,
                    "email" => $selectUser->email,
                    "isActive" => $selectUser->isActive,
                    "status" => $selectUser->status,
                    "agency" => $agency,
                    "app" => $app,
                    "permission" => $selectPermission,
                    "specialPermission" => $specialPermission,
                    "S3" => [
                        "S3_VERSION" => env('S3_VERSION'),
                        "S3_REGION" => env('S3_REGION'),
                        "AWS_ACCESS_KEY" => env('AWS_ACCESS_KEY'),
                        "AWS_SECRET_ACCESS_KEY" => env('AWS_SECRET_ACCESS_KEY'),
                        "S3_ENDPOINT" => env('S3_ENDPOINT'),
                        "S3_BUCKET" => env("S3_BUCKET")
                    ]
                ];

                $dataToken['token'] = $token;
                Redis::set($token, json_encode($dataToken));
                $getData = Redis::get($token);
                if ($getData) {
                    $dataFromRedis = json_decode($getData);
                    $newDataFromRedis = $dataFromRedis;

                    $endTime = microtime(true);
                    $executionTime = $endTime - $startTime;
                    $timeRequest = round($executionTime, 1);
                    Log::info("User Login", ['timeRequest' => $timeRequest]);

                    $response = [
                        'status' => 200,
                        'message' => 'Berhasil Login',
                        'data' => [
                            'token' => $token,
                            'dataToken' => $newDataFromRedis
                        ],
                        'error' => []
                    ];

                    return response()->json($response, 200);
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Token Expired',
                        'data' => [],
                        'error' => []
                    ];

                    return response()->json($response, 400);
                }
                // $endTime = microtime(true);
                // $executionTime = $endTime - $startTime;
                // $dataLogin['time'] = round($executionTime, 1);
                // Log::info("User Login", $dataLogin);
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'Akun tidak ditemukan',
                    'data' => [],
                    'error' => []
                ];

                return response()->json($response, 400);
            }
        } catch (\Exception  $e) {
            $response = [
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => $e,
                'error' => []
            ];

            return response()->json($response, 400);
        }
    }

    public function otp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
            ]);
            $user = User::firstwhere('email', $request->email);

            $userOtp = [
                'email' => $user->email,
                'otp' => $user->otp,
            ];
            $response = [
                'success' => true,
                'data' => $userOtp,
                'message' => 'OTP berhasil dikirim ke email anda'
            ];
            return response()->json($response, 200);
        } catch (\Exception  $e) {
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];

            return response()->json($response, 404);
        }
    }
    public function checkOtp(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required',
                'email' => 'required|email'
            ]);

            $user = User::where('email', $request->input('email'))->first();

            if ($user && $user->otp === $request->input('otp')) {
                $data = [
                    'email' => $user->email,
                    'otp' => $user->otp,
                ];
                $response = [
                    "status" => 200,
                    "message" => 'OTP is correct',
                    "data" => $data,
                    "error" => []
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    "status" => 400,
                    "message" => 'Invalid OTP',
                    "data" => [],
                    "error" => []
                ];
                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response = [
                "status" => 400,
                "message" => $e->getMessage(),
                "data" => [],
                "error" => []
            ];
            return response()->json($response, 400);
        }
    }
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);
            $user = User::firstwhere('email', $request->input('email'));
            if ($user) {
                $data = [
                    'otp' => rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9),
                ];
                $user->otp = $data['otp'];
                $user->save();
                $dataNew = [
                    'email' => $user->email,
                    'otp' => $data['otp'],
                    'updateAt' => Carbon::now()->round(microtime(true) * 1000)
                ];

                $content = [
                    'title' => 'OTP From Newus Technology',
                    'body' =>  $dataNew['otp']
                ];
                Mail::to($dataNew['email'])->send(new SendMail($content));
                $response = [
                    "status" => 200,
                    "message" => 'OTP berhasil dikirim ke email anda',
                    "data" => $dataNew,
                    "error" => []
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    "status" => 400,
                    "message" => 'Email tidak terdaftar',
                    "data" => [],
                    "error" => []
                ];
                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response = [
                "status" => 400,
                "message" => $e->getMessage(),
                "data" => [],
                "error" => []
            ];
            return response()->json($response, 400);
        }
    }
    public function getAlluser()
    {
        try {
            $user = User::whereNull('deleteAt')->get();
            $response = [
                'success' => true,
                'data' => $user,
                'message' => 'OTP is correct'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
    public function getOneUser($userCode, Request $request)
    {
        try {
            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();
            $user_permission = userPermission::where('userCode', $userCode)->where('deleteAt', null)->get();
            $response = [
                'success' => true,
                'data' => $user,
                'permission' => $user_permission,
                'message' => 'User Found'
            ];
            return response()->json($response, 200);
            if ($user == null) {
                $response = [
                    'success' => false,
                    'message' => 'User Not Found'
                ];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
    public function addPermission($userCode, Request $request)
    {
        try {
            $request->validate([
                'permissionCode' => 'required',
            ]);
            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();
            $permission = Permission::find($request->input('permissionCode'));

            if (!$user || !$permission) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User or Permission not found',
                    'data' => null,
                    'error' => [],
                ], 404);
            }
            $response = [
                'status' => 200,
                'message' => 'Permission Berhasil Ditambahkan',
                'data' => [
                    'usercode' => $user->userCode,
                    'permission' => [
                        'permissionCode' => $permission->permissionCode,
                        'permission' => $permission->permission,
                        'description' => $permission->description,
                        'createAt' => Carbon::now()->round(microtime(true) * 1000)
                    ],
                ],
                'error' => [],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
    public function deletePermission($userCode, Request $request)
    {
        try {
            $request->validate([
                'permissionCode' => 'required',
            ]);
            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();
            $permission = Permission::find($request->input('permissionCode'));
            $upCode = userPermission::where('userCode', $userCode)->where('deleteAt', null)->first('upCode');
            if (!$user || !$permission) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User or Permission not found',
                    'data' => null,
                    'error' => [],
                ], 404);
            }
            $response = [
                'status' => 200,
                'message' => 'Permission Berhasil Dihapus',
                'data' => [
                    'usercode' => $user->userCode,
                    'upCode' => $upCode,
                    'permission' => [
                        'permissionCode' => $permission->permissionCode,
                        'permission' => $permission->permission,
                        'description' => $permission->description,
                        'createAt' => Carbon::now()->round(microtime(true) * 1000)
                    ],
                ],
                'error' => [],
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
    public function addRole($userCode, Request $request)
    {
        try {
            $request->validate([
                'roleCode' => 'required',
            ]);
            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();
            $role = Role::find($request->input('roleCode'));
            if (!$user || !$role) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User or Role not found',
                    'data' => null,
                    'error' => [],
                ], 404);
            }
            $response = [
                'status' => 200,
                'message' => 'Role Berhasil Ditambahkan',
                'data' => [
                    'usercode' => $user->userCode,
                    'role' => [
                        'roleCode' => $role->roleCode,
                        'role' => $role->role,
                        'agencyCode' => $role->agencyCode,
                        'createAt' => Carbon::now()->round(microtime(true) * 1000)
                    ]
                ]
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
    public function deleteRole($userCode, Request $request)
    {
        try {
            $request->validate([
                'roleCode' => 'required',
            ]);
            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();
            $role = Role::find($request->input('roleCode'));
            if (!$user || !$role) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User or Role not found',
                    'data' => null,
                    'error' => [],
                ], 404);
            }
            $roleUpdate = $role->update(['deleteAt' => Carbon::now()->round(microtime(true) * 1000)]);
            $response = [
                'status' => 200,
                'message' => 'Role Berhasil Dihapus',
                'data' => [
                    'usercode' => $user->userCode,
                    'role' => [
                        'roleCode' => $role->roleCode,
                        'role' => $role->role,
                        'agencyCode' => $role->agencyCode,
                        'deleteAt' => $roleUpdate
                    ]
                ]
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
    public function createUser($agencyCode, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
            $user = User::create([
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'email' => $request->input('email'),
                'agencyCode' => $agencyCode,
                'isActive' => 1,
                'otp' => rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9),
                'status' => 'active',
                'createAt' => Carbon::now()->round(microtime(true) * 1000)
            ]);
            $response = [
                'status' => 200,
                'message' => 'User has been Created Successfully',
                'data' => $user,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function editUser($userCode, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User not found',
                    'data' => null,
                    'error' => [],
                ], 404);
            }

            // Update the user data
            $user->update([
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'email' => $request->input('email'),
            ]);

            $response = [
                'status' => 200,
                'message' => 'User has been Updated Successfully',
                'data' => $user,
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function deleteUser($userCode)
    {
        try {
            $user = User::where('userCode', $userCode)->where('deleteAt', null)->first();

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User not found',
                    'data' => null,
                    'error' => [],
                ], 404);
            }

            $userDelete = $user->update(['deleteAt' => Carbon::now()->round(microtime(true) * 1000)]);

            $response = [
                'status' => 200,
                'message' => 'User has been Deleted Successfully',
                'data' => $user,
                'updateAt' => $userDelete
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function authenticate()
    {
        $response = [
            'status' => 401,
            'message' => '401 Unauthorized',
            'data' => [],
            'error' => ''
        ];

        return response()->json($response, 401);
    }
}
