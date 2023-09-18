<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected string $mainUrl = 'https://suppliers-api.wildberries.ru';
    protected string $statisticUrl = 'https://statistics-api.wildberries.ru';
    protected string $key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjliY2NlY2M4LTg4Y2YtNDAxMy1hNWZmLWQxZGIxNWZmNjZiYiJ9.dshR-TfGKRpk3LXaj6Ghjnn8BwznhS4Po6YZ7z7LCYg';

    public function main()
    {
        $sales = $this->sales($this->key, '2023-08-21', '2023-08-27');
        $delivery = $this->reportDetailByPeriod($this->key, '2023-08-21', '2023-08-27');
        $arr = array_merge($sales, $delivery);
        return view('main', ['arr' => $arr]); 
    }

    public function reportDetailByPeriod(string $key, string $dateFrom, string $dateTo)
    {
        $data = [
            "headers" => ["Authorization" => $key],
            "query" => [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]
        ];

        $request = $this->sendRequest('statistic', 'GET', '/api/v1/supplier/reportDetailByPeriod', $data);
        //если произошла ошибка
        if(get_class($request) == 'Illuminate\Http\JsonResponse'){
            return false;
        }

        $arr = [
            'delivery_rub' => 0,
        ];

        $response = json_decode($request->getBody());

        foreach ($response as $row){
            $arr['delivery_rub'] += $row->delivery_rub;
        }

        // foreach($arr as $key => $value) {
        //     echo nl2br($key.': '.$value.'\n');
        // }

        return $arr; 
    }

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
}
