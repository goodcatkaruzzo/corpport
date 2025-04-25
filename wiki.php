<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$articles = getWikiArticles();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiki - Корпоративный портал</title>
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
            <h2>Корпоративная Wiki</h2>
            <?php if (isAdmin()): ?>
                <a href="wiki_add.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Добавить статью
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($articles)): ?>
            <div class="alert alert-info">Статей пока нет</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($articles as $article): ?>
                    <a href="wiki_item.php?id=<?= $article['id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?= sanitize($article['title']) ?></h5>
                            <small class="text-muted">Обновлено: <?= date('d.m.Y H:i', strtotime($article['updated_at'])) ?></small>
                        </div>
                        <p class="mb-1"><?= nl2br(sanitize(substr($article['content'], 0, 200))) ?>...</p>
                        <small class="text-muted">Автор: <?= sanitize($article['first_name']) ?> <?= sanitize($article['last_name']) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>