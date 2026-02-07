<?php
require_once 'includes/auth.php';
authRedirect();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create-order.php');
    exit();
}

$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service_id = $_POST['service_id'] ?? null;
$custom_service = trim($_POST['custom_service'] ?? '');
$service_date = $_POST['service_date'] ?? '';
$service_time = $_POST['service_time'] ?? '';
$payment_type = $_POST['payment_type'] ?? '';

// Валидация на сервере
if (empty($address)) {
    header('Location: create-order.php?error=Адрес обязателен');
    exit();
}

if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
    header('Location: create-order.php?error=Неверный формат телефона. Пример: +7(915)-123-45-67');
    exit();
}

if (empty($service_date) || empty($service_time)) {
    header('Location: create-order.php?error=Укажите дату и время');
    exit();
}

if (empty($payment_type) || !in_array($payment_type, ['cash', 'card'])) {
    header('Location: create-order.php?error=Выберите способ оплаты');
    exit();
}

if (empty($service_id) && empty($custom_service)) {
    header('Location: create-order.php?error=Выберите услугу или укажите иную');
    exit();
}

if (!empty($service_id) && !is_numeric($service_id)) {
    header('Location: create-order.php?error=Некорректный выбор услуги');
    exit();
}

// Сохранение
$stmt = $pdo->prepare("INSERT INTO orders (user_id, address, phone, service_id, custom_service, service_date, service_time, payment_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $_SESSION['user_id'],
    $address,
    $phone,
    $service_id ?: null,
    $custom_service ?: null,
    $service_date,
    $service_time,
    $payment_type
]);

header('Location: dashboard.php?message=Заявка успешно создана!');
exit();
?>