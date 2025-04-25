<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteWikiArticle($id);
    header('Location: wiki.php');
    exit;
}

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
    <title>Удалить статью Wiki - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Удалить статью Wiki</h2>
        
        <div class="alert alert-warning">
            Вы действительно хотите удалить статью "<?= sanitize($wiki_item['title']) ?>"?
        </div>
        
        <form method="POST">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="wiki_item.php?id=<?= $id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>