<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User\User;
use Carbon\Carbon;

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

    public function deletepermission($permissionCode)
    {
        try {
            $permission = Permission::where('permissionCode', $permissionCode)
                ->where('deleteAt', NULL)
                ->first();

            if (!$permission) {
                return response()->json(['message' => 'Permission Tidak Ada'], 404);
            }

            $permissionupdate = [
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $permission->update($permissionupdate);

            $response = [
                'success' => true,
                'data' => $permission,
                'message' => 'Berhasil Delete Permission'
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
