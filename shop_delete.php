<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = getShopItem($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Удаляем изображение
    deleteImage($item['image']);
    deleteShopItem($id);
    header('Location: shop.php');
    exit;
}

if (!$item) {
    header('Location: shop.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удалить товар - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Удалить товар</h2>
        
        <div class="alert alert-warning">
            Вы действительно хотите удалить товар "<?= sanitize($item['name']) ?>"?
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= sanitize($item['name']) ?></h5>
                <p class="card-text"><?= nl2br(sanitize($item['description'])) ?></p>
                <div class="d-flex justify-content-between">
                    <span class="badge bg-success"><?= $item['price'] ?> баллов</span>
                    <span class="badge bg-info">В наличии: <?= $item['stock'] ?></span>
                </div>
            </div>
        </div>
        
        <form method="POST">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="shop.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>