<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    
    // Валидация
    if (empty($username)) $errors[] = 'Имя пользователя обязательно';
    if (empty($password)) $errors[] = 'Пароль обязателен';
    if ($password !== $password_confirm) $errors[] = 'Пароли не совпадают';
    if (empty($first_name)) $errors[] = 'Имя обязательно';
    if (empty($last_name)) $errors[] = 'Фамилия обязательна';
    if (empty($email)) $errors[] = 'Email обязателен';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный формат email';
    if (empty($phone)) $errors[] = 'Телефон обязателен';
    
    if (empty($errors)) {
        if (registerUser($username, $password, $first_name, $last_name, $email, $phone)) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $errors[] = 'Ошибка при регистрации. Возможно, имя пользователя или email уже заняты.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center mb-4">Регистрация</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Фамилия</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
        </form>
        
        <div class="mt-3 text-center">
            Уже есть аккаунт? <a href="login.php">Войдите</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>