<?php

namespace App\Http\Controllers\API\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgencyController extends Controller
{

    public function addagency(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'noHp' => 'required',
                'address' => 'required',
                'logo' => 'required',
                'domain' => 'required',
            ]);

            $newagency = [
                'name' => $request -> input('name'),
                'email' => $request -> input('email'),
                'noHp' => $request -> input('noHp'),
                'address' => $request -> input('address'),
                'logo' => $request -> input('logo'),
                'domain' => $request -> input('domain'),
                'createAt' => Carbon::now()->toDateTimeString(),
            ];

            Agency::insert($newagency);

            $response = [
                'success' => true,
                'data' => $newagency,
                'message' => 'Berhasil Membuat Agency'
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
    
    public function getoneagency($agencyCode)
    {
        try{

            $agency = Agency::where('agencyCode', $agencyCode)
            ->where('deleteAt',NULL)
            ->first();
            if($agency==NULL){
                return response()->json(['message' => 'Agency Tidak Ada'], 404);
            }
            $response = [
                'success' => true,
                'data' => $agency,
                'message' => 'Berhasil menampilkan satu Agency',
            ];

            return response()->json($response, 200);
        }catch(\Exception  $e){
            $response = [
                'success' => false,
                'data' => $e,
                'message' => $e->getMessage()
            ];

            return response()->json($response, 404);
        }
    }

    public function updateagency($agencyCode, Request $request){
        try{
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'noHp' => 'required',
                'address' => 'required',
                'logo' => 'required',
                'domain' => 'required',
            ]);

            $agency = Agency::where('agencyCode', $agencyCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$agency) {
                return response()->json(['message' => 'Agency Tidak Ada'], 404);
            }
            
            $agencyupdate=[
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'noHp' => $request->input('noHp'),
                'address' => $request->input('address'),
                'logo' => $request->input('logo'),
                'domain' => $request->input('domain'),
                'updateAt' => Carbon::now()->toDateTimeString(),
            ];

            $agency -> update($agencyupdate);

            $response = [
                'success' => true,
                'data' => $agency,
                'message' => 'Berhasil Update Subscription'
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

    public function deleteagency($agencyCode){
        try{
            $agency = Agency::where('agencyCode', $agencyCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$agency) {
                return response()->json(['message' => 'Agency Tidak Ada'], 404);
            }
            
            $agencyupdate=[
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $agency -> update($agencyupdate);

            $response = [
                'success' => true,
                'data' => $agency,
                'message' => 'Berhasil Delete Agency'
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
