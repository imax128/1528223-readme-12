<?php

session_start(); // открываем сессию
// если в сессии есть переменная user, значит пользователь уже авторизован
if (isset($_SESSION['user'])) {
    // и регистрироваться ему не нужно, значит редиректим его на стартовую зареганных юзеров
    header('Location: /feed.php');
} else {
    $is_auth = 0;
}