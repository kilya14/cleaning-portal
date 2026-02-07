<?php 
// Правильный путь для корневого файла
require_once 'includes/auth.php'; 
if (isAuth()) {
    // Перенаправление: админ → админка, пользователь → кабинет
    if ($_SESSION['login'] === 'adminka') {
        header('Location: admin/index.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Не Сам — Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container mt-4">
    <!-- Слайдер -->
    <div class="slider-wrapper">
        <div class="slider-container">
            <div class="slide active" style="background-image: url('assets/img/slider1.jpg');"></div>
            <div class="slide" style="background-image: url('assets/img/slider2.jpg');"></div>
            <div class="slide" style="background-image: url('assets/img/slider3.jpg');"></div>
            <button class="slider-btn prev" type="button" aria-label="Предыдущий">&lt;</button>
            <button class="slider-btn next" type="button" aria-label="Следующий">&gt;</button>
        </div>
        <div class="slider-dots"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <div class="card p-4 shadow">
                <h2 class="text-center mb-4">Вход в систему</h2>
                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                <?php if (!empty($_GET['message'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_GET['message']) ?></div>
                <?php endif; ?>
                <form action="login-process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Логин</label>
                        <input type="text" name="login" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
                <div class="text-center mt-3">
                    <a href="register.php">Нет аккаунта? Зарегистрируйтесь?</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/slider.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>