<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\Agency\Agency;
use App\Models\Permission\Permission;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RefreshController extends Controller
{
    public function index(Request $request)
    {

        try {
            $Authorization = (($request->headers->get('Authorization')) ? $request->headers->get('Authorization') : '');
            if ($Authorization) {
                $redis = json_decode(Redis::get($Authorization));

                $user = new User();
                $selectUser = $user->getUser($redis->email);

                $agency = [];
                $app    = [];
                if ($selectUser->agencyCode) {
                    $newAgency = new Agency();
                    $agency = $newAgency->getAgency($selectUser->agencyCode);

                    $newSubscription = new Subscription();
                    $selectSubscription = $newSubscription->getSubscription($selectUser->agencyCode);

                    $tempApp = [];
                    foreach ($selectSubscription as $key => $val) {
                        $temp = $val;
                        $val->setting = json_decode($val->setting, TRUE);
                        $tempApp[] = $temp;
                    }
                    $app = $tempApp;
                }

                $newPermission = new Permission();
                $selectPermission = $newPermission->getPermission($selectUser->userCode);
                $specialPermission = $newPermission->getSpecialPermission($selectUser->userCode);

                $token = Str::random(64);
                $dataToken = [
                    "userCode" => $selectUser->userCode,
                    "email" => $selectUser->email,
                    "isActive" => $selectUser->isActive,
                    "status" => $selectUser->status,
                    "agency" => $agency,
                    "app" => $app,
                    "permission" => $selectPermission,
                    "specialPermission" => $specialPermission,
                    "S3" => [
                        "S3_VERSION" => env('S3_VERSION'),
                        "S3_REGION" => env('S3_REGION'),
                        "AWS_ACCESS_KEY" => env('AWS_ACCESS_KEY'),
                        "AWS_SECRET_ACCESS_KEY" => env('AWS_SECRET_ACCESS_KEY'),
                        "S3_ENDPOINT" => env('S3_ENDPOINT'),
                        "S3_BUCKET" => env("S3_BUCKET")
                    ]
                ];

                $dataToken['token'] = $token;
                Redis::set($token, json_encode($dataToken));
                $getData = Redis::get($token);
                if ($getData) {
                    $dataFromRedis = json_decode($getData);
                    $newDataFromRedis = $dataFromRedis;

                    $response = [
                        'status' => 200,
                        'message' => 'Success Refresh',
                        'data' => [
                            'token' => $token,
                            'dataToken' => $newDataFromRedis
                        ],
                        'error' => []
                    ];

                    return response()->json($response, 200);
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Token Expired',
                        'data' => [],
                        'error' => []
                    ];

                    return response()->json($response, 400);
                }
            }
        } catch (\Exception $e) {
            $response = [
                "status" => 400,
                "message" => $e->getMessage(),
                "data" => [],
                "error" => []
            ];

            return response()->json($response, 400);
        }
    }
}
