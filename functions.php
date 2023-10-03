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
    int $CategoryId,
    mysqli $con
): int {
    $AuthorId = 2;
    $sql = "INSERT INTO Lot( NameLot, Detail, Image, StartPrise, DateEnd, StepBet, AuthorId, CategoryId)
            VALUES ( ?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssisiii', $NameLot, $Detail, $Image, $StartPrise, $DateEnd, $StepBet, $AuthorId, $CategoryId);
    mysqli_stmt_execute($stmt);
    return $con->insert_id;
}
