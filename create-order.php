<?php
require_once 'includes/auth.php';
authRedirect();
require_once 'includes/db.php';

// Загрузка списка услуг
$services = $pdo->query("SELECT * FROM services")->fetchAll();

// Обработка ошибки из URL
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Не Сам — Новая заявка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media (max-width: 390px) {
            .card {
                border-radius: 12px !important;
                margin: 8px;
            }
            .btn {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
            .form-label {
                font-size: 0.95rem;
            }
        }
        #otherServiceGroup {
            display: none;
        }
    </style>
</head>
<body>
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <div class="card p-4 shadow">
                <h2 class="text-center mb-4">Новая заявка на уборку</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="save-order.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Адрес</label>
                        <input type="text" name="address" class="form-control" required placeholder="Улица, дом, квартира">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="tel" 
                               name="phone" 
                               class="form-control" 
                               value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" 
                               required 
                               placeholder="+7(XXX)-XXX-XX-XX"
                               maxlength="18">
                        <div class="form-text">Пример: +7(915)-123-45-67</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Выберите услугу</label>
                        <select name="service_id" class="form-select" required>
                            <option value="">— Выберите —</option>
                            <?php foreach ($services as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="otherServiceCheck">
                        <label class="form-check-label" for="otherServiceCheck">Иная услуга</label>
                    </div>

                    <div id="otherServiceGroup" class="mb-3">
                        <label class="form-label">Опишите услугу</label>
                        <textarea name="custom_service" class="form-control" rows="2" placeholder="Например: мытьё окон на балконе" disabled></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Дата выполнения</label>
                        <input type="date" name="service_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Время</label>
                        <input type="time" name="service_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Способ оплаты</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="payment_type" value="cash" class="form-check-input" required>
                                <label class="form-check-label">Наличные</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="payment_type" value="card" class="form-check-input" required>
                                <label class="form-check-label">Карта</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Отправить заявку</button>
                    <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">← Назад в кабинет</a>
                </form>
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

// Логика "Иная услуга"
document.getElementById('otherServiceCheck').addEventListener('change', function() {
    const group = document.getElementById('otherServiceGroup');
    const select = document.querySelector('select[name="service_id"]');
    const textarea = document.querySelector('textarea[name="custom_service"]');
    if (this.checked) {
        group.style.display = 'block';
        select.removeAttribute('required');
        select.value = '';
        select.disabled = true;
        textarea.disabled = false;
        textarea.setAttribute('required', 'required');
    } else {
        group.style.display = 'none';
        select.setAttribute('required', 'required');
        select.disabled = false;
        textarea.disabled = true;
        textarea.removeAttribute('required');
        textarea.value = '';
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>