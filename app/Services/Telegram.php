<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Telegram
{
    public function call($method , $params){
        $url = "https://api.telegram.org/bot". config('services.telegram.api_key')."/".$method;
        $response = Http::post($url,$params);
        return $response->json()['result'];
    }
}
