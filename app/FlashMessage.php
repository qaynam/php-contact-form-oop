<?php

namespace App;

class FlashMessage {


    public function __construct()
    {
        //セッションを有効にする
        session_start();
    }

    static public function store($name, $message)
    {

        if(!isset($name) || empty($name)) throw new \Exception('name パラメータが渡されていません。');
        if(!isset($message) || empty($message)) throw new \Exception('message パラメータが渡されていません。');

        if(!isset($_SESSION['flash'])) $_SESSION['flash'] = array();

        if(!empty($_SESSION['flash'][$name])) unset($_SESSION[$name]);

        $_SESSION['flash'][$name] = $message;

    }

    static public function show()
    {
        if(!isset($_SESSION)) session_start();
        
        $message = array();

        if(isset($_SESSION['flash']) && is_array($_SESSION['flash'])) {
            $message = $_SESSION['flash'];

            unset($_SESSION['flash']);
        }

        return $message;

    }

}
