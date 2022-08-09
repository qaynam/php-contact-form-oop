<?php

namespace App;

class Utilities {

    public static function dd($argv, $p = false) {
        
        print '<pre>';

        if($p === true) {
            print_r($argv, true);
        } else {
            var_dump($argv);
        }

        print '</pre>';
        
        die();
    }
    
    /**
     * サイトのURLを取得する
    */
    public static function siteURL() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'].'/';
        return $protocol.$domainName;
    }

}
