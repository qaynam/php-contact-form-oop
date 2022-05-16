<?php 

require __DIR__.'/../../vendor/autoload.php';

use App\ContactForm;
use App\FlashMessage;
use App\HttpResponse;
use App\MailerController;
use Dotenv\Dotenv;

//インスタスを作成
$contact_form = new ContactForm;
$flash_message = new FlashMessage;
$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->safeLoad();
$mail_controller = new MailerController($_ENV);

// POSTだけ許可する
HttpResponse::acceptMethods(['POST']);

try {

    //エスケープ
    $form_post_data = $contact_form->convertHtmlSpecialChars($_POST);

    //フォームのバリデーション
    $contact_form->validation($form_post_data);

    //2重送信を防ぐ
    if(!isset($form_post_data->token) || empty($form_post_data->token) || $form_post_data->token !== $_SESSION['token']) throw new Exception("2重送信されています、画面更新してから、もう一度お試しください。", 1);
    unset($_SESSION['token']);
    unset($form_post_data->token);

    //メールテンプレートを取得
    $mail_template_src = $mail_controller->getMailTemplate();
    $mail_notification_template_src = $mail_controller->getMailNotificaitonTemplate();

    //メールテンプレート作成
    $mail_template = $mail_controller->generateMailTemplate($mail_template_src, $form_post_data);
    $mail_notification_template = $mail_controller->generateMailTemplate($mail_notification_template_src, $form_post_data);

    //自動メール送信（お客用）
    $mail_controller->setAddress($form_post_data->email)
                    ->setSubject('お問い合わせありがとうございます')
                    ->setBody($mail_template)
                    ->sendMail();

    //自動メール送信（自分用）
    $mail_controller->clearAddress()
                    ->setSubject('お問い合わせがあります')
                    ->setAddress($_ENV['MAIL_NOTIFICATION_ADDRESS'])
                    ->setBody($mail_notification_template)
                    ->sendMail();

    //成功メッセージを保存
    $flash_message->store('success', ['お問い合わせありがとうございました！']);
    

} catch (\Exception $e) {

    //バリデーションエラーをフラッシュメッセージで返す
    if(isset($e->errors)){
        $errors = [];
        for ($i=1; $i <= count($e->errors); $i++) { 
            array_push($errors, $e->errors[$i - 1]);
        }
    
        $flash_message->store('errors', $errors);
    } else {
        $flash_message->store('errors', [$e->getMessage()]);
    }

    //データをセッションに保存する
    $contact_form->saveFormData($_POST);

} finally {

    //リダイレクトさせる
    HttpResponse::redirect('http://'.$_SERVER['HTTP_HOST'].'/');

}


