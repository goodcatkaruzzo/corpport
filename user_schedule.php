<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;

// Получаем данные пользователя
global $pdo;
$stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin.php');
    exit;
}

$user_schedule = getUserWorkSchedule($user_id);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedules = [];
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    foreach ($days as $day) {
        if (!empty($_POST[$day]['start']) && !empty($_POST[$day]['end'])) {
            $schedules[$day] = [
                'start' => sanitize($_POST[$day]['start']),
                'end' => sanitize($_POST[$day]['end'])
            ];
        }
    }
    
    if (updateWorkSchedule($user_id, $schedules)) {
        header("Location: user_schedule.php?id=$user_id&success=1");
        exit;
    } else {
        $error = "Ошибка при обновлении графика";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>График работы - Корпоративный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin.php">Администрирование</a></li>
                <li class="breadcrumb-item active">График работы: <?= sanitize($user['first_name']) ?> <?= sanitize($user['last_name']) ?></li>
            </ol>
        </nav>
        
        <h2>График работы: <?= sanitize($user['first_name']) ?> <?= sanitize($user['last_name']) ?></h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">График успешно обновлен</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
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
            
            $schedule_map = [];
            foreach ($user_schedule as $day) {
                $schedule_map[$day['day_of_week']] = [
                    'start' => substr($day['start_time'], 0, 5),
                    'end' => substr($day['end_time'], 0, 5)
                ];
            }
        ?>
        
        <form method="POST">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>День недели</th>
                            <th>Начало работы</th>
                            <th>Конец работы</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($day_names as $day_key => $day_name): ?>
                            <tr>
                                <td><?= $day_name ?></td>
                                <td>
                                    <input type="time" class="form-control" name="<?= $day_key ?>[start]" 
                                           value="<?= $schedule_map[$day_key]['start'] ?? '' ?>">
                                </td>
                                <td>
                                    <input type="time" class="form-control" name="<?= $day_key ?>[end]" 
                                           value="<?= $schedule_map[$day_key]['end'] ?? '' ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <a href="admin.php" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>