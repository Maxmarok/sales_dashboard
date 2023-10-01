<?php
namespace App\Services\WB;

use App\Http\Resources\KeyService\CreateKeyResource;
use App\Jobs\UpdateReportByPeriodJob;
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
    public function addKey(array $data, bool $init = false)
    {
        $user = Auth()->user();

        $baseUrl = $this->statisticUrl;
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
                if($response && $init) (new PrimaryDataService())->loadData($data['type'], $data['lk_id'], $response);
            }

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
        
        if($init) {
            $key = ApiKey::create($data);
        } else {
            $key = ApiKey::where('id', $data['id'])->update($data);

            return Response()->json([
                "code" => 200,
                "data" => $key
            ]);
        }
        
        if($data['type'] === 'statistic' && $init) UpdateReportByPeriodJob::dispatch($user);

        return Response()->json([
            "code" => 200,
            "data" => new CreateKeyResource($key)
        ]);
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
