<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;

// Получаем данные пользователя
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $role = sanitize($_POST['role']);
    $points = (int)$_POST['points'];
    
    if (empty($first_name)) $errors[] = 'Имя обязательно';
    if (empty($last_name)) $errors[] = 'Фамилия обязательна';
    if (empty($email)) $errors[] = 'Email обязателен';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный формат email';
    if (empty($phone)) $errors[] = 'Телефон обязателен';
    if ($points < 0) $errors[] = 'Баллы не могут быть отрицательными';
    
    // Проверка уникальности email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Этот email уже используется другим пользователем';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, points = ? WHERE id = ?");
        if ($stmt->execute([$first_name, $last_name, $email, $phone, $role, $points, $id])) {
            $success = true;
            $user = $pdo->prepare("SELECT * FROM users WHERE id = ?")->execute([$id])->fetch();
            
            // Обновляем сессию, если редактируем текущего пользователя
            if ($id == $_SESSION['user_id']) {
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['role'] = $role;
                $_SESSION['points'] = $points;
            }
        } else {
            $errors[] = 'Ошибка при обновлении пользователя';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать пользователя - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Редактировать пользователя</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">Пользователь успешно обновлен!</div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= sanitize($user['first_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Фамилия</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= sanitize($user['last_name']) ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($user['email']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="<?= sanitize($user['phone']) ?>" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="role" class="form-label">Роль</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="points" class="form-label">Баллы</label>
                    <input type="number" class="form-control" id="points" name="points" min="0" value="<?= $user['points'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" value="<?= sanitize($user['username']) ?>" disabled>
                <div class="form-text">Имя пользователя нельзя изменить</div>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="admin.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>