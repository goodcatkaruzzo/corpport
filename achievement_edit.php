<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$achievement = null;

// Получаем данные о достижении
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
$stmt->execute([$id]);
$achievement = $stmt->fetch();

if (!$achievement) {
    header('Location: admin.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $points = (int)$_POST['points'];
    
    $icon = $achievement['icon'];
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['icon'], 'images/achievements/');
        if ($upload['success']) {
            // Удаляем старое изображение
            deleteImage('images/achievements/' . $icon);
            $icon = $upload['filename'];
        } else {
            $errors[] = $upload['error'];
        }
    }
    
    if (empty($name)) $errors[] = 'Название обязательно';
    if (empty($description)) $errors[] = 'Описание обязательно';
    if ($points <= 0) $errors[] = 'Баллы должны быть положительными';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE achievements SET name = ?, description = ?, points = ?, icon = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $points, $icon, $id])) {
            $success = true;
            $achievement = $pdo->prepare("SELECT * FROM achievements WHERE id = ?")->execute([$id])->fetch();
        } else {
            $errors[] = 'Ошибка при обновлении достижения';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать достижение - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .current-icon {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Редактировать достижение</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">Достижение успешно обновлено!</div>
        <?php endif; ?>
        
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
                <input type="text" class="form-control" id="name" name="name" value="<?= sanitize($achievement['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= sanitize($achievement['description']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="points" class="form-label">Баллы</label>
                <input type="number" class="form-control" id="points" name="points" min="1" value="<?= $achievement['points'] ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Текущая иконка</label><br>
                <img src="images/achievements/<?= sanitize($achievement['icon']) ?>" class="current-icon" alt="<?= sanitize($achievement['name']) ?>">
            </div>
            
            <div class="mb-3">
                <label for="icon" class="form-label">Новая иконка (оставьте пустым, чтобы не изменять)</label>
                <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                <div class="form-text">Рекомендуемый размер: 100x100px. Максимальный размер 2MB.</div>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="admin.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>