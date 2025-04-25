<!-- news_item.php -->
<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news_item = getNewsItem($id);

if (!$news_item) {
    header('Location: news.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= sanitize($news_item['title']) ?> - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="news.php">Новости</a></li>
                <li class="breadcrumb-item active"><?= sanitize($news_item['title']) ?></li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <h2><?= sanitize($news_item['title']) ?></h2>
                <small class="text-muted">
                    Опубликовано: <?= date('d.m.Y H:i', strtotime($news_item['created_at'])) ?> | 
                    Автор: <?= sanitize($news_item['first_name']) ?> <?= sanitize($news_item['last_name']) ?>
                </small>
            </div>
            <div class="card-body">
                <div class="news-content">
                    <?= nl2br(sanitize($news_item['content'])) ?>
                </div>
            </div>
            <?php if (isAdmin()): ?>
            <div class="card-footer text-end">
                <a href="news_edit.php?id=<?= $news_item['id'] ?>" class="btn btn-sm btn-outline-primary">Редактировать</a>
                <a href="news_delete.php?id=<?= $news_item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить эту новость?')">Удалить</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>