<?php


require_once('helpers.php');
require_once('functions.php');
require_once('init.php');

$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);
if (!isset($_SESSION['is_auth'])) {
    http_response_code(403);
    $detail = include_template('403.php', ['nav' => $nav]);
    print(include_template('layout.php', [
        'title' => '403',
        'nav' => $nav,
        'contetnt' => $detail
    ]));

} else {

    $bets_my = bets_my($_SESSION['AuthorId'], $con);

    for ($i = 0; $i < count($bets_my); $i++) {
        $bet = $bets_my[$i];
        $bet_win = bets_win($bet['LotId'], $con);

        $bets_my[$i]['is_win'] = isset($bet_win['Id']) && $bet_win['Id'] === $bet['Id'];
        $bets_my[$i]['contact_user'] = $bet_win['contact_user'] ?? "";
    }


    $my_bets = include_template('userBet.php', ['nav' => $nav, 'bet_lots' => $bets_my]);
    print(include_template('layout.php', [
        'title' => 'Мои ставки',
        'nav' => $nav,
        'contetnt' => $my_bets
    ]));
}
