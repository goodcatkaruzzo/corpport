<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteNews($id);
    header('Location: news.php');
    exit;
}

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
    <title>Удалить новость - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Удалить новость</h2>
        
        <div class="alert alert-warning">
            Вы действительно хотите удалить новость "<?= sanitize($news_item['title']) ?>"?
        </div>
        
        <form method="POST">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="news_item.php?id=<?= $id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>