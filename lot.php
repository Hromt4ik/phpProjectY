<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');


if (!isset($_GET['Id']) || http_response_code() === 404) {

    $page_content = include_template('404.php', ['categories' => get_categories($con)]);
}

$lot = get_lot_by_id($con, $_GET['Id']);


if (http_response_code() === 404) {
    $page_content = include_template('404.php', ['categories' => get_categories($con)]);
} else {
    $page_content = include_template('detail_lot.php', ['lot' => $lot, 'categories' => get_categories($con)]);
}
$layout = include_template('layout.php', ['title' => 'Главная', 'is_auth' => $is_auth,
    'user_name' => $user_name, 'categories' => get_categories($con), 'contetnt' => $page_content]);

print($layout);