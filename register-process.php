<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$fullname = trim($_POST['fullname'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

$errors = [];

if (!preg_match('/^[\p{Cyrillic}\s\-]{2,100}$/u', $fullname)) {
    $errors[] = 'ФИО должно содержать только кириллицу, пробелы, дефисы (2–100 символов)';
}
if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
    $errors[] = 'Неверный формат телефона (+7(XXX)-XXX-XX-XX)';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Неверный формат email';
}
if (strlen($login) < 3 || strlen($login) > 50) {
    $errors[] = 'Логин должен быть от 3 до 50 символов';
}
if (strlen($password) < 8) {
    $errors[] = 'Пароль должен быть не менее 8 символов';
}
if ($password !== $password2) {
    $errors[] = 'Пароли не совпадают';
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? OR email = ? OR login = ?");
$stmt->execute([$phone, $email, $login]);
if ($stmt->fetch()) {
    $errors[] = 'Пользователь с такими данными уже существует';
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_data'] = compact('fullname', 'phone', 'email', 'login');
    header('Location: register.php');
    exit();
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (fullname, phone, email, login, password) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$fullname, $phone, $email, $login, $hashed_password]);

$_SESSION['success'] = 'Регистрация успешна! Войдите в систему.';
header('Location: index.php?message=Регистрация прошла успешно!');
exit();
?>