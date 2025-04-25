<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$items = getShopItems();
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корпоративная лавка - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 56px;
        }
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 1000;
            padding: 20px 0;
            overflow-x: hidden;
            overflow-y: auto;
            border-right: 1px solid #eee;
            background-color: #f8f9fa;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #333;
        }
        .sidebar .nav-link.active {
            color: #007bff;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        .shop-item-img {
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <?php include 'navbar.php'; ?>
    
    <!-- Боковая панель -->
    <?php include 'sidebar.php'; ?>

    <!-- Основное содержимое -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Корпоративная лавка</h2>
            <div>
                <span class="badge bg-primary">Ваши баллы: <?= $current_user['points'] ?></span>
                <?php if (isAdmin()): ?>
                    <a href="shop_add.php" class="btn btn-primary ms-2">
                        <i class="bi bi-plus-lg"></i> Добавить товар
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (empty($items)): ?>
            <div class="alert alert-info">Товаров пока нет</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="images/<?= sanitize($item['image']) ?>" class="card-img-top shop-item-img" alt="<?= sanitize($item['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= sanitize($item['name']) ?></h5>
                                <p class="card-text"><?= nl2br(sanitize($item['description'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success"><?= $item['price'] ?> баллов</span>
                                    <?php if ($item['stock'] > 0): ?>
                                        <span class="badge bg-info">В наличии: <?= $item['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Нет в наличии</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <?php if ($item['stock'] > 0 && $current_user['points'] >= $item['price']): ?>
                                    <form method="POST" action="purchase.php">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-primary w-100">Купить</button>
                                    </form>
                                <?php elseif ($item['stock'] > 0): ?>
                                    <button class="btn btn-secondary w-100" disabled>Недостаточно баллов</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>Нет в наличии</button>
                                <?php endif; ?>
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