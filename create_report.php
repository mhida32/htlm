<?php
require 'config.php';

if (!isLoggedIn() || isAdmin()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_number = trim($_POST['car_number']);
    $description = trim($_POST['description']);
    
    if (empty($car_number) || empty($description)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif (mb_strlen($description) < 20) {
        $error = 'Описание должно содержать не менее 20 символов';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO reports (user_id, car_number, description) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $car_number, $description]);
            $success = 'Заявление успешно создано!';
            // Очищаем поля после успешной отправки
            $_POST['car_number'] = '';
            $_POST['description'] = '';
        } catch (PDOException $e) {
            $error = 'Ошибка при создании заявления: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Новое заявление | Нарушениям.Нет</title>
    <?php loadStyles(); ?>
    <style>
        .form-container {
            max-width: 700px;
        }
        .char-counter {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: right;
        }
        .preview-card {
            border: 1px dashed #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            background-color: #f8f9fa;
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
                <a href="reports.php" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Выйти
                </a>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <h2 class="mb-4"><i class="bi bi-plus-circle"></i> Новое заявление</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" id="reportForm">
                        <div class="mb-4">
                            <label for="car_number" class="form-label">Номер автомобиля</label>
                            <input type="text" class="form-control" id="car_number" name="car_number" 
                                   value="<?= isset($_POST['car_number']) ? htmlspecialchars($_POST['car_number']) : '' ?>" 
                                   placeholder="Например: А123БВ777" required>
                            <div class="form-text">Укажите государственный регистрационный номер</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание нарушения</label>
                            <textarea class="form-control" id="description" name="description" rows="5" 
                                      required minlength="20" oninput="updateCharCounter()"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                            <div class="char-counter">
                                <span id="charCount">0</span>/500 символов (минимум 20)
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirm" required>
                                <label class="form-check-label" for="confirm">
                                    Подтверждаю достоверность предоставленной информации
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-arrow-counterclockwise"></i> Очистить
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Отправить заявление
                            </button>
                        </div>
                    </form>
                    
                    <!-- Превью заявления -->
                    <div class="preview-card mt-4 d-none" id="previewCard">
                        <h5><i class="bi bi-eye"></i> Предпросмотр заявления</h5>
                        <hr>
                        <p><strong>Номер автомобиля:</strong> <span id="previewCarNumber"></span></p>
                        <p><strong>Описание нарушения:</strong></p>
                        <p id="previewDescription"></p>
                        <p class="text-muted"><small>Заявление будет проверено администратором</small></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Счетчик символов
        function updateCharCounter() {
            const textarea = document.getElementById('description');
            const charCount = document.getElementById('charCount');
            charCount.textContent = textarea.value.length;
            
            // Показываем/скрываем превью
            const previewCard = document.getElementById('previewCard');
            if (textarea.value.length > 0) {
                previewCard.classList.remove('d-none');
                document.getElementById('previewCarNumber').textContent = 
                    document.getElementById('car_number').value || '[не указан]';
                document.getElementById('previewDescription').textContent = 
                    textarea.value;
            } else {
                previewCard.classList.add('d-none');
            }
        }
        
        // Инициализация при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            updateCharCounter();
        });
    </script>
</body>
</html>