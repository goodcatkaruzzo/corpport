<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $points = (int)$_POST['points'];
    
    // Обработка изображения
    $icon = '';
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['icon'], 'images/achievements/');
        if ($upload['success']) {
            $icon = $upload['filename'];
        } else {
            $errors[] = $upload['error'];
        }
    } else {
        $errors[] = 'Иконка обязательна';
    }
    
    if (empty($name)) $errors[] = 'Название обязательно';
    if (empty($description)) $errors[] = 'Описание обязательно';
    if ($points <= 0) $errors[] = 'Баллы должны быть положительными';
    
    if (empty($errors)) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO achievements (name, description, points, icon) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $points, $icon])) {
            $success = true;
        } else {
            $errors[] = 'Ошибка при добавлении достижения';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить достижение - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Добавить достижение</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">Достижение успешно добавлено!</div>
            <a href="admin.php" class="btn btn-primary">Вернуться в админку</a>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Название</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="points" class="form-label">Баллы</label>
                    <input type="number" class="form-control" id="points" name="points" min="1" required>
                </div>
                
                <div class="mb-3">
                    <label for="icon" class="form-label">Иконка</label>
                    <input type="file" class="form-control" id="icon" name="icon" accept="image/*" required>
                    <div class="form-text">Рекомендуемый размер: 100x100px. Максимальный размер 2MB.</div>
                </div>
                
                <button type="submit" class="btn btn-primary">Добавить достижение</button>
                <a href="admin.php" class="btn btn-secondary">Отмена</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>