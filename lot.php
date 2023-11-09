<?php
require_once('helpers.php');
require_once('functions.php');
require_once('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

$id = (isset($_GET['Id'])   && filter_var($_GET['Id'], FILTER_VALIDATE_INT)) ? $_GET['Id'] : -1;
$lot = get_lot_by_id($con, $id);

$errors = [];

if (http_response_code() === 404) {
    $page_content = include_template('404.php', ['nav' => $nav]);
} else {

    $bets_lot = bets_lot($id, $con);
    $count_bets = count($bets_lot);
    if ($count_bets === 0) {
        $min_bet = $lot['StartPrise'] + $lot['StepBet'];
        $price = $lot['StartPrise'];
    } else {
        $min_bet = $bets_lot[0]['Sum'] + $lot['StepBet'];
        $price = $bets_lot[0]['Sum'];
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['cost'])) {
            $errors['cost'] = 'Поле не заполнено';
        }
        if (!isset($errors['cost'])) {
            if (!filter_var($_POST['cost'], FILTER_VALIDATE_INT)) {
                $errors['cost'] = 'Ставка должна быть целым числом';
            } else {
                if (!isset($errors['cost']) && $_POST['cost'] < $min_bet) {
                    $errors['cost'] = 'Ставка должна быть больше, либо равна минимальной ставке';
                }
            }
            if (!isset($errors['cost'])) {
                add_bet($id, $_SESSION['AuthorId'],  intval($_POST['cost']), $con);
                header('Location: /lot.php?Id=' . $id);
                exit();
            }
        }
    }
    $page_content = include_template('detail_lot.php', [
        'nav' => $nav,
        'min_bet' => $min_bet,
        'errors' => $errors,
        'bet_lots' => $bets_lot,
        'lots' => $lot,
        'price' => $price,
        'count_bets' => $count_bets
    ]);
}
$name_title = isset($lot['NameLot']) ? $lot['NameLot'] : '404';
$layout = include_template('layout.php', ['title' => $name_title, 'nav' => $nav, 'contetnt' => $page_content]);

print($layout);
