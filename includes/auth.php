<?php
session_start();

function isAuth() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isAuth() && $_SESSION['login'] === 'adminka';
}

function authRedirect() {
    if (!isAuth()) {
        header('Location: index.php');
        exit();
    }
}

function adminRedirect() {
    if (!isAdmin()) {
        $base = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/admin') !== false) ? '../' : '';
        header('Location: ' . $base . 'dashboard.php');
        exit();
    }
}
?>