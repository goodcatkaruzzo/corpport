<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$achievements = getUserAchievements($_SESSION['user_id']);
$notifications = getUserNotifications($_SESSION['user_id'], true);
$schedule = getUserWorkSchedule($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой профиль - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .profile-header {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .achievement-icon {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="profile-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><?= sanitize($user['first_name']) ?> <?= sanitize($user['last_name']) ?></h1>
                    <p class="lead"><?= sanitize($user['email']) ?></p>
                    <p class="mb-0">Телефон: <?= sanitize($user['phone']) ?></p>
                    <p>Баллы: <span class="badge bg-primary"><?= $user['points'] ?></span></p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex flex-column align-items-end">
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?> mb-2">
                            <?= $user['role'] === 'admin' ? 'Администратор' : 'Пользователь' ?>
                        </span>
                        <small class="text-muted">Зарегистрирован: <?= date('d.m.Y', strtotime($user['created_at'])) ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Мои достижения</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($achievements)): ?>
                            <p class="text-muted">У вас пока нет достижений</p>
                        <?php else: ?>
                            <div class="d-flex flex-wrap">
                                <?php foreach ($achievements as $achievement): ?>
                                    <div class="mb-3 me-3 text-center" data-bs-toggle="tooltip" title="<?= sanitize($achievement['description']) ?>">
                                        <img src="images/achievements/<?= sanitize($achievement['icon']) ?>" alt="<?= sanitize($achievement['name']) ?>" class="achievement-icon">
                                        <div class="small"><?= sanitize($achievement['name']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Мои уведомления</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notifications)): ?>
                            <p class="text-muted">Нет новых уведомлений</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($notifications as $notification): ?>
                                    <a href="notifications.php" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <small><?= sanitize($notification['message']) ?></small>
                                            <small class="text-muted"><?= date('d.m.Y H:i', strtotime($notification['created_at'])) ?></small>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a href="notifications.php" class="btn btn-sm btn-outline-primary mt-3">Все уведомления</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Мой график работы</h5>
            </div>
            <div class="card-body">
                <?php if (empty($schedule)): ?>
                    <p class="text-muted">График работы не установлен</p>
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