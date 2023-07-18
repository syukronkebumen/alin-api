<?php

namespace App\Http\Controllers\API\Role;
use App\Http\Controllers\Controller;
use App\Models\Role\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class RoleController extends Controller
{
    public function getallrole()
    {
        try{
            $role =  Role::whereNull('deleteAt')->get();
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

    public function getonerole($roleCode)
    {
        try{
            $role = Role::where('roleCode', $roleCode)
            ->where('deleteAt',NULL)
            ->first();
            // $role = Role::firstWhere('role', $request->role);
            if($role==NULL){
                return response()->json(['message' => 'Role Tidak Ada'], 404);
            }
            $response = [
                'success' => true,
                'data' => $role,
                'message' => 'Berhasil menampilkan satu role',
            ];

            return response()->json($response, 200);
        }catch(\Exception $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];
            
            return response()->json($response, 404);
        }
    }

    public function deleterole($roleCode){
        try{
            $role = Role::where('roleCode', $roleCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$role) {
                return response()->json(['message' => 'Role Tidak Ada'], 404);
            }
            
            $roleupdate=[
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $role -> update($roleupdate);

            $response = [
                'success' => true,
                'data' => $role,
                'message' => 'Berhasil Delete Role'
            ];

            return response()->json($response, 200);
            
        }catch(\Exception $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];
            
            return response()->json($response, 404);
        }
    }

    public function createrole(Request $request)
    {
        try{
            $request->validate([
                'role' => 'required',
                'agencyCode' => 'required',
            ]);

            $newdatarole = [
                'role' => $request -> input('role'),
                'agencyCode' => $request -> input('agencyCode'),
                'status' => 'active',
                'createAt' => Carbon::now()->toDateTimeString()
            ];

            DB::table('role')->insert($newdatarole);

            $response = [
                'success' => true,
                'data' => $newdatarole,
                'message' => 'Berhasil Membuat Role'
            ];

            return response()->json($response, 200);

        }catch(\Exception $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];
            
            return response()->json($response, 404);
        }
    }

    public function updaterole($roleCode, Request $request){
        try{
            $request->validate([
                'role' => 'required',
            ]);
            $role = Role::where('roleCode', $roleCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$role) {
                return response()->json(['message' => 'Role Tidak Ada'], 404);
            }
            
            $roleupdate=[
                'role' => $request->input('role'),
                'updateAt' => Carbon::now()->toDateTimeString(),
            ];

            $role -> update($roleupdate);

            $response = [
                'success' => true,
                'data' => $role,
                'message' => 'Berhasil Update Role'
            ];

            return response()->json($response, 200);
            
        }catch(\Exception $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];
            
            return response()->json($response, 404);
        }
    }

    public function deleterp($roleCode, $permissionCode){
        try{
            $role = Role::where('roleCode', $roleCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$role) {
                return response()->json(['message' => 'Role Tidak Ada'], 404);
            }
            
            $roleupdate=[
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $role -> update($roleupdate);

            $response = [
                'success' => true,
                'data' => $role,
                'message' => 'Berhasil Delete Role'
            ];

            return response()->json($response, 200);
            
        }catch(\Exception $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];
            
            return response()->json($response, 404);
        }
    }
}