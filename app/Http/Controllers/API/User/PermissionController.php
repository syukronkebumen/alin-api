<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User\User;

class PermissionController extends Controller
{

    // ...

    public function getAllpermission()
    {
        try {
            $permissions = Permission::get();
            $response = [
                'status' => 200,
                'message' => 'Success',
                'data' => $permissions,
                'error' => [],
            ];
            if ($permissions == null) {
                $response = [
                    'status' => 404,
                    'message' => 'Data not found',
                    'data' => null,
                    'error' => [],
                ];
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error retrieving permissions',
                'data' => null,
                'error' => [$e->getMessage()],
            ], 500);
        }
    }
}
