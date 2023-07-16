<?php

namespace App\Http\Controllers\API\Role;
use App\Http\CoHntrollers\Controller;

class RoleController extends Controller
{
    public function getallrole()
    {
        try{
            $role =  DB::select('select * from role');
            $response = [
                'success' => true,
                'data' => $role,
                'message' => 'Berhasil menampilkan semua role'
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