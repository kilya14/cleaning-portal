<?php
session_start();
require_once 'includes/db.php';

$login = 'adminka';
$password = 'cleanservic';

echo "<h3>Проверка входа для: '$login'</h3>";

$stmt = $pdo->prepare("SELECT id, fullname, password FROM users WHERE login = ? LIMIT 1");
$stmt->execute([$login]);
$user = $stmt->fetch();

if (!$user) {
    echo "❌ Пользователь '$login' не найден.<br>";
} else {
    echo "✅ Пользователь найден: " . htmlspecialchars($user['fullname']) . "<br>";
    $ok = password_verify($password, $user['password']);
    echo "Пароль верный? " . ($ok ? "ДА" : "НЕТ") . "<br>";
    if (!$ok) {
        echo "Хеш в БД: " . substr($user['password'], 0, 60) . "...<br>";
        echo "Попробуйте пересоздать админа через SQL (см. ниже).";
    }
}

// Дополнительно: проверим, есть ли другие пользователи
echo "<hr><h4>Все логины в БД:</h4>";
$stmt = $pdo->query("SELECT login FROM users");
$rows = $stmt->fetchAll();
foreach ($rows as $r) echo "- " . $r['login'] . "<br>";
?>