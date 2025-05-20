<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Нарушениям.Нет</title>
    <?php loadStyles(); ?>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-megaphone"></i> Нарушениям.Нет
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php">
                                    <i class="bi bi-speedometer2"></i> Панель администратора
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="reports.php">
                                    <i class="bi bi-list-check"></i> Мои заявления
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="create_report.php">
                                    <i class="bi bi-plus-circle"></i> Создать заявление
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Выйти
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Вход
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="bi bi-person-plus"></i> Регистрация
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 mb-4">Портал сознательных граждан</h1>
                <p class="lead mb-4">
                    Помогаем полиции в фиксации нарушений правил дорожного движения.
                    Ваша бдительность делает наши дороги безопаснее!
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="register.php" class="btn btn-primary btn-lg px-4 gap-3">
                            <i class="bi bi-person-plus"></i> Зарегистрироваться
                        </a>
                        <a href="login.php" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right"></i> Войти
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h3 class="card-title">Добро пожаловать, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h3>
                            <p class="card-text">
                                <?php if (isAdmin()): ?>
                                    Вы вошли как администратор. Вы можете просматривать и обрабатывать все заявления.
                                <?php else: ?>
                                    Вы можете создавать новые заявления о нарушениях ПДД или просматривать свои предыдущие обращения.
                                <?php endif; ?>
                            </p>
                            <a href="<?= isAdmin() ? 'admin.php' : 'reports.php' ?>" class="btn btn-primary">
                                <?= isAdmin() ? 'Перейти в панель администратора' : 'Мои заявления' ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Футер -->
    <footer class="mt-5">
        <div class="container text-center">
            <p class="mb-0">© 2025 Портал «Нарушениям.Нет». Все права защищены.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>