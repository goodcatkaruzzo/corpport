<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php');
    exit;
}

$users = getAllUsers();
$news = getNews(100);
$wiki = getWikiArticles();
$shop_items = getShopItems();
$achievements = getAllAchievements();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администрирование - Корпоративный портал</title>
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
        .admin-tab {
            display: none;
        }
        .admin-tab.active {
            display: block;
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
        <h2 class="mb-4">Администрирование</h2>
        
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">Пользователи</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button">Новости</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="wiki-tab" data-bs-toggle="tab" data-bs-target="#wiki" type="button">Wiki</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shop-tab" data-bs-toggle="tab" data-bs-target="#shop" type="button">Магазин</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button">Достижения</button>
            </li>
        </ul>
        
        <div class="tab-content" id="adminTabsContent">
            <!-- Вкладка пользователей -->
            <div class="admin-tab active" id="users">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Управление пользователями</h4>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя пользователя</th>
                                <th>Имя</th>
                                <th>Фамилия</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Роль</th>
                                <th>Баллы</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= sanitize($user['username']) ?></td>
                                    <td><?= sanitize($user['first_name']) ?></td>
                                    <td><?= sanitize($user['last_name']) ?></td>
                                    <td><?= sanitize($user['email']) ?></td>
                                    <td><?= sanitize($user['phone']) ?></td>
                                    <td><?= $user['role'] ?></td>
                                    <td><?= $user['points'] ?></td>
                                    <td>
                                        <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                                        <a href="user_schedule.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary">График</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Вкладка новостей -->
            <div class="admin-tab" id="news">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Управление новостями</h4>
                    <a href="news_add.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Добавить новость
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Заголовок</th>
                                <th>Автор</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= sanitize($item['title']) ?></td>
                                    <td><?= sanitize($item['first_name']) ?> <?= sanitize($item['last_name']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                                    <td>
                                        <a href="news_edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                                        <a href="news_delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить эту новость?')">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Вкладка wiki -->
            <div class="admin-tab" id="wiki">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Управление Wiki</h4>
                    <a href="wiki_add.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Добавить статью
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Заголовок</th>
                                <th>Автор</th>
                                <th>Обновлено</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wiki as $article): ?>
                                <tr>
                                    <td><?= $article['id'] ?></td>
                                    <td><?= sanitize($article['title']) ?></td>
                                    <td><?= sanitize($article['first_name']) ?> <?= sanitize($article['last_name']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($article['updated_at'])) ?></td>
                                    <td>
                                        <a href="wiki_edit.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                                        <a href="wiki_delete.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить эту статью?')">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Вкладка магазина -->
            <div class="admin-tab" id="shop">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Управление корпоративной лавкой</h4>
                    <a href="shop_add.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Добавить товар
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Цена</th>
                                <th>В наличии</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shop_items as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= sanitize($item['name']) ?></td>
                                    <td><?= $item['price'] ?></td>
                                    <td><?= $item['stock'] ?></td>
                                    <td>
                                        <a href="shop_edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                                        <a href="shop_delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить этот товар?')">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Вкладка достижений -->
            <div class="admin-tab" id="achievements">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Управление достижениями</h4>
                    <a href="achievement_add.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Добавить достижение
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Описание</th>
                                <th>Баллы</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($achievements as $achievement): ?>
                                <tr>
                                    <td><?= $achievement['id'] ?></td>
                                    <td><?= sanitize($achievement['name']) ?></td>
                                    <td><?= sanitize($achievement['description']) ?></td>
                                    <td><?= $achievement['points'] ?></td>
                                    <td>
                                        <a href="achievement_edit.php?id=<?= $achievement['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                                        <a href="achievement_delete.php?id=<?= $achievement['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить это достижение?')">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Переключение вкладок
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#adminTabs .nav-link');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Удаляем активный класс у всех вкладок
                    tabs.forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
                    
                    // Добавляем активный класс текущей вкладке
                    this.classList.add('active');
                    const target = this.getAttribute('data-bs-target');
                    document.querySelector(target).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>