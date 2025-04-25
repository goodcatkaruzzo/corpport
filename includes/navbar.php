<?php
if (!isset($current_user)) {
    $current_user = getCurrentUser();
    $notifications = getUserNotifications($_SESSION['user_id'], true);
}
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Корпоративный портал</a>
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
