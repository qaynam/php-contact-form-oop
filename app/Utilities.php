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

}