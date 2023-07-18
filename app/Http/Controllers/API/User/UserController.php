<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User\User;
use App\Models\User\Permission;
use Laravel\Passport\Passport;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:user,email',
                'password' => 'required|string|min:6',
            ]);


            // $user = DB::table("user")create([
            //     'name' => $request->input('name'),
            //     'email' => $request->input('email'),
            //     'password' => Hash::make($request->input('password')),
            // ]);

            $dataUser = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'isActive' => 1,
                'otp' => rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9),
                'status' => 'active',
                'createAt' => Carbon::now()->round(microtime(true) * 1000)
            ];

            Log::info("User Register", $dataUser);
            DB::table('user')->insert($dataUser);

            // jangan lupa dibuatkan log nya
            $response = [
                'success' => true,
                'data' => $dataUser,
                'message' => 'Berhasil Register'
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
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|exists:user,email',
                'password' => 'required',
            ]);

            $user = User::firstwhere('email', $request->email);

            if ($user && Hash::check($request->password, $user->password)) {
                $dataLogin = [
                    'email' => $user->email,
                    'password' => $user->password,
                ];
                $response = [
                    'success' => true,
                    'data' => $dataLogin,
                    'message' => 'Berhasil Login',
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'success' => false,
                    'data' => [],
                    'message' => 'Password Salah'
                ];
                return response()->json($response, 404);
            }
        } catch (\Exception  $e) {
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];

            return response()->json($response, 404);
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
                    'success' => true,
                    'data' => $data,
                    'message' => 'OTP is correct'
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Invalid OTP'
                ];
                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
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

                $response = [
                    'success' => true,
                    'data' => $dataNew,
                    'message' => 'OTP berhasil dikirim ke email anda'
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Email tidak terdaftar'
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
            $response = [
                'success' => true,
                'data' => $user,
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
}
