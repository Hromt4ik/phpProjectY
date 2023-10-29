<?php
require_once ('helpers.php');
require_once ('functions.php');
require_once ('init.php');


$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);


$Id = $_GET['Id'] ?? -1;
$lot = get_lot_by_id($con, $Id);;

$errors = [];

if (http_response_code() === 404) {
    $page_content = include_template('404.php', ['nav' => $nav]);
} else {

        $bets_lot = bets_lot($Id, $con); 
        $count_bets = count($bets_lot);
        if(!$count_bets){
            $min_bet = (!empty($lot['StartPrise']) && !empty($lot['StepBet'])) ? $lot['StartPrise'] + $lot['StepBet'] :  0;
        }else{
            $min_bet = $bets_lot[0]['Sum'];
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (empty($_POST['cost'])) {
                $errors['cost'] = 'Поле не заполнено';
            }
            if(!isset($errors['cost'])){
                if(!filter_var($_POST['cost'], FILTER_VALIDATE_INT)){
                    $errors['cost'] = 'Ставка должна быть целым числом';
                }else{
                    if(!isset($errors['cost']) && $_POST['cost'] < $min_bet) {
                        $errors['cost'] = 'Ставка должна быть больше, либо равна минимальной ставке';
                    }
                }
                if(!isset($errors['cost'])){
                    $addBet = bet_add($Id, $_SESSION['AuthorId'], (int)$_POST['cost'], $con);
                    $page_content = header('Location: /lot.php?Id=' . $Id);
                }
            }
        }
            $page_content = include_template('detail_lot.php', ['nav' => $nav, 'min_bet' => $min_bet,'errors' => $errors, 'bet_lots' => $bets_lot,
            'lots' => $lot, 'count_bets'=> $count_bets]);
        
        
    }



    $name_title = isset($lot['NameLot']) ? $lot['NameLot'] : '404';
    $layout = include_template('layout.php', ['title' => $name_title, 'nav' => $nav, 'contetnt' => $page_content]);
    
    print($layout);