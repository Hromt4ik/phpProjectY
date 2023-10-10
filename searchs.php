<?php
require_once ('helpers.php');
require_once ('functions.php');
require_once ('init.php');
$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);
if(!empty($_GET)){
    $searchs = trim($_GET['search']);
    $search_str = isset($_GET['search']) ? $_GET['search'] : "";
    $count_lot = search_lot_count($searchs, $con);

        $limit = 3;
        $count_page = ceil($count_lot/$limit);
        $curr_page =  isset($_GET['page']) ? $_GET['page'] : 1;
        if($curr_page <= 0){
            $curr_page = 1;
        }
        if($curr_page >= $count_page){
            $curr_page = $count_page;
        }
        $offset = ($curr_page - 1) * $limit;
    if($count_lot){
        $search_lot = search_lot($searchs, $con, $limit, $offset);

    }else{
        $search_lot = null;
    }

}


$main = include_template('search.php', [
    'nav' => $nav,
    'lots' => $search_lot,
    'search_str' => $search_str,
    'count_page' => $count_page,
    'curr_page' => $curr_page
]);

print($layout = include_template('layout.php', [
    'title' => 'Поиск',
    'nav' => $nav,
    'contetnt' => $main
]));
