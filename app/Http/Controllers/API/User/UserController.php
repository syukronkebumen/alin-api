<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function register()
    {
        try {
            //code disini










            // jangan lupa dibuatkan log nya
            $data = [];
            $response = [
                'success' => true,
                'data' => $data,
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
}
