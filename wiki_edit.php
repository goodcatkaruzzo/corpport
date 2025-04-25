<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$wiki_item = getWikiArticle($id);

if (!$wiki_item) {
    header('Location: wiki.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    
    if (empty($title)) $errors[] = 'Заголовок обязателен';
    if (empty($content)) $errors[] = 'Содержание обязательно';
    
    if (empty($errors)) {
        if (updateWikiArticle($id, $title, $content)) {
            $success = true;
            $wiki_item = getWikiArticle($id);
        } else {
            $errors[] = 'Ошибка при обновлении статьи';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать статью Wiki - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Редактировать статью Wiki</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">Статья успешно обновлена!</div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= sanitize($wiki_item['title']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">Содержание</label>
                <textarea class="form-control" id="content" name="content" rows="15" required><?= sanitize($wiki_item['content']) ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="wiki_item.php?id=<?= $id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>