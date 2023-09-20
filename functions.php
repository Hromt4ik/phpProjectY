<?php

function format_price(int $num): string
{
    return number_format($num, 0, ",", " ") . " ₽";
}


function get_dt_range(string $date): array
{
    $minutes_in_hour = 60;
    $seconds_in_minute = 60;

    date_default_timezone_set("Asia/Yekaterinburg");
    $minutes = floor((strtotime($date) - time()) / $seconds_in_minute);
    $hours = floor($minutes / $minutes_in_hour);
    $minutes = $minutes - ($hours * $minutes_in_hour);
    #добавляем одну минуту чтобы общее время в минутах было не 59, а 00
    return [str_pad($hours, 2, "0", STR_PAD_LEFT), str_pad($minutes + 1, 2, "0", STR_PAD_LEFT)];
}

function get_categories(mysqli $con): array
{
    $sql = "SELECT * FROM `Category`;";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function get_lots(mysqli $con): array
{
    $sql = "SELECT Lot.Id,`NameLot`, `StartPrise`, `Image`, Category.NameCategory, `DateEnd`
            FROM `Lot`
                     INNER JOIN Category ON Lot.CategoryId = Category.Id
            WHERE `DateEnd` > NOW()
            ORDER BY `DateCreate` DESC";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}


function get_lot_by_id(mysqli $con, int $lot_id): array|int
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
    $select_res  = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($select_res, MYSQLI_ASSOC);
    if(mysqli_num_rows($select_res) === 0) {
        return http_response_code(404);
    } else {
        return $rows[0];
    }
}

function filed_in(string|int $field): bool
{
 return isset($_POST[$field]);
}

function validate($fields)
{
    $error_codes = [];
    return $error_codes;
}
function check_correct_date($date): bool{

    return is_date_valid($_POST[$date]);
}

