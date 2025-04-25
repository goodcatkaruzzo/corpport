<!-- wiki_item.php -->
<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$wiki_item = getWikiArticle($id);

if (!$wiki_item) {
    header('Location: wiki.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= sanitize($wiki_item['title']) ?> - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="wiki.php">Wiki</a></li>
                <li class="breadcrumb-item active"><?= sanitize($wiki_item['title']) ?></li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <h2><?= sanitize($wiki_item['title']) ?></h2>
                <small class="text-muted">
                    Обновлено: <?= date('d.m.Y H:i', strtotime($wiki_item['updated_at'])) ?> | 
                    Автор: <?= sanitize($wiki_item['first_name']) ?> <?= sanitize($wiki_item['last_name']) ?>
                </small>
            </div>
            <div class="card-body">
                <div class="wiki-content">
                    <?= nl2br(sanitize($wiki_item['content'])) ?>
                </div>
            </div>
            <?php if (isAdmin()): ?>
            <div class="card-footer text-end">
                <a href="wiki_edit.php?id=<?= $wiki_item['id'] ?>" class="btn btn-sm btn-outline-primary">Редактировать</a>
                <a href="wiki_delete.php?id=<?= $wiki_item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить эту статью?')">Удалить</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>