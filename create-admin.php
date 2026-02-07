<?php
require_once 'includes/db.php';

// Генерируем хеш ПРЯМО в PHP (гарантируем совместимость)
$hash = password_hash('cleanservic', PASSWORD_BCRYPT);
echo "Новый хеш для 'cleanservic':<br>";
echo "<code>$hash</code><br><br>";

// Вляем в БД
$stmt = $pdo->prepare("INSERT INTO users (fullname, phone, email, login, password) VALUES (?, ?, ?, ?, ?)");
$result = $stmt->execute([
    'Администратор',
    '+7(000)-000-00-00',
    'admin@cleanservice.ru',
    'adminka',
    $hash
]);

if ($result) {
    echo "✅ Админ успешно создан!";
} else {
    echo "❌ Ошибка: " . implode(', ', $stmt->errorInfo());
}
?>