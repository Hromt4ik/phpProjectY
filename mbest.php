<?php

require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

$page_content = include_template('my-best.php', ['nav' => $nav, 'categories' => $categories]);
$layout = include_template('layout.php', [
    'title' => 'Добавление',
    'nav' => $nav,
    'contetnt' => $page_content
]);
print($layout);
