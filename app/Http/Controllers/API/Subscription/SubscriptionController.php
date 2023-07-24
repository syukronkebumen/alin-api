<?php

namespace App\Http\Controllers\API\Subscription;
use App\Http\Controllers\Controller;
use App\Models\Subscription\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class SubscriptionController extends Controller
{
    public function getonesub($subscriptionCode){
        try{
            $sub = Subscription::where('subscriptionCode', $subscriptionCode)
            ->where('deleteAt',NULL)
            ->first();
            // $role = Role::firstWhere('role', $request->role);
            if($sub==NULL){
                return response()->json(['message' => 'Subscription Tidak Ada'], 404);
            }
            $response = [
                'success' => true,
                'data' => $sub,
                'message' => 'Berhasil menampilkan satu subscription',
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
    
    public function addsub(Request $request)
    {
        try{
            $request->validate([
                'agencyCode' => 'required',
                'appCode' => 'required',
                'price' => 'required',
                'setting' => 'required',
                'endDate' => 'required',
            ]);

            $newsub = [
                'agencyCode' => $request -> input('agencyCode'),
                'appCode' => $request -> input('appCode'),
                'price' => $request -> input('price'),
                'setting' => $request -> input('setting'),
                'startDate' => Carbon::now()->toDateTimeString(),
                'endDate' => Carbon::now()->addMonths($request -> input('endDate'))->toDateTimeString(),
                'createAt' => Carbon::now()->toDateTimeString(),
            ];

            DB::table('subscription')->insert($newsub);

            $response = [
                'success' => true,
                'data' => $newsub,
                'message' => 'Berhasil Membuat Subscription'
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

    public function updatesub($subscriptionCode, Request $request){
        try{
            $request->validate([
                'price' => 'required',
                'setting' => 'required',
            ]);
            $sub = Subscription::where('subscriptionCode', $subscriptionCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$sub) {
                return response()->json(['message' => 'Subscription Tidak Ada'], 404);
            }
            
            $subupdate=[
                'price' => $request->input('price'),
                'setting' => $request->input('setting'),
                'updateAt' => Carbon::now()->toDateTimeString(),
            ];

            $sub -> update($subupdate);

            $response = [
                'success' => true,
                'data' => $sub,
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

    public function deletesub($subscriptionCode){
        try{
            $sub = Subscription::where('subscriptionCode', $subscriptionCode)
            ->where('deleteAt',NULL)
            ->first();

            if (!$sub) {
                return response()->json(['message' => 'Subscription Tidak Ada'], 404);
            }
            
            $subupdate=[
                'deleteAt' => Carbon::now()->toDateTimeString(),
            ];

            $sub -> update($subupdate);

            $response = [
                'success' => true,
                'data' => $sub,
                'message' => 'Berhasil Delete Subscription'
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