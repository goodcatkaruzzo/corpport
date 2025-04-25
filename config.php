<?php
// Конфигурация базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'corporate_portal');

// Настройки сессии
session_start();

// Подключение к базе данных
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для проверки авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Функция для проверки роли администратора
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Функция для защиты от XSS
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>