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
 * Получает список всех не истекших лотов лотов в порядке создания от последнего к первому
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
function get_users(mysqli $con): array
{
    $sql = "SELECT * FROM User";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}



/**
 * Добовляет пользователя
 * @param string $email Email пользователя
 * @param string $name имя пользователя
 * @param string $password пароль пользователя
 * @param int $contact_info контактная информация
 * @param mysqli $con подключение к базе
 */
function add_user(string $email, string $name, string $password, string $contact_info, mysqli $con): void
{
    $temp_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO User(`Email`, `NameUser`, `PasswordUser`, `ContactInfo`)
            VALUES(?,?,?,?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $email, $name, $temp_password, $contact_info);
    mysqli_stmt_execute($stmt);
}


/**
 * Возвращает пользователя, null если пользователь не найден
 * @param mysqli $con подключение к базе
 * @param string $email Email пользователя
 *
 * @return array пользователь
 */
function get_user(string $email, mysqli $con): array|null
{
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


/**
 * Возвращает количетво найденых лотов соответствующих строке поиска
 * @param string $search_str строка поиска
 * 
 * @return int количетво лотов
 */
function search_lot_count(string $search_str, mysqli $con): int
{
    $sql = "SELECT `Lot`.`Id` FROM `Lot` 
    INNER JOIN Category ON Lot.CategoryId = Category.Id WHERE `DateEnd` >= CURRENT_DATE 
    AND MATCH(`NameLot`,Lot.Detail) AGAINST(?) ORDER BY `DateCreate` DESC;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $search_str);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}


/**
 * Возвращает Лоты по названию или категории если лоты не найден возвращает null
 * @param mysqli $con подключение к базе
 * @param string $search_str стрка поиска
 * @param int $limit количетсво возвращаемых записей
 * @param int $offset позиция с которой начинается выборка
 * 
 *
 * @return array лоты подходяшие под условия поиска
 */
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

/**
 * Возвращает Лоты по категории если лоты не найден возвращает null
 * @param mysqli $con подключение к базе
 * @param int $id_cat Id категории
 * @param int $limit количетсво возвращаемых записей
 * @param int $offset позиция с которой начинается выборка
 * 
 *
 * @return array лоты подходящей категории
 */
function lot_list_cat(int $id_cat, mysqli $con, int $limit, int $offset): array|null
{
    $sql = "SELECT Lot.Id,  Lot.NameLot as name_lot, `Image`, StartPrise, DateEnd, Category.NameCategory as cat_name, CategoryId FROM Lot
    INNER JOIN Category ON Lot.CategoryId = Category.id
    WHERE DateEnd >= CURRENT_DATE && CategoryId = ?  ORDER BY DateEnd DESC 
    LIMIT ?
    OFFSET ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'iii', $id_cat, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) === 0) {
        http_response_code(404);
    }

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}



/**
 * Возвращает количетво лотов в категории
 * @param mysqli $con подключение к базе
 * @param int $id_cat Id категории 
 *
 * @return int количество лотов в категории
 */
function cat_lot_count(int $id_cat, mysqli $con): int
{
    $sql = "SELECT COUNT(Lot.id) AS count_lot FROM Lot
    INNER JOIN Category ON Lot.CategoryId = Category.id
    WHERE DateEnd >= CURRENT_DATE && CategoryId = ?  ORDER BY DateEnd DESC ";
    $stmt = mysqli_prepare($con, $sql);
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_cat);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $result[0]['count_lot'];
}

/**
 * Возвращает ставки для лота или null если нетс ставок на этот лот
 * @param mysqli $con подключение к базе
 * @param int $id_lot Id лота 
 *
 * @return array ставки на лот 
 */
function bets_lot(int $id_lot, mysqli $con): array|null
{
    $sql = "SELECT Bet.Id, User.NameUser as user_name, Sum, Bet.DateCreate, LotId, UserId FROM Bet
    INNER JOIN User ON User.id = UserId
    WHERE LotId = ? ORDER BY Bet.DateCreate DESC;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_lot);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Возвращает ставки для лота или null если нетс ставок на этот лот
 * @param string $date дата и время ставки
 *
 * @return string время прошедшее с мосента ставки в нужном формате 
 */
function formate_date(string $date): string
{
    date_default_timezone_set('Asia/Yekaterinburg');
    $diff = time() - strtotime($date);

    $day = floor($diff / (SECOND_IN_MINUTE * MINUTES_IN_HOUR * HOUR_IN_DAY));
    $hour = floor($diff / (SECOND_IN_MINUTE * MINUTES_IN_HOUR));
    $minute = floor($diff / SECOND_IN_MINUTE);

    $sec = $diff;

    if ($day >= 1) {
        return $day . ' ' . get_noun_plural_form($day, 'день', 'дня', 'дней') . ' назад';
    }
    if ($hour >= 1) {
        return $hour . ' ' . get_noun_plural_form($hour, 'час', 'часа', 'часов') . ' назад';
    }
    if ($minute >= 1) {
        return $minute . ' ' . get_noun_plural_form($minute, 'минуту', 'минуты', 'минут') . ' назад';
    } 
    return $sec . ' ' . get_noun_plural_form($sec, 'секунду', 'секунды', 'секунд') . ' назад';
    
}

