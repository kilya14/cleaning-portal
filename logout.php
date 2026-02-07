<?php
session_start();
session_destroy();
header('Location: index.php?message=Вы вышли из системы');
exit();
?>