<?php
require_once ('helpers.php');
require_once ('functions.php');
require_once ('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php',['categories' => $categories]);

$errors = [];
$required_fields =['email','password'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
    if(!isset($errors['email']) && !isset($errors['password'])){
        $user_get = get_user($_POST['email'], $con);
        if($user_get){
            if(!password_verify($_POST['password'],$user_get['password'])){
                $errors['password'] = 'Пароль введен неверно';
            }else{
//                session_start();
//                $_SESSION['username'] = $user_get['name'];
//                $is_auth = 1;
//                pr($_SESSION['username']);
                $detail_lot = header('Location: /');
                print(include_template('layout.php', [
                    'is_auth' => $is_auth,
                    'user_name' => $user_name,
                    'title' => 'Вход',
                    'nav' => $nav,
                    'contetnt' => $detail_lot]));
            }
        }else{
            $errors['email'] = 'Еmail введен неверно';
        }

    }
}

function getPostVal($name):string{
    return $_POST[$name] ?? "";
}


$page_content = include_template('login.php',['nav' => $nav, 'errors' => $errors]);
$layout = print(include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Вход',
    'contetnt' => $page_content,
    'nav' => $nav
]));
