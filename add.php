<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');


$errors = [];

    if (isset($_POST["lot-name"]) && empty($_POST['lot-name']))
    {
    }





function validateFilled($name) {
    if (empty($_POST[$name])) {
    return "Поле" . $name . "быть заполнено"; }
}

function check_correct_date($date): bool{

    return is_date_valid($_POST[$date]);
}

function getPostVal($name) {
    return $_POST[$name] ?? "";
}

$page_content = include_template('add-lot.php', [ 'categories' => get_categories($con), 'errors' => $errors]);
$layout = include_template('layout.php',['title' => 'Главная','is_auth' => $is_auth,
    'user_name' => $user_name, 'categories' => get_categories($con), 'contetnt' => $page_content]);



print($layout);