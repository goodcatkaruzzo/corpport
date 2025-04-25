<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 220px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-house-door me-2"></i> Главная
            </a>
        </li>
        <li>
            <a href="news.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : '' ?>">
                <i class="bi bi-newspaper me-2"></i> Новости
            </a>
        </li>
        <li>
            <a href="wiki.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'wiki.php' ? 'active' : '' ?>">
                <i class="bi bi-book me-2"></i> Wiki
            </a>
        </li>
        <li>
            <a href="shop.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : '' ?>">
                <i class="bi bi-shop me-2"></i> Корпоративная лавка
            </a>
        </li>
        <li>
            <a href="schedule.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : '' ?>">
                <i class="bi bi-calendar3 me-2"></i> Рабочий график
            </a>
        </li>
        <?php if (isAdmin()): ?>
        <li>
            <hr>
            <a href="admin.php" class="nav-link text-danger <?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : '' ?>">
                <i class="bi bi-gear me-2"></i> Администрирование
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
