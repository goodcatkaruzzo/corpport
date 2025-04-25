<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_user = getCurrentUser();
$notifications = getUserNotifications($_SESSION['user_id'], true);
$news = getNews(5);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корпоративный портал</title>
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
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }
        .achievement-icon {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .shop-item-img {
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Корпоративный портал</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            <?php if (count($notifications) > 0): ?>
                                <span class="badge bg-danger notification-badge"><?= count($notifications) ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <?php if (empty($notifications)): ?>
                                <li><a class="dropdown-item" href="#">Нет новых уведомлений</a></li>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <li><a class="dropdown-item" href="notifications.php"><?= sanitize($notification['message']) ?></a></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="notifications.php">Все уведомления</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= sanitize($current_user['first_name']) ?> <?= sanitize($current_user['last_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">Профиль</a></li>
                            <li><a class="dropdown-item" href="achievements.php">Достижения</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Боковая панель -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 220px;">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="index.php" class="nav-link active">
                    <i class="bi bi-house-door me-2"></i> Главная
                </a>
            </li>
            <li>
                <a href="news.php" class="nav-link">
                    <i class="bi bi-newspaper me-2"></i> Новости
                </a>
            </li>
            <li>
                <a href="wiki.php" class="nav-link">
                    <i class="bi bi-book me-2"></i> Wiki
                </a>
            </li>
            <li>
                <a href="shop.php" class="nav-link">
                    <i class="bi bi-shop me-2"></i> Корпоративная лавка
                </a>
            </li>
            <li>
                <a href="schedule.php" class="nav-link">
                    <i class="bi bi-calendar3 me-2"></i> Рабочий график
                </a>
            </li>
            <?php if (isAdmin()): ?>
            <li>
                <hr>
                <a href="admin.php" class="nav-link text-danger">
                    <i class="bi bi-gear me-2"></i> Администрирование
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Основное содержимое -->
    <div class="main-content">
        <h2>Добро пожаловать, <?= sanitize($current_user['first_name']) ?>!</h2>
        <p>Ваши баллы: <span class="badge bg-primary"><?= $current_user['points'] ?></span></p>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Последние новости</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($news)): ?>
                            <p>Новостей пока нет</p>
                        <?php else: ?>
                            <?php foreach ($news as $item): ?>
                                <div class="mb-3">
                                    <h6><a href="news_item.php?id=<?= $item['id'] ?>"><?= sanitize($item['title']) ?></a></h6>
                                    <p class="text-muted small"><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?> | <?= sanitize($item['first_name']) ?> <?= sanitize($item['last_name']) ?></p>
                                    <p><?= nl2br(sanitize(substr($item['content'], 0, 200))) ?>...</p>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                            <a href="news.php" class="btn btn-sm btn-outline-primary">Все новости</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ваши достижения</h5>
                    </div>
                    <div class="card-body">
                        <?php $achievements = getUserAchievements($_SESSION['user_id']); ?>
                        <?php if (empty($achievements)): ?>
                            <p>У вас пока нет достижений</p>
                        <?php else: ?>
                            <div class="d-flex flex-wrap">
                                <?php foreach ($achievements as $achievement): ?>
                                    <div class="mb-3 me-3 text-center" data-bs-toggle="tooltip" title="<?= sanitize($achievement['description']) ?>">
                                        <img src="images/<?= sanitize($achievement['icon']) ?>" alt="<?= sanitize($achievement['name']) ?>" class="achievement-icon">
                                        <div class="small"><?= sanitize($achievement['name']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <a href="achievements.php" class="btn btn-sm btn-outline-primary">Все достижения</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ваш график</h5>
                    </div>
                    <div class="card-body">
                        <?php $schedule = getUserWorkSchedule($_SESSION['user_id']); ?>
                        <?php if (empty($schedule)): ?>
                            <p>График не установлен</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($schedule as $day): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php 
                                            $day_names = [
                                                'monday' => 'Понедельник',
                                                'tuesday' => 'Вторник',
                                                'wednesday' => 'Среда',
                                                'thursday' => 'Четверг',
                                                'friday' => 'Пятница',
                                                'saturday' => 'Суббота',
                                                'sunday' => 'Воскресенье'
                                            ];
                                            echo $day_names[$day['day_of_week']];
                                        ?>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= substr($day['start_time'], 0, 5) ?> - <?= substr($day['end_time'], 0, 5) ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <a href="schedule.php" class="btn btn-sm btn-outline-primary mt-3">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Включение всплывающих подсказок
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>