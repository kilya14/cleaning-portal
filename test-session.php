<?php
session_start();
$_SESSION['test'] = 'Работает!';
echo "Сессия установлена. <a href='check-session.php'>Проверить</a>";
?>