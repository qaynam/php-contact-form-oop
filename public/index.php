<?php

require __DIR__.'/../vendor/autoload.php';

use App\ContactForm;
use App\FlashMessage;

$flash_messages = FlashMessage::show();
$token = sha1(random_bytes(10));
if(!isset($_SESSION)) session_start();
$_SESSION['token'] = $token;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
        <div class="col-10 mx-auto">
            <h1 class="mb-5 w-100">お問い合わせ</h1>
           
            <?php if(count($flash_messages)){ ?>
                <?php if(isset($flash_messages['errors']) && count($flash_messages['errors'])){ 
                    $alert_class = 'alert-danger';
                    $messages = $flash_messages['errors'];
                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="me-2" fill="#842029" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                  </svg>
                    ';
                } else {
                    $alert_class = 'alert-success';
                    $messages = $flash_messages['success'];
                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="me-2" fill="#0f5132" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                  </svg>
                    ';
                }?>
                <div class="alert <?= $alert_class ?>" role="alert">
                    <?php foreach ($messages as $key => $message_content) { ?>
                        <div class="d-flex align-items-center mb-2">
                            <?= $icon ?>
                            <div><?= $message_content ?></div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
                    
            <form action="/contact/" method="POST">

                <div class="mb-3">
                    <label for="name" class="form-label">お名前</label>
                    <input type="name" name="name" value="<?= ContactForm::getFormData('name') ?>" id="name" class="form-control" placeholder="太郎 太郎">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">メールアドレス</label>
                    <input type="email" name="email" value="<?= ContactForm::getFormData('email') ?>" id="email" class="form-control" placeholder="メールアドレスを入力してください。" required>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">要件</label>
                    <input type="text" name="title" value="<?= ContactForm::getFormData('title') ?>" id="title" class="form-control"placeholder="用意をご自由に入力してください。">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">内容</label>
                    <textarea id="content" name="content" class="form-control"  placeholder="ご自由に入力してください。" rows="10"><?= ContactForm::getFormData('content') ?></textarea>
                </div>

                <input type="hidden" name="token" value="<?= $token ?>">

                <div class="mb-3">
                    <button type="submit" class="btn btn-lg btn-primary w-100">
                        <span>送信</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
    <script>

        const form = document.querySelector('form');
        const submit_btn = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', () => {
            submit_btn.disabled = true;
            submit_btn.innerHTML = `
                <span id="sending">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    送信中...
                </span>`
        });

    </script>
</body>
</html>