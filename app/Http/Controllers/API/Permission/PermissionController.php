<?php

namespace App\Http\Controllers\API\Permission;
use App\Http\Controllers\Controller;
use App\Models\Permission\Permission;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class PermissionController extends Controller
{
    public function deletepermission($permissionCode){
        try{
            $permission = Permission::where('permissionCode', $permissionCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$permission) {
                return response()->json(['message' => 'Permission Tidak Ada'], 404);
            }
            
            $permissionupdate=[
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $permission -> update($permissionupdate);

            $response = [
                'success' => true,
                'data' => $permission,
                'message' => 'Berhasil Delete Permission'
            ];

            return response()->json($response, 200);
        }catch (\Exception  $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];
            
            return response()->json($response, 404);
        }
    }
    
}