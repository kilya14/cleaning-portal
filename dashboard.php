<?php
require_once 'includes/auth.php';
authRedirect(); // Проверка авторизации
require_once 'includes/db.php';

// Загрузка истории заявок пользователя
$stmt = $pdo->prepare("SELECT o.*, s.name AS service_name FROM orders o 
                       LEFT JOIN services s ON o.service_id = s.id 
                       WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Не Сам — Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-new { background-color: #6c757d; color: white; }
        .status-in_progress { background-color: #ffc107; color: #212529; }
        .status-completed { background-color: #198754; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; text-decoration: line-through; }
        .order-card {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateX(5px);
        }
        .no-orders {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        @media (max-width: 390px) {
            .btn-group .btn {
                padding: 6px 10px;
                font-size: 0.85rem;
            }
            .order-card {
                margin: 8px 0;
            }
            .status-badge {
                font-size: 0.8rem;
                padding: 3px 8px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid px-3 py-3">
    <!-- Шапка -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Добро пожаловать, <?= htmlspecialchars($_SESSION['fullname']) ?>!</h1>
            <p class="text-muted mb-0">Ваш личный кабинет</p>
        </div>
        <div class="d-flex gap-2">
            <?php if (isAdmin()): ?>
                <a href="admin/index.php" class="btn btn-sm btn-warning">Админка</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Выйти</a>
        </div>
    </div>

    <!-- Кнопка создания заявки -->
    <div class="mb-4 text-center">
        <a href="create-order.php" class="btn btn-primary btn-lg w-100" style="max-width: 300px;">
            🧹 Создать заявку
        </a>
    </div>

    <!-- Фильтры -->
    <div class="mb-4">
        <div class="btn-group w-100" role="group">
            <a href="?status=all" class="btn btn-outline-secondary <?= !isset($_GET['status']) || $_GET['status'] === 'all' ? 'active' : '' ?>">Все</a>
            <a href="?status=new" class="btn btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'new' ? 'active' : '' ?>">Новые</a>
            <a href="?status=in_progress" class="btn btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'in_progress' ? 'active' : '' ?>">В работе</a>
            <a href="?status=completed" class="btn btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'completed' ? 'active' : '' ?>">Выполнено</a>
            <a href="?status=cancelled" class="btn btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'cancelled' ? 'active' : '' ?>">Отменено</a>
        </div>
    </div>

    <!-- История заявок -->
    <h2 class="h5 mb-3 fw-bold">История заявок</h2>
    
    <?php if (empty($orders)): ?>
        <div class="no-orders">
            <div class="display-6 mb-3">📭</div>
            <p class="lead">У вас пока нет заявок</p>
            <p class="text-muted">Создайте первую заявку на уборку!</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($orders as $order): 
                // Фильтрация по статусу
                $status_filter = $_GET['status'] ?? 'all';
                if ($status_filter !== 'all' && $order['status'] !== $status_filter) continue;
            ?>
                <div class="col-12">
                    <div class="card order-card shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?php 
                                            $status_labels = [
                                                'new' => 'Новая',
                                                'in_progress' => 'В работе',
                                                'completed' => 'Выполнено',
                                                'cancelled' => 'Отменено'
                                            ];
                                            echo $status_labels[$order['status']] ?? $order['status'];
                                        ?>
                                    </span>
                                    <?php if ($order['status'] === 'cancelled' && !empty($order['cancel_reason'])): ?>
                                        <div class="mt-1">
                                            <small class="text-danger">Причина: <?= htmlspecialchars($order['cancel_reason']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></small>
                            </div>
                            
                            <div class="mb-2">
                                <strong>📍 Адрес:</strong> <?= htmlspecialchars($order['address']) ?><br>
                                <strong>📞 Телефон:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                                <strong>🧹 Услуга:</strong> 
                                <?php 
                                    echo $order['service_name'] 
                                        ? htmlspecialchars($order['service_name']) 
                                        : (!empty($order['custom_service']) ? 'Иная: ' . htmlspecialchars($order['custom_service']) : 'Не указана');
                                ?><br>
                                <strong>📅 Дата/время:</strong> <?= date('d.m.Y', strtotime($order['service_date'])) ?> / <?= $order['service_time'] ?><br>
                                <strong>💳 Оплата:</strong> <?= $order['payment_type'] === 'cash' ? 'Наличные' : 'Карта' ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>