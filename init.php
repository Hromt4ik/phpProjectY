<?php
session_start();

const HOST = 'localhost';
const LOGIN = 'srkcersj';
const PASSWORD = 'cempkV';
const DB_NAME = 'srkcersj_m3';

$con = mysqli_connect(HOST, LOGIN, PASSWORD, DB_NAME);
mysqli_set_charset($con, "utf8");
