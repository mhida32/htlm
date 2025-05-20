<?php
require 'config.php';

if (!isLoggedIn() || isAdmin()) {
    header('Location: login.php');
    exit;
}

// Получение заявлений пользователя
$stmt = $pdo->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Мои заявления | Нарушениям.Нет</title>
    <?php loadStyles(); ?>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-megaphone"></i> Нарушениям.Нет
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="create_report.php">
                            <i class="bi bi-plus-circle"></i> Создать заявление
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Мои заявления</h2>
            <a href="create_report.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Новое заявление
            </a>
        </div>
        
        <?php if ($reports): ?>
            <div class="row">
                <?php foreach ($reports as $report): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">№<?= $report['report_id'] ?></h5>
                                    <span class="status-badge status-<?= $report['status'] ?>">
                                        <?= $report['status'] === 'new' ? 'Новое' : 
                                            ($report['status'] === 'confirmed' ? 'Подтверждено' : 'Отклонено') ?>
                                    </span>
                                </div>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="bi bi-car-front"></i> <?= htmlspecialchars($report['car_number']) ?>
                                </h6>
                                <p class="card-text"><?= htmlspecialchars($report['description']) ?></p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> <?= date('d.m.Y H:i', strtotime($report['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-list-check" style="font-size: 3rem; color: #6c757d;"></i>
                <h3 class="mt-3">У вас пока нет заявлений</h3>
                <p class="text-muted">Нажмите кнопку "Новое заявление", чтобы создать первое обращение</p>
                <a href="create_report.php" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Создать заявление
                </a>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>