/**
 * Возвращает ставки пользователя  
 * @param mysqli $con подключение к базе
 * @param int $id_user Id пользователя 
 *
 * @return array список ставок на лот пользователя 
 */
function bets_my(int $id_user, mysqli $con): array|null
{
    $sql = "SELECT Bet.Id, User.NameUser as user_name, Lot.DateEnd as lot_date, Lot.Image as pic_lot, Sum, Bet.DateCreate, LotId, UserId, Lot.NameLot as lot_name, 
    Category.NameCategory as category_name FROM Bet
    INNER JOIN User ON User.Id = UserId
    INNER JOIN Lot ON Lot.Id = LotId
    INNER JOIN Category ON Category.id = CategoryId
    WHERE UserId = ? ORDER BY Bet.DateCreate DESC;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_user);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Возвращает победную ставку лота 
 * @param mysqli $con подключение к базе
 * @param int $id_lot Id лота 
 *
 * @return array ставка
 */
function bets_win(int $id_lot, mysqli $con): array|null
{
    $sql = "SELECT Bet.Id, winer.NameUser as user_name, Author.ContactInfo as contact_user, Sum, Bet.DateCreate, LotId, UserId FROM Bet 
    INNER JOIN User As winer ON winer.Id = UserId 
    INNER JOIN Lot ON Bet.LotId = Lot.Id
    INNER JOIN User as Author on Author.Id = Lot.AuthorId
    WHERE LotId = ? ORDER BY Bet.DateCreate DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_lot);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_assoc($res);
    return $rows;
}

/**
 * Добавляет ставку
 * @param mysqli $con подключение к базе
 * @param int $lot_id Id лота 
 * @param int $user_id Id пользователя 
 * @param int $cost сумма ставки
 */
function add_bet(int $lot_id, int $user_id, int $cost, mysqli $con): void
{
    $sql = "INSERT INTO Bet(`Sum`, LotId, UserId)
            VALUES(?,?,?);";
    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_bind_param($stmt, 'iii', $cost, $lot_id, $user_id);

    mysqli_stmt_execute($stmt);
}

/**
 * Добавляет победителя лоту
 * @param mysqli $con подключение к базе
 * @param int $lot_id Id лота 
 * @param int $bet_id Id ставки 
 */
function add_winer(int $lot_id, int $bet_id, mysqli $con): void
{
    $sql = 'UPDATE `Lot` SET `WinerId`= ? WHERE Id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $bet_id, $lot_id);
    mysqli_stmt_execute($stmt);

}

/**
 * Возвращает все лоты
 * @param mysqli $con подключение к базе
 *
 * @return array список лотов
 */
function get_all_lots(mysqli $con): array
{
    $sql = "SELECT Lot.Id, Lot.NameLot as name_lot, `Image`, StartPrise, DateEnd, Category.NameCategory as cat_name, WinerId FROM Lot
INNER JOIN Category ON Lot.CategoryId = Category.Id
WHERE DateEnd < CURRENT_DATE() AND WinerId IS NUll 
ORDER BY DateCreate DESC";

    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

/**
 * Возвращает определенное количетсво лотов с определенной позиции при поиске по пустой строке
 * @param mysqli $con подключение к базе
 * @param int $limit количетво записей
 * @param int $offset позиция с которой начинается выборка
 * 
 * @return array список лотов
 */
function search_all_lot(mysqli $con, int $limit, int $offset): array|null
{

    $sql = "SELECT Lot.Id,`NameLot`, `StartPrise`, `Image`, Category.NameCategory, `DateEnd`
    FROM `Lot`
             INNER JOIN Category ON Lot.CategoryId = Category.Id
    WHERE `DateEnd` >= CURRENT_DATE
    ORDER BY `DateCreate` DESC
    LIMIT ?
    OFFSET ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        return null;
    }
}

/**
 * Возвращает еоличетво лотов при поиске по апустой строке
 * @param mysqli $con подключение к базе
 * 
 * @return array список лотов
 */
function search_all_lot_count(mysqli $con): int
{
    $sql = "SELECT Lot.Id
    FROM `Lot`
             INNER JOIN Category ON Lot.CategoryId = Category.Id
    WHERE `DateEnd` >= CURRENT_DATE

    ORDER BY `DateCreate` DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}
