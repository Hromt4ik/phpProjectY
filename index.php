<?php


require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

$lot_list_all = get_all_lots($con);

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

foreach ($lot_list_all as $lot) {
    $bet_list = bets_win($lot['Id'], $con);

    if ($bet_list !== null) {
        add_winer($lot['Id'], $bet_list['UserId'], $con);
    }
}

$page_content = include_template('main.php', ['categories' => $categories, 'lots' => get_lots($con)]);
$layout = include_template('layout.php', ['title' => 'Главная', 'nav' => $nav, 'contetnt' => $page_content]);

print($layout);
