<?php

namespace App;

class HttpResponse {


    public static function acceptMethods($methos)
    {
        $request_method = $_SERVER['REQUEST_METHOD'];

        if(!is_array($methos)) throw new \Exception('methods パラメータは必須です。');

        if(!in_array($request_method, $methos)) {
            http_response_code(405);
            die();
        }

    }

    public static function redirect($url)
    {   
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            header('Location:'.$url);
        } else {
            echo("$url is not a valid URL");
        }

    }
    
}