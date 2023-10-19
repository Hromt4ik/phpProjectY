<?php

const MINUTES_IN_HOUR = 60;
const SECOND_IN_MINUTE = 60;
const HOUR_IN_DAY = 24;

/**
* Форматирует Цену
* @param int $num Неформатированая цена
* 
* @return string форматированная цена
*/
function format_price(int $num): string
{
    return number_format($num, thousands_separator: " ") . " ₽";
}

/**
* Возварщает время оставшееся до завершения лота
* @param string $date дата завершения лота 
* 
* @return array [часы, минуты]
*/
function get_dt_range(string $date): array
{
    date_default_timezone_set("Asia/Yekaterinburg");
    $minutes = floor(((strtotime($date) + (SECOND_IN_MINUTE * MINUTES_IN_HOUR * HOUR_IN_DAY)) - time()) / SECOND_IN_MINUTE);
    $hours = floor($minutes / MINUTES_IN_HOUR);
    $minutes = $minutes - ($hours * SECOND_IN_MINUTE);
    #добавляем одну минуту чтобы общее время в минутах было не 59, а 00
    return [str_pad($hours, 2, "0", STR_PAD_LEFT), str_pad($minutes + 1, 2, "0", STR_PAD_LEFT)];
}

/**
* Получает список всех категорий
* @param mysqli $con подключение к базе
* 
* @return array список категорий
*/
function get_categories(mysqli $con): array
{
    $sql = "SELECT * FROM `Category`";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

/**
* Получает список всех лотов в порядке создания от последнего к первому
* @param mysqli $con подключение к базе
*
* @return array список лотов
*/
function get_lots(mysqli $con): array
{
    $sql = "SELECT Lot.Id,`NameLot`, `StartPrise`, `Image`, Category.NameCategory, `DateEnd`
            FROM `Lot`
                     INNER JOIN Category ON Lot.CategoryId = Category.Id
            WHERE `DateEnd` >= CURRENT_DATE

            ORDER BY `DateCreate` DESC";

    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

/**
* Возвращает Лот по Id или null если лот не найден 
* @param mysqli $con подключение к базе
* @param int $lot_id Id лота
*
* @return array лот 
*/
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

/**
* Добовляет лот
* @param string $NameLot имя лота
* @param string $Detail описание лота
* @param string $Image изображение лота
* @param int $StartPrise начальная цена
* @param string $DateEnd дата завершения
* @param int $StepBet шаг ставки
* @param int $AuthorId Id автора
* @param int $CategoryId Id категории 
* @param mysqli $con подключение к базе
*
* @return int Id лота
*/
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

/**
* Возвращает список пользователей
* @param mysqli $con подключение к базе
*
* @return array список пользователей 
*/
function get_users(mysqli $con):array{
    $sql = "SELECT * FROM User";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}


// function pr($val){
//     $bt   = debug_backtrace();
//     $file = file($bt[0]['file']);
//     $src  = $file[$bt[0]['line']-1];
//     $pat = '#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i';
//     $var  = preg_replace ($pat, '$2', $src);
//     echo '<script>console.log("'.trim($var).'='. 
//      addslashes(json_encode($val,JSON_UNESCAPED_UNICODE)) .'")</script>'."\n";
// }


/**
* Добовляет пользователя
* @param string $email Email пользователя
* @param string $name имя пользователя
* @param string $password пароль пользователя
* @param int $contact_info контактная информация
* @param mysqli $con подключение к базе
*/
function add_user(string $email, string $name, string $password, string $contact_info, mysqli $con): void{
    $temp_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO User(`Email`, `NameUser`, `PasswordUser`, `ContactInfo`)
            VALUES(?,?,?,?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $email, $name, $temp_password, $contact_info);
    mysqli_stmt_execute($stmt);
}


/**
* Возвращает пользователя
* @param mysqli $con подключение к базе
* @param string $email Email пользователя
*
* @return array пользователь
*/
function get_user(string $email, mysqli $con):array|null{
    $sql = "SELECT * FROM User WHERE `Email` = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}



/**
* Сохраняет заначение поля при POST запросе
* @param string $name имя поля
* 
* @return string значение поля
*/
function getPostVal($name): string
{
    return $_POST[$name] ?? "";
}


function search_lot_count(string $search_str, mysqli $con): int{
    $sql = "SELECT `Lot`.`Id` FROM `Lot` 
    INNER JOIN Category ON Lot.CategoryId = Category.Id WHERE `DateEnd` >= CURRENT_DATE 
AND MATCH(`NameLot`,Lot.Detail) AGAINST(?) ORDER BY `DateCreate` DESC;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $search_str);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function search_lot(string $search_str, mysqli $con, int $limit, int $offset): array|null
{
    $sql = "SELECT Lot.Id,`NameLot`, `StartPrise`, `Image`, Category.NameCategory, `DateEnd`, Detail 
FROM `Lot` 
    INNER JOIN Category ON Lot.CategoryId = Category.Id WHERE `DateEnd` >= CURRENT_DATE 
AND MATCH(`NameLot`,Lot.Detail) AGAINST(?) ORDER BY `DateCreate` DESC
    LIMIT ?
    OFFSET ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sii', $search_str, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        return null;
    }
}