<?php
require_once('helpers.php');
require_once('functions.php');
require_once('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

if (isset($_SESSION['is_auth']) && $_SESSION['is_auth']) {
    http_response_code(403);
    $page_content = include_template('403.php', ['nav' => $nav]);
    $layout = include_template('layout.php', [
        'title' => 'Главная',
        'nav' => $nav,
        'contetnt' => $page_content
    ]);
    print($layout);
} else {

    $errors = [];
    $required_fields = ['email', 'password'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = 'Поле не заполнено';
            }
        }
        if(!isset($errors['email'])){
            $user_get = get_user($_POST['email'], $con);
            if (!$user_get) {
                $errors['password'] = 'Вы ввели неверный email/пароль';   
            }else{
                if (!password_verify($_POST['password'], $user_get['PasswordUser'])) {
                    $errors['password'] = 'Вы ввели неверный email/пароль';
                }else{
                    $_SESSION['username'] = $user_get['NameUser'];
                    $_SESSION['is_auth'] = true;
                    $_SESSION['AuthorId'] = $user_get['Id'];
                    header('Location: /'); 
                    exit();
                }
            }  
        }
    }

    $page_content = include_template('login.php', ['nav' => $nav, 'errors' => $errors]);
    $layout = include_template('layout.php', [
        'title' => 'Вход',
        'contetnt' => $page_content,
        'nav' => $nav
    ]);
    
    print($layout);
}
