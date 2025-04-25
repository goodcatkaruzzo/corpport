<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_user = getCurrentUser();
$user_schedule = getUserWorkSchedule($_SESSION['user_id']);
$all_users = isAdmin() ? getAllUsers() : [];
$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];

if (isAdmin() && $selected_user_id !== $_SESSION['user_id']) {
    $selected_schedule = getUserWorkSchedule($selected_user_id);
    $selected_user = null;
    foreach ($all_users as $user) {
        if ($user['id'] == $selected_user_id) {
            $selected_user = $user;
            break;
        }
    }
} else {
    $selected_schedule = $user_schedule;
    $selected_user = $current_user;
}

// Обработка формы для администратора
if (isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    if (updateWorkSchedule($selected_user_id, $schedules)) {
        header("Location: schedule.php?user_id=$selected_user_id&success=1");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рабочий график - Корпоративный портал</title>
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
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <?php include 'navbar.php'; ?>
    
    <!-- Боковая панель -->
    <?php include 'sidebar.php'; ?>

    <!-- Основное содержимое -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Рабочий график</h2>
            <?php if (isAdmin() && !empty($all_users)): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <?= sanitize($selected_user['first_name']) ?> <?= sanitize($selected_user['last_name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <?php foreach ($all_users as $user): ?>
                            <li>
                                <a class="dropdown-item" href="schedule.php?user_id=<?= $user['id'] ?>">
                                    <?= sanitize($user['first_name']) ?> <?= sanitize($user['last_name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
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
            foreach ($selected_schedule as $day) {
                $schedule_map[$day['day_of_week']] = [
                    'start' => substr($day['start_time'], 0, 5),
                    'end' => substr($day['end_time'], 0, 5)
                ];
            }
        ?>
        
        <?php if (isAdmin()): ?>
            <form method="POST">
        <?php endif; ?>
        
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
                                <?php if (isAdmin()): ?>
                                    <input type="time" class="form-control" name="<?= $day_key ?>[start]" 
                                           value="<?= $schedule_map[$day_key]['start'] ?? '' ?>">
                                <?php else: ?>
                                    <?= $schedule_map[$day_key]['start'] ?? 'Выходной' ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isAdmin()): ?>
                                    <input type="time" class="form-control" name="<?= $day_key ?>[end]" 
                                           value="<?= $schedule_map[$day_key]['end'] ?? '' ?>">
                                <?php else: ?>
                                    <?= $schedule_map[$day_key]['end'] ?? 'Выходной' ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isAdmin()): ?>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
// После основного содержимого добавим:
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Календарь</h5>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Добавим стили и скрипты для календаря -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ru.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ru',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                <?php foreach ($selected_schedule as $day): ?>
                    {
                        title: 'Рабочая смена',
                        start: '<?= date('Y-m-d') ?>T<?= $day['start_time'] ?>',
                        end: '<?= date('Y-m-d') ?>T<?= $day['end_time'] ?>',
                        daysOfWeek: [ 
                            <?= 
                                ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 
                                 'friday' => 5, 'saturday' => 6, 'sunday' => 0][$day['day_of_week']] 
                            ?> 
                        ],
                        color: '#0d6efd',
                        textColor: 'white'
                    },
                <?php endforeach; ?>
            ],
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }
        });
        calendar.render();
    });
</script>