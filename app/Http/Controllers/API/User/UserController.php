<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                'isActive'=> 1,
                'otp'=> 123456,
                'status'=> 'active',
                'createAt' => Carbon::now()->toDateTimeString()  
            ];

            Log::info("User Register",$dataUser);
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
    public function login(Request $request){
        try{
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $dataLogin = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];
            Log::info("User login",$dataLogin);
            $response = [
                'success' => true,
                'data' => $dataLogin,
                'message' => 'Berhasil Login'
            ];
            return response()->json($response, 200);
        }catch (\Exception  $e) {
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];

            return response()->json($response, 404);
        }
    }
}
