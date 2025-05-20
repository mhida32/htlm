<?php
require 'config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $action = $_POST['action'];
    
    if (in_array($action, ['confirm', 'reject'])) {
        $status = $action === 'confirm' ? 'confirmed' : 'rejected';
        $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE report_id = ?");
        $stmt->execute([$status, $report_id]);
    }
}

// Получение всех заявлений
$stmt = $pdo->query("
    SELECT r.*, u.full_name 
    FROM reports r
    JOIN users u ON r.user_id = u.user_id
    ORDER BY r.created_at DESC
");
$reports = $stmt->fetchAll();

// Функция для фильтрации заявлений по статусу (заменяем стрелочную функцию)
function countByStatus($reports, $status) {
    $count = 0;
    foreach ($reports as $report) {
        if ($report['status'] === $status) {
            $count++;
        }
    }
    return $count;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Панель администратора | Нарушениям.Нет</title>
    <?php loadStyles(); ?>
    <style>
        .report-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        .report-card.new {
            border-left-color: #0dcaf0;
        }
        .report-card.confirmed {
            border-left-color: #198754;
        }
        .report-card.rejected {
            border-left-color: #dc3545;
        }
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .search-box {
            max-width: 300px;
        }
    </style>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-megaphone"></i> Нарушениям.Нет
            </a>
            <div class="d-flex align-items-center">
                <span class="badge bg-danger me-2">Админ</span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Выйти
                </a>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-speedometer2"></i> Панель администратора</h2>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> На главную
            </a>
        </div>

        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Всего заявлений</h5>
                                <h2 class="mb-0"><?= count($reports) ?></h2>
                            </div>
                            <i class="bi bi-list-check" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Подтверждено</h5>
                                <h2 class="mb-0"><?= countByStatus($reports, 'confirmed') ?></h2>
                            </div>
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Отклонено</h5>
                                <h2 class="mb-0"><?= countByStatus($reports, 'rejected') ?></h2>
                            </div>
                            <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список заявлений -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Все заявления</h5>
            </div>
            <div class="card-body">
                <?php if ($reports): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <th>Номер авто</th>
                                    <th>Описание</th>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr class="report-card <?= $report['status'] ?>">
                                        <td><?= $report['report_id'] ?></td>
                                        <td><?= htmlspecialchars($report['full_name']) ?></td>
                                        <td>
                                            <span class="badge bg-dark">
                                                <i class="bi bi-car-front"></i> <?= htmlspecialchars($report['car_number']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars(mb_substr($report['description'], 0, 50)) . (mb_strlen($report['description']) > 50 ? '...' : '') ?></td>
                                        <td><?= date('d.m.Y H:i', strtotime($report['created_at'])) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $report['status'] ?>">
                                                <?= $report['status'] === 'new' ? 'Новое' : 
                                                    ($report['status'] === 'confirmed' ? 'Подтверждено' : 'Отклонено') ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="report_id" value="<?= $report['report_id'] ?>">
                                                <button type="submit" name="action" value="confirm" class="btn btn-sm btn-success" <?= $report['status'] === 'confirmed' ? 'disabled' : '' ?>>
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" <?= $report['status'] === 'rejected' ? 'disabled' : '' ?>>
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                        <h3 class="mt-3">Нет заявлений</h3>
                        <p class="text-muted">Здесь будут отображаться заявления пользователей</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Футер -->
    <footer class="mt-5">
        <div class="container text-center py-3">
            <p class="mb-0">Административная панель &copy; <?= date('Y') ?> Нарушениям.Нет</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>