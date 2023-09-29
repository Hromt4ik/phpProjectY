<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

function getPostVal($name)
{
    return $_POST[$name] ?? "";
}

$errors = [];
$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        $errors[$field] = 'Поле не заполнено';
    }
}

if (!isset($errors['lot-date'])) {
    if (!is_date_valid($_POST['lot-date']) || (time() >= strtotime($_POST['lot-date']))) {
        $errors['lot-date'] = 'Дата не должна быть пустой или меньше текущей';
    }
}

if (!isset($errors['lot-rate'])) {
    if (!is_numeric($_POST['lot-rate'])) {
        $errors['lot-rate'] = 'Цена должна быть натуральныим числом';
    } elseif ($_POST['lot-rate'] < 0) {
        $errors['lot-rate'] = 'Цена должна быть больше 0';
    }
}

if (!isset($errors['lot-step'])) {
    if (!is_numeric($_POST['lot-step'])) {
        $errors['lot-step'] = 'Ставка должна быть натуральныим числом';
    } elseif ($_POST['lot-step'] <= 1) {
        $errors['lot-step'] = 'Стаквка должна быть больше 1';
    }
}


if (!isset($errors['lot-name'])) {
    $len = strlen($_POST['lot-name']);
    if ($len < 3 or $len > 75) {
        $errors['lot-name'] = "Значение должно быть от 3 до 75 символов";
    }
}

if (!isset($errors['message'])) {
    $len = strlen($_POST['message']);
    if ($len < 3 or $len > 500) {
        $errors['message'] = "Значение должно быть от 3 до 500 символов";
    }
}

$temp = time();

//Обработка изображения
if ($_FILES) {
    if ($_FILES['image']['tmp_name'] !== "") {
        if (
            (mime_content_type($_FILES['image']['tmp_name']) == "image/png") || (mime_content_type($_FILES['image']['tmp_name']) == "image/jpeg")
            || (mime_content_type($_FILES['image']['tmp_name']) == "image/jpg")
        ) {
            $file_name = $_FILES['image']['name'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/'.$temp.$file_name ;
            move_uploaded_file($_FILES['image']['tmp_name'], $file_path . $temp.$file_name);
        } else {
            $errors['image'] = "Картинка должна быть в формате *.png, *.jpeg или *.jpg";
        }
    } else {
        $errors['image'] = "Добавте изображение";
    }
}
if (empty($errors)) {


    $addLot = add_lot(
        $_POST['lot-name'],
        $_POST['message'],
        '/uploads/'.$temp.$_FILES['image']['name'],
        $_POST['lot-rate'],
        $_POST['lot-date'],
        $_POST['lot-step'],
        $_POST['category'],
        $con
    );

    $lot = get_lot_by_id($con, $addLot
    );

    foreach ($required_fields as $field) {
    [$field] = ""; 
    }

    if (http_response_code() === 404) {
        $page_content = include_template('404.php', ['categories' => get_categories($con)]);
    } else {
        $page_content = header('Location: /lot.php?Id=' . $addLot);
    }
    $layout = include_template('layout.php', [
        'title' => 'Главная',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'categories' => get_categories($con),
        'contetnt' => $page_content
    ]);

    print($layout);

} else {
    $page_content = include_template('add-lot.php', ['categories' => get_categories($con), 'errors' => $errors]);
    $layout = include_template('layout.php', [
        'title' => 'Главная',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'categories' => get_categories($con),
        'contetnt' => $page_content
    ]);
    print($layout);
}