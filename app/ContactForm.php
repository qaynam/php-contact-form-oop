<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;

class ContactForm {

    public $email;
    public $title;
    public $content;

    //フォームのバリデーション
    public static function validation($request) 
    {   
        //バリデーションエラーメッセージたち
        $validation_errors = [];
        
        if(!isset($request->name) || $request->name == null) array_push($validation_errors, "お名前は必須です。");
        if(!isset($request->email) || $request->email == null) array_push($validation_errors, "メールアドレスは必須です。");
        if(!isset($request->title) || $request->title == null) array_push($validation_errors, "要件は必須です。");
        if(!isset($request->content) || $request->content == null) array_push($validation_errors, "内容は必須です。");
        if(!isset($request->mail) && !PHPMailer::validateAddress($request->email, 'php')) array_push($validation_errors, "無効なメールアドレスです。");

        if(count($validation_errors) != 0){
            $exception = new \Exception();
            $exception->errors = $validation_errors;

            throw $exception;
        };

        return true;

    }

    public static function saveFormData($form_request)
    {
        if(!isset($_SESSION)) session_start();
        
        $form_data = array();

        if(isset($_SESSION['form_data'])) unset($_SESSION['form_data']);

        foreach ($form_request as $key => $value) {
            $form_data[$key] = $value ;
        }

        $_SESSION['form_data'] = $form_data;

    }

    public static function getFormData($key)
    {
        if(!isset($key) || empty($key)) throw new \Exception('keyパラメータは必須です');
        
        if(!isset($_SESSION)) session_start();

        $data = null;

        if(isset($_SESSION['form_data']) && isset($_SESSION['form_data'][$key])) {
            $data = $_SESSION['form_data'][$key];
            unset($_SESSION['form_data'][$key]);
        }

        return $data;
    }

    public static function convertHtmlSpecialChars($form_request)
    {
        if(!isset($form_request) || !is_array($form_request)) throw new \Exception('パラメータは配列である必要があります。');

        $form_post_data = (object) array();

        foreach ($form_request as $key => $value) {
            $form_post_data->{$key} = htmlspecialchars($value); 
        }

        return $form_post_data;
    }


}