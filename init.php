<?php

$is_auth = rand(0, 1);
$user_name = 'Семён'; // укажите здесь ваше имя

// mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT);

const HOST = 'localhost';
const LOGIN = 'root';
const PASSWORD = '';
const DB_NAME = 'srkcersj_m3';

$con = mysqli_connect(HOST, LOGIN, PASSWORD, DB_NAME);
mysqli_set_charset($con, "utf8");
