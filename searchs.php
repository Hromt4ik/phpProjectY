<?php
require_once('helpers.php');
require_once('functions.php');
require_once('init.php');

const LIMIT_ON_PAGE = 3;

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

if (isset($_GET['search']) && !empty($_GET['search'])) {

    $searchs = trim($_GET['search']);
    $search_str = isset($_GET['search']) ? $_GET['search'] : "";

    $count_lot = search_lot_count($searchs, $con);
    
    $count_page = intval(ceil($count_lot / LIMIT_ON_PAGE));
    $curr_page = intval((isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) ? $_GET['page'] : 1);
    
    if($curr_page > $count_page || $curr_page < 0 || is_string($curr_page)) {
        $curr_page = 1;
    }
    
    $offset = ($curr_page - 1) * LIMIT_ON_PAGE;

    if ($count_lot) {
        $search_lot = search_lot($searchs, $con, LIMIT_ON_PAGE, $offset);
    } else {
        $search_lot = null;
    }

} else {

    $count_lot = search_all_lot_count($con);
    $count_page = intval(ceil($count_lot / LIMIT_ON_PAGE));
    $curr_page = intval((isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) ? $_GET['page'] : 1);

    if($curr_page > $count_page || $curr_page < 0 || is_string($curr_page)) {
        $curr_page = 1;
    }

    $offset = ($curr_page - 1) * LIMIT_ON_PAGE;
    if ($count_lot) {
        $search_lot = search_all_lot($con, LIMIT_ON_PAGE, $offset);
    } else {
        $search_lot = null;
    }

}

$main = include_template('search.php', [
    'nav' => $nav,
    'lots' => $search_lot,
    'search_str' => $search_str ?? "",
    'count_page' => $count_page,
    'curr_page' => $curr_page
]);

print($layout = include_template('layout.php', [
    'title' => 'Поиск',
    'nav' => $nav,
    'contetnt' => $main
]));
