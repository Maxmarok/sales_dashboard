<?php
namespace App\Services\WB;

use App\Http\Resources\KeyService\CreateKeyResource;
use App\Models\ApiKey;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class KeyService extends WBMain {

    /**
     * Создает АПИ-ключ
     * @param array $data
     * @return JsonResponse
     */
    public function addKey(array $data)
    {
        $user = Auth()->user();
        switch($data['type']) {
            case 'statistic':
                $url = $this->statisticUrl."/api/v1/supplier/sales?dateFrom=".Carbon::now()->subYear()->format('Y-m-d');
                break;
            case 'standard':
                $url = "$this->mainUrl/public/api/v1/info";
                break;
            case 'ad':
                $url = "$this->adUrl/adv/v0/adverts";
                break;
            default:
                break;
        }
        $client = new Client();

        try{
            $request = $client->request("GET", $url, [
                "headers" => ["Authorization" => $data['key']]
            ]);
        }catch (GuzzleException $exception){
            return match ($exception->getCode()) {
                401 => Response()->json([
                    "code" => 401,
                    "message" => "API-ключ неверный"
                ], 401),
                default => Response()->json([
                    "code" => 403,
                    "message" => $exception->getMessage()
                ], 403),
            };
        }

        $key = ApiKey::create($data);


        return Response()->json([
            "code" => 200,
            "message" => new CreateKeyResource($key)
        ]);
    }
}
