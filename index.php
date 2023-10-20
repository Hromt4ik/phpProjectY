<?php


require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

$page_content = include_template('main.php', ['categories' => $categories, 'lots' => get_lots($con)]);
$layout = include_template('layout.php', ['title' => 'Главная', 'nav' => $nav, 'contetnt' => $page_content]);

print($layout);
