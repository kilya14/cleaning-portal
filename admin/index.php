<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

adminRedirect(); // Только админ

// Обработка смены статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $cancel_reason = trim($_POST['cancel_reason'] ?? '');
    $allowed = ['new', 'in_progress', 'completed', 'cancelled'];
    $redirect = 'index.php?status=' . ($_GET['status'] ?? 'all');

    if (in_array($status, $allowed)) {
        if ($status === 'cancelled' && empty($cancel_reason)) {
            header('Location: ' . $redirect . '&error=Укажите причину отмены');
            exit;
        }
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, cancel_reason = ? WHERE id = ?");
        $stmt->execute([$status, $status === 'cancelled' ? $cancel_reason : null, $order_id]);
        header('Location: ' . $redirect . '&saved=1');
    } else {
        header('Location: ' . $redirect);
    }
    exit;
}

// Загрузка всех заявок
$status_filter = $_GET['status'] ?? 'all';
$sql = "SELECT o.*, s.name AS service_name, u.fullname AS client_name, u.phone AS client_phone 
        FROM orders o 
        LEFT JOIN services s ON o.service_id = s.id 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$orders = $pdo->query($sql)->fetchAll();

$status_labels = [
    'new' => 'Новая',
    'in_progress' => 'В работе',
    'completed' => 'Выполнено',
    'cancelled' => 'Отменено'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Не Сам — Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .order-card { border-left: 4px solid #0d6efd; transition: transform 0.2s; }
        .order-card:hover { transform: translateX(5px); }
        .status-select { min-width: 140px; }
    </style>
</head>
<body>
<div class="container-fluid px-3 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Админ-панель</h1>
            <p class="text-muted mb-0">Управление заявками</p>
        </div>
        <a href="../dashboard.php" class="btn btn-outline-secondary">← В кабинет</a>
    </div>

    <!-- Фильтры -->
    <div class="mb-4">
        <div class="btn-group w-100" role="group">
            <a href="?status=all" class="btn btn-outline-secondary <?= $status_filter === 'all' ? 'active' : '' ?>">Все</a>
            <a href="?status=new" class="btn btn-outline-secondary <?= $status_filter === 'new' ? 'active' : '' ?>">Новые</a>
            <a href="?status=in_progress" class="btn btn-outline-secondary <?= $status_filter === 'in_progress' ? 'active' : '' ?>">В работе</a>
            <a href="?status=completed" class="btn btn-outline-secondary <?= $status_filter === 'completed' ? 'active' : '' ?>">Выполнено</a>
            <a href="?status=cancelled" class="btn btn-outline-secondary <?= $status_filter === 'cancelled' ? 'active' : '' ?>">Отменено</a>
        </div>
    </div>

    <?php if (!empty($_GET['saved'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Статус заявки успешно обновлён.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    <?php endif; ?>

    <h2 class="h5 mb-3 fw-bold">Все заявки</h2>

    <?php
    $filtered = $orders;
    if ($status_filter !== 'all') {
        $filtered = array_values(array_filter($orders, fn($o) => $o['status'] === $status_filter));
    }
    $per_page = 10;
    $total = count($filtered);
    $total_pages = max(1, (int)ceil($total / $per_page));
    $page = max(1, min((int)($_GET['page'] ?? 1), $total_pages));
    $offset = ($page - 1) * $per_page;
    $paginated = array_slice($filtered, $offset, $per_page);
    ?>

    <?php if (empty($filtered)): ?>
        <div class="no-orders">
            <div class="display-6 mb-3">📭</div>
            <p class="lead">Заявок нет</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($paginated as $order): ?>
                <div class="col-12">
                    <div class="card order-card shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                                        <?= $status_labels[$order['status']] ?? $order['status'] ?>
                                    </span>
                                    <small class="text-muted ms-2">#<?= $order['id'] ?></small>
                                </div>
                                <small class="text-muted"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></small>
                            </div>
                            <div class="mb-3">
                                <strong>👤 Клиент:</strong> <?= htmlspecialchars($order['client_name'] ?? '—') ?><br>
                                <strong>📍 Адрес:</strong> <?= htmlspecialchars($order['address']) ?><br>
                                <strong>📞 Телефон:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                                <strong>🧹 Услуга:</strong>
                                <?php
                                echo $order['service_name']
                                    ? htmlspecialchars($order['service_name'])
                                    : (!empty($order['custom_service']) ? 'Иная: ' . htmlspecialchars($order['custom_service']) : 'Не указана');
                                ?><br>
                                <strong>📅 Дата/время:</strong> <?= date('d.m.Y', strtotime($order['service_date'])) ?> / <?= htmlspecialchars($order['service_time']) ?><br>
                                <strong>💳 Оплата:</strong> <?= $order['payment_type'] === 'cash' ? 'Наличные' : 'Карта' ?>
                            </div>
                            <div class="border-top pt-3 mt-2">
                                <label class="form-label fw-bold mb-2 small text-uppercase text-muted">Изменить статус:</label>
                                <form method="POST" class="order-status-form">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <div class="d-flex gap-2 align-items-start flex-wrap mb-2">
                                        <select name="status" class="form-select form-select-sm status-select order-status-select">
                                            <?php foreach ($status_labels as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Сохранить</button>
                                    </div>
                                    <div class="cancel-reason-group mt-2" style="display: <?= $order['status'] === 'cancelled' ? 'block' : 'none' ?>;">
                                        <label class="form-label small">Причина отмены <span class="text-danger">*</span></label>
                                        <input type="text" name="cancel_reason" class="form-control form-control-sm cancel-reason-input" placeholder="Укажите причину отмены заявки" value="<?= htmlspecialchars($order['cancel_reason'] ?? '') ?>">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($total_pages > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0">
                <?php
                $base = '?status=' . urlencode($status_filter);
                for ($i = 1; $i <= $total_pages; $i++):
                    $active = $i === $page;
                ?>
                <li class="page-item <?= $active ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $base . ($i > 1 ? '&page=' . $i : '') ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.order-status-form').forEach(form => {
    const select = form.querySelector('.order-status-select');
    const reasonGroup = form.querySelector('.cancel-reason-group');
    const reasonInput = form.querySelector('.cancel-reason-input');

    function toggleReason() {
        if (select.value === 'cancelled') {
            reasonGroup.style.display = 'block';
            reasonInput.setAttribute('required', 'required');
        } else {
            reasonGroup.style.display = 'none';
            reasonInput.removeAttribute('required');
        }
    }
    select.addEventListener('change', toggleReason);
    toggleReason();
});
</script>
</body>
</html>
