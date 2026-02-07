<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
$stmt->execute([$login]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['login'] = $user['login'];
    $_SESSION['phone'] = $user['phone'] ?? '';

    if ($user['login'] === 'adminka') {
        header('Location: admin/index.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
} else {
    header('Location: index.php?error=Неверный логин или пароль');
    exit();
}
?>