<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

validate($_POST);

$page_content = include_template('add-lot.php', [ 'categories' => get_categories($con)]);
$layout = include_template('layout.php',['title' => 'Главная','is_auth' => $is_auth,
    'user_name' => $user_name, 'categories' => get_categories($con), 'contetnt' => $page_content]);



print($layout);