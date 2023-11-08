<?php
require_once('helpers.php');
require_once('functions.php');
require_once('init.php');

const OFFSET_ONE = 1;
const LIMIT = 3;

$id = (isset($_GET['Id'])  && filter_var($_GET['Id'], FILTER_VALIDATE_INT)) ? $_GET['Id'] : -1;

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories, 'categoriesId'=> $id]);

$count_lot = cat_lot_count($id, $con);
$count_page = intval(ceil($count_lot / LIMIT));
$curr_page = intval((isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) ? $_GET['page'] : 1);

if($curr_page > $count_page || $curr_page < 0) {
    $curr_page = 1;
}

$offset = ($curr_page - OFFSET_ONE) * LIMIT;
$lot_list = lot_list_cat($id, $con, LIMIT, $offset);

$lots = include_template('lots.php', [
    'lots' => $lot_list,
    'categories' => $categories,
    'nav' => $nav,
    'count_page' => $count_page,
    'curr_page' => $curr_page
]);
print($layout = include_template('layout.php', [
    'title' => 'Главная',
    'nav' => $nav,
    'lots' => $lot_list,
    'contetnt' => $lots
]));
