<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = getShopItem($id);

if (!$item) {
    header('Location: shop.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];
    
    if (empty($name)) $errors[] = 'Название обязательно';
    if (empty($description)) $errors[] = 'Описание обязательно';
    if ($price <= 0) $errors[] = 'Цена должна быть положительной';
    if ($stock < 0) $errors[] = 'Количество не может быть отрицательным';
    
    // Обработка изображения
    $image = $item['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image']);
        if ($upload['success']) {
            // Удаляем старое изображение
            deleteImage($image);
            $image = $upload['filename'];
        } else {
            $errors[] = $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if (updateShopItem($id, $name, $description, $price, $stock, $image)) {
            $success = true;
            $item = getShopItem($id);
        } else {
            $errors[] = 'Ошибка при обновлении товара';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать товар - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .current-image {
            max-width: 200px;
            max-height: 200px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Редактировать товар</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">Товар успешно обновлен!</div>
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
                <input type="text" class="form-control" id="name" name="name" value="<?= sanitize($item['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= sanitize($item['description']) ?></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="price" class="form-label">Цена (баллы)</label>
                    <input type="number" class="form-control" id="price" name="price" min="1" value="<?= $item['price'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="stock" class="form-label">Количество</label>
                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?= $item['stock'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Текущее изображение</label><br>
                <img src="images/<?= sanitize($item['image']) ?>" class="current-image" alt="<?= sanitize($item['name']) ?>">
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Новое изображение (оставьте пустым, чтобы не изменять)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Максимальный размер 2MB. Допустимые форматы: JPG, JPEG, PNG, GIF.</div>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="shop.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>