<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

$Id = $_GET['Id'] ?? -1;
$lot = get_lot_by_id($con, $Id);

if (http_response_code() === 404) {
    $page_content = include_template('404.php', ['nav' => $nav]);
} else {
    $page_content = include_template('detail_lot.php', ['lot' => $lot, 'nav' => $nav]);
}
$layout = include_template('layout.php', ['title' => $lot['NameLot'], 'nav' => $nav, 'contetnt' => $page_content]);

print($layout);
