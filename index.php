<?php


require_once('functions.php');
require_once('helpers.php');
require_once('init.php');



$page_content = include_template('main.php', [ 'categories' => get_categories($con), 'lots' => get_lots($con)]);
$layout = include_template('layout.php',['title' => 'Главная','is_auth' => $is_auth,
   'user_name' => $user_name, 'categories' => get_categories($con), 'contetnt' => $page_content]);

print($layout)

?>

