<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

const MAX_NAME_LENGHT = 75;
const MAX_DETAIL_LENGHT = 500;

// if($_SERVER['REQUEST_METHOD'] == 'POST')



$categories = get_categories($con);
$nav = include_template('categories.php', ['categories' => $categories]);

if (!(isset($_SESSION['is_auth']) && $_SESSION['is_auth'])) {
    http_response_code(403);
    $page_content = include_template('403.php', ['nav' => $nav]);
    $layout = include_template('layout.php', [
        'title' => 'Главная',
        'nav' => $nav,
        'contetnt' => $page_content
    ]);
    print($layout);
} else {



    $errors = [];
    $required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = 'Поле не заполнено';
            }
        }

        if (!isset($errors['category'])) {
            $category_id = [];
            foreach ($categories as $catigory) {
                array_push($category_id, $catigory['Id']);
            }

        }

        if (!isset($errors['lot-date'])) {
            if (!is_date_valid($_POST['lot-date']) || (time() >= strtotime($_POST['lot-date']))) {
                $errors['lot-date'] = 'Дата не должна быть пустой или меньше текущей';
            }
        }

        if (!isset($errors['lot-rate'])) {
            if (!filter_var($_POST['lot-rate'], FILTER_VALIDATE_INT)) {
                $errors['lot-rate'] = 'Цена должна быть натуральныим числом';
            } elseif ($_POST['lot-rate'] < 0) {
                $errors['lot-rate'] = 'Цена должна быть больше 0';
            }
        }

        if (!isset($errors['lot-step'])) {
            if (!filter_var($_POST['lot-step'], FILTER_VALIDATE_INT)) {
                $errors['lot-step'] = 'Ставка должна быть натуральныим числом';
            } elseif ($_POST['lot-step'] < 1) {
                $errors['lot-step'] = 'Стаквка должна быть больше 0';
            }
        }


        if (!isset($errors['lot-name'])) {
            $len = strlen($_POST['lot-name']);
            if ($len > MAX_NAME_LENGHT) {
                $errors['lot-name'] = 'Значение должно быть меньше ' . MAX_NAME_LENGHT . ' символов';
            }
        }

        if (!isset($errors['message'])) {
            $len = strlen($_POST['message']);
            if ($len > MAX_DETAIL_LENGHT) {
                $errors['message'] = 'Значение должно быть меньше ' . MAX_DETAIL_LENGHT . ' символов';
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
                    $file_url = '/uploads/' . $temp . $file_name;
                    move_uploaded_file($_FILES['image']['tmp_name'], $file_path . $temp . $file_name);
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
                '/uploads/' . $temp . $_FILES['image']['name'],
                $_POST['lot-rate'],
                $_POST['lot-date'],
                $_POST['lot-step'],
                $_SESSION['AuthorId'],
                $_POST['category'],
                $con
            );

            $lot = get_lot_by_id(
                $con,
                $addLot
            );

            if (http_response_code() === 404) {
                $page_content = include_template('404.php', ['nav' => $nav]);
                $layout = include_template('layout.php', [
                    'title' => 'Лот',
                    'nav' => $nav,
                    'contetnt' => $page_content]);
                    print($layout);
            } else {
                header('Location: /lot.php?Id=' . $addLot);
            }
            
        } else {
            $page_content = include_template('add-lot.php', ['errors' => $errors, 'nav' => $nav, 'categories' => $categories]);
            $layout = include_template('layout.php', [
                'title' => 'Добавление',
                'nav' => $nav,
                'contetnt' => $page_content
            ]);
            print($layout);
        }
    } else {


        $page_content = include_template('add-lot.php', ['errors' => $errors, 'nav' => $nav, 'categories' => $categories]);
        $layout = include_template('layout.php', [
            'title' => 'Добавление',
            'nav' => $nav,
            'contetnt' => $page_content
        ]);
        print($layout);
    }
}