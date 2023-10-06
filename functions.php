<?php

const MINUTES_IN_HOUR = 60;
const SECOND_IN_MINUTE = 60;
const HOUR_IN_DAY = 24;




function format_price(int $num): string
{
    return number_format($num, thousands_separator: " ") . " ₽";
}


function get_dt_range(string $date): array
{
    date_default_timezone_set("Asia/Yekaterinburg");
    $minutes = floor(((strtotime($date) + (SECOND_IN_MINUTE * MINUTES_IN_HOUR * HOUR_IN_DAY)) - time()) / SECOND_IN_MINUTE);
    $hours = floor($minutes / MINUTES_IN_HOUR);
    $minutes = $minutes - ($hours * SECOND_IN_MINUTE);
    #добавляем одну минуту чтобы общее время в минутах было не 59, а 00
    return [str_pad($hours, 2, "0", STR_PAD_LEFT), str_pad($minutes + 1, 2, "0", STR_PAD_LEFT)];
}

function get_categories(mysqli $con): array
{
    $sql = "SELECT * FROM `Category`";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function get_lots(mysqli $con): array
{
    $sql = "SELECT Lot.Id,`NameLot`, `StartPrise`, `Image`, Category.NameCategory, `DateEnd`
            FROM `Lot`
                     INNER JOIN Category ON Lot.CategoryId = Category.Id
            WHERE `DateEnd` >= CURRENT_DATE

            ORDER BY `DateCreate` DESC";

    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}


function get_lot_by_id(mysqli $con, int $lot_id): array|null
{
    $sql = "SELECT 
    Lot.Id, 
    `NameLot`,
       `Detail`,
       `DateCreate`,
       `StartPrise`,
       `Image`,
       Category.NameCategory,
       `DateEnd`,
       `StepBet`
FROM `Lot`
         INNER JOIN Category ON Lot.CategoryId = Category.Id
WHERE Lot.Id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $select_res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_assoc($select_res);
    if (mysqli_num_rows($select_res) === 0) {
        http_response_code(404);
    }
    return $rows;
}

function add_lot(
    string $NameLot,
    string $Detail,
    string $Image,
    int $StartPrise,
    string $DateEnd,
    int $StepBet,
    int $AuthorId,
    int $CategoryId,
    mysqli $con
): int {
    $sql = "INSERT INTO Lot( NameLot, Detail, Image, StartPrise, DateEnd, StepBet, AuthorId, CategoryId)
            VALUES ( ?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssisiii', $NameLot, $Detail, $Image, $StartPrise, $DateEnd, $StepBet, $AuthorId, $CategoryId);
    mysqli_stmt_execute($stmt);
    return $con->insert_id;
}

function get_users(mysqli $con):array{
    $sql = "SELECT * FROM User";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function pr($val){
    $bt   = debug_backtrace();
    $file = file($bt[0]['file']);
    $src  = $file[$bt[0]['line']-1];
    $pat = '#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i';
    $var  = preg_replace ($pat, '$2', $src);
    echo '<script>console.log("'.trim($var).'='. 
     addslashes(json_encode($val,JSON_UNESCAPED_UNICODE)) .'")</script>'."\n";
}

function add_user(string $email, string $name, string $password, string $contact_info, mysqli $con){
    $temp_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO User(`Email`, `NameUser`, `PasswordUser`, `ContactInfo`)
            VALUES(?,?,?,?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $email, $name, $temp_password, $contact_info);
    mysqli_stmt_execute($stmt);
}

function get_user(string $email, mysqli $con):array|null{
    $sql = "SELECT * FROM User WHERE `Email` = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function getPostVal($name): string
{
    return $_POST[$name] ?? "";
}


