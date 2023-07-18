<?php

namespace App\Http\Controllers\API\RolePermission;
use App\Http\Controllers\Controller;
use App\Models\RolePermission\RolePermission;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class RolePermissionController extends Controller
{
    public function deleterp($roleCode, $permissionCode){
        try{
            $rolepermission = RolePermission::where('roleCode', $roleCode)
            ->where('permissionCode', $permissionCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$rolepermission) {
                return response()->json(['message' => 'Role Permission Tidak Ada'], 404);
            }
            
            $rolepermissionupdate=[
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $rolepermission -> update($rolepermissionupdate);

            $response = [
                'success' => true,
                'data' => $rolepermission,
                'message' => 'Berhasil Delete Role Permission'
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