<?php 
require_once 'includes/auth.php'; 
if (isAuth()) header('Location: dashboard.php');

$errors = $_SESSION['register_errors'] ?? [];
$data = $_SESSION['register_data'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_data']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Не Сам — Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <div class="card p-4 shadow">
                <h2 class="text-center mb-4">Регистрация</h2>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form id="registerForm" action="register-process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">ФИО</label>
                        <input type="text" name="fullname" class="form-control" 
                               value="<?= htmlspecialchars($data['fullname'] ?? '') ?>" 
                               required pattern="[А-Яа-яЁё\s]{2,100}" title="Только кириллица и пробелы">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($data['phone'] ?? '') ?>" 
                               required pattern="\+7\([0-9]{3}\)-[0-9]{3}-[0-9]{2}-[0-9]{2}" 
                               placeholder="+7(XXX)-XXX-XX-XX" maxlength="18">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Логин</label>
                        <input type="text" name="login" class="form-control" 
                               value="<?= htmlspecialchars($data['login'] ?? '') ?>" 
                               required minlength="3" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль (мин. 8 символов)</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Повторите пароль</label>
                        <input type="password" name="password2" class="form-control" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
                </form>
                <div class="text-center mt-3">
                    <a href="index.php">Уже есть аккаунт? Войти</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Маска телефона
document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
    e.target.value = !x[2] ? x[1] : '+' + x[1] + '(' + x[2] + ')' + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>