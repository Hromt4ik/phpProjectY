<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

function getPostVal($name) {
    return $_POST[$name] ?? "";
}

$errors = [];
$required_fields =['lot-name','category','message','lot-rate','lot-step','lot-date'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $errors[$field] = 'Поле не заполнено';
    }
}

if(!$errors['lot-date']) {
    if(!is_date_valid($_POST['lot-date']) || (time() >= strtotime($_POST['lot-date']))){
        $errors['lot-date'] = 'Дата не должна быть пустой или меньше текущей';
    }
}

if(!$errors['lot-rate']){
    if($_POST['lot-rate'] < 0){
        $errors['lot-rate'] = 'Цена должна быть больше 0';
    }
}

if(!$errors['lot-step']){
    if($_POST['lot-step'] < 0){
        $errors['lot-step'] = 'Стаквка должна быть больше 0';
    }
}


if(!$errors['lot-name']){
    $len = strlen($_POST['lot-name']);
    if ($len < 3 or $len > 75) {
        $errors['lot-name'] = "Значение должно быть от 3 до 75 символов";
    }
}

if(!$errors['message']){
    $len = strlen($_POST['message']);
    if ($len < 3 or $len > 500) {
        $errors['massage'] = "Значение должно быть от 3 до 500 символов";
    }
}



$page_content = include_template('add-lot.php', [ 'categories' => get_categories($con), 'errors' => $errors]);
$layout = include_template('layout.php',['title' => 'Главная','is_auth' => $is_auth,
    'user_name' => $user_name, 'categories' => get_categories($con), 'contetnt' => $page_content]);



print($layout);