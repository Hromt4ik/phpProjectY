<?php
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

const MAX_NAME_LENGHT = 75;
const MAX_DETAIL_LENGHT = 500;
const MIN_TITLE_LENGHT = 3;

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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = 'Поле не заполнено';
            }
        }

        if (!isset($errors['lot-date'])) {
            if (!is_date_valid($_POST['lot-date']) || (time() >= strtotime($_POST['lot-date']))) {
                $errors['lot-date'] = 'Дата не должна быть пустой и меньше или равна текущей';
            }
        }

        $options = array(
            'options' => array(
                'min_range' => 1,
            ),
        );

        if (!isset($errors['lot-rate'])) {
            if (!filter_var($_POST['lot-rate'], FILTER_VALIDATE_INT, $options)) {
                $errors['lot-rate'] = 'Цена должна быть натуральныим числом и больше 0';
            }
        }

        if (!isset($errors['lot-step'])) {
            if (!filter_var($_POST['lot-step'], FILTER_VALIDATE_INT, $options)) {
                $errors['lot-step'] = 'Ставка должна быть натуральныим числом и больше 0';
            }
        }

        if (!isset($errors['lot-name'])) {
            $len = strlen($_POST['lot-name']);
            if ($len > MAX_NAME_LENGHT) {
                $errors['lot-name'] = 'Значение должно быть меньше ' . MAX_NAME_LENGHT . ' символов';
            }
            if ($len < MIN_TITLE_LENGHT) {
                $errors['lot-name'] = 'Значение должно быть меньше ' . MIN_TITLE_LENGHT . ' символов';
            }
        }

        if (!isset($errors['message'])) {
            $len = strlen($_POST['message']);
            if ($len > MAX_DETAIL_LENGHT) {
                $errors['message'] = 'Значение должно быть меньше ' . MAX_DETAIL_LENGHT . ' символов';
            }
            if ($len < MIN_TITLE_LENGHT) {
                $errors['message'] = 'Значение должно быть меньше ' . MIN_TITLE_LENGHT . ' символов';
            }
        }

        $temp = time();

        //Обработка изображения
        if ($_FILES) {
            if ($_FILES['image']['tmp_name'] !== "") {
                if (
                    (mime_content_type($_FILES['image']['tmp_name']) === "image/png") || (mime_content_type($_FILES['image']['tmp_name']) === "image/jpeg")
                    || (mime_content_type($_FILES['image']['tmp_name']) === "image/jpg")
                ) {
                    $file_name = $_FILES['image']['name'];
                    $file_path = __DIR__ . '/uploads/';
                    $file_url = '/uploads/' . $temp . $file_name;
                    move_uploaded_file($_FILES['image']['tmp_name'], $file_path . $temp . $file_name);
                } else {
                    $errors['image'] = "Картинка должна быть в формате *.png, *.jpeg или *.jpg";
                }
            } else {
                $errors['image'] = "Добавьте изображение";
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

            header('Location: /lot.php?Id=' . $addLot);
            exit();

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
