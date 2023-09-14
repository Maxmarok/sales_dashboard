<?php
namespace App\Services\WB;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

abstract class WBMain{
    protected string $mainUrl = 'https://suppliers-api.wildberries.ru';
    protected string $adUrl = 'https://advert-api.wb.ru';
    protected string $statisticUrl = 'https://statistics-api.wildberries.ru';

    /**
     * Отправляет API запрос
     *
     * $hostType = main|statistic
     * $method = GET|POST|PUT..
     * $url = endpoint
     * $data = Заголовки, query, body..
     *
     * УСТАРЕЛ!!!
     * В будущем будет удален!!!
     *
     * @param string $hostType
     * @param string $method
     * @param string $url
     * @param array $data
     * @return JsonResponse|ResponseInterface
     */
    public function sendRequest(string $hostType, string $method, string $url, array $data): \Illuminate\Http\JsonResponse|\Psr\Http\Message\ResponseInterface
    {
        $host = $hostType == 'main' ? $this->mainUrl : $this->statisticUrl;

        try{
            $client = new Client();
            $request = $client->request($method, "$host$url", $data);
        }catch (GuzzleException $exception){
            return match ($exception->getCode()) {
                401 => Response()->json([
                    "code" => 401,
                    "message" => "API-ключ неверный",
                    "error" => $exception->getMessage()
                ], 401),
                default => Response()->json([
                    "code" => 403,
                    "message" => "Сервис временно не доступен",
                    "error" => $exception->getMessage()
                ], 403),
            };
        }
        return $request;
    }

    public function checkRequest($request, string $type, array $data)
    {
        $messages = [
            'detail' => [
                'error' => 'Ошибка при создании детального отчета!',
                'null' => 'Пустой детальный отчет!'
            ],
            'stocks' => [
                'error' =>  'Ошибка при создании отчета Stocks!',
                'null' => 'Пустой отчет Stocks'
            ],
            'orders' => [
                'error' => 'Ошибка при создании отчета заказов!',
                'null' => 'Пустой отчет заказов'
            ],
            'sales' => [
                'error' => 'Ошибка при создании отчета продаж!',
                'null' => 'Пустой отчет продаж'
            ],
            'prices' => [
                'error' => 'Ошибка при загрузке цен',
                'null' => 'Цены вернули null'
            ]
        ];

        if($request == null){
            Log::channel('reports')->info($messages[$type]['null']);
            return true;
        }

        if($request->getStatusCode() != 200){
            Log::channel('reports')->emergency($messages[$type]['error'], $data);
            return false;
        }

        return true;
    }
}
