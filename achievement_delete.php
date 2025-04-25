<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем данные о достижении
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
$stmt->execute([$id]);
$achievement = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Удаляем иконку
    deleteImage('images/achievements/' . $achievement['icon']);
    
    // Удаляем связи с пользователями
    $stmt = $pdo->prepare("DELETE FROM user_achievements WHERE achievement_id = ?");
    $stmt->execute([$id]);
    
    // Удаляем само достижение
    $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: admin.php');
    exit;
}

if (!$achievement) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удалить достижение - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Удалить достижение</h2>
        
        <div class="alert alert-warning">
            Вы действительно хотите удалить достижение "<?= sanitize($achievement['name']) ?>"?
            <br><br>
            Это действие также удалит это достижение у всех пользователей, которые его получили.
        </div>
        
        <div class="card mb-3">
            <div class="card-body text-center">
                <img src="images/achievements/<?= sanitize($achievement['icon']) ?>" class="achievement-icon mb-3" alt="<?= sanitize($achievement['name']) ?>">
                <h5 class="card-title"><?= sanitize($achievement['name']) ?></h5>
                <p class="card-text"><?= sanitize($achievement['description']) ?></p>
                <span class="badge bg-primary"><?= $achievement['points'] ?> баллов</span>
            </div>
        </div>
        
        <form method="POST">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="admin.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>