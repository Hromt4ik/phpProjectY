<?php
require_once('helpers.php');
require_once('functions.php');
require_once('init.php');

const MAX_NAME = 30;
const MAX_EMAIL = 300;
const MAX_CONTACT = 300;


$category_list = get_categories($con);
$nav = include_template('categories.php', ['categories' => $category_list]);

if (isset($_SESSION['is_auth'])) {
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
    $required_fields = ['email', 'password', 'name', 'message'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = 'Поле не заполнено';
            }
        }

        if (!isset($errors['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'E-mail введен некорректно!';
            }

        }
        if (!isset($errors['name'])) {
            $len = strlen($_POST['name']);

            if ($len > MAX_NAME) {
                $errors['name'] = "Имя должно быть меньше" . MAX_NAME . " символов";
            }
        }

        if (!isset($errors['email'])) {
            $len = strlen($_POST['email']);

            if ($len > MAX_EMAIL) {
                $errors['email'] = "Email должен быть меньше " . MAX_EMAIL . " символов";
            }
        }

        if (!isset($errors['message'])) {
            $len = strlen($_POST['message']);

            if ($len > MAX_CONTACT) {
                $errors['message'] = "Контактная информация должна быть меньше " . MAX_CONTACT . " символов";
            }
        }
        if (!$errors) {
            $email = $_POST['email'];
            $name = $_POST['name'];
            $password = $_POST['password'];
            $message = $_POST['message'];
            $id_user = add_user($email, $name, $password, $message, $con);
            if($id_user){
                header('Location: /sign-in.php');
                exit();
            }
            $errors['email'] = 'Данный E-mail уже используеться!';    
        }
    }

    $page_content = include_template('sign-up.php', ['nav' => $nav, 'errors' => $errors]);
    $layout = print(include_template('layout.php', [
        'title' => 'Регистрация',
        'contetnt' => $page_content,
        'nav' => $nav
    ]));
}
