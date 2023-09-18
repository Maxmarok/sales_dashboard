<?php
namespace App\Services\WB;

use App\Http\Resources\KeyService\CreateKeyResource;
use App\Models\ApiKey;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeyService extends WBMain {

    /**
     * Создает АПИ-ключ
     * @param array $data
     * @return JsonResponse
     */
    public function addKey(array $data)
    {
        $user = Auth()->user();

        $baseUrl = $this->statisticUrl;
        // $type = 'statistic';
        $date = Carbon::now()->subYear()->format('Y-m-d');

        switch($data['type']) {
            case 'statistic':
                $baseUrl = $this->statisticUrl;
                $date = Carbon::now()->subYear()->format('Y-m-d');
                $format = "%s/api/v1/supplier/sales?dateFrom=%s";
                $url = sprintf($format, $baseUrl, $date);
                break;
            case 'standard':
                $baseUrl = $this->mainUrl;
                $format = "%s/public/api/v1/info";
                $url = sprintf($format, $baseUrl);
                break;
            case 'ad':
                $baseUrl = $this->adUrl;
                $format = "%s/adv/v0/adverts";
                $url = sprintf($format, $baseUrl);
                break;
            default:
                break;
        }


        $client = new Client();

        try{
           
            $request = $client->request("GET", $url, [
                "headers" => ["Authorization" => $data['key']]
            ]);

            if($data['type'] === 'statistic') {
                $response = json_decode($request->getBody(), true);
                if($response) (new PrimaryDataService())->loadData($data['type'], $data['lk_id'], $response);
            }

        
            // if($data['type'] === 'statistic') {
            //     $this->getData($data['lk_id'], $data['key'], $url, 'sales');

            //     $type = 'reportDetailByPeriod';
            //     $url = sprintf($format, $baseUrl, $type, $date);

            //     $this->getData($data['lk_id'], $data['key'], $url, 'detail');
            // } else {
            //     $req = Http::withHeaders([
            //         'Authorization' => $data['key'],
            //     ])->timeout(60000)->get($url);
            // }

        }catch (GuzzleException $exception){
            return match ($exception->getCode()) {
                401 => Response()->json([
                    "code" => 401,
                    "message" => "API-ключ неверный"
                ], 401),
                429 => Response()->json([
                    "code" => 429,
                    "message" => $exception->getMessage()
                ], 429),
                default => Response()->json([
                    "code" => 403,
                    "message" => $exception->getMessage()
                ], 403),
            };
        }
        
        
        $key = ApiKey::create($data);

        if($data['type'] === 'ad') {
            return Response()->json([
                "code" => 200,
                "data" => auth()->user()->lk
            ]);
        } else {
            return Response()->json([
                "code" => 200,
                "data" => new CreateKeyResource($key)
            ]);
        } 
        
    }

    public function getData($id, $key, $url, $type)
    {
        $req = Http::withHeaders([
            'Authorization' => $key,
        ])->timeout(60000)->get($url);

        $response = $req->json();

        if($response) (new PrimaryDataService())->loadData($type, $id, $response);
    }
}
