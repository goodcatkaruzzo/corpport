<?php
require_once 'config.php';

// Регистрация нового пользователя
function registerUser($username, $password, $first_name, $last_name, $email, $phone) {
    global $pdo;
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, first_name, last_name, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $first_name, $last_name, $email, $phone]);
        
        // Назначаем достижение "Новичок"
        $achievement_id = 1; // ID достижения "Новичок"
        $user_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO user_achievements (user_id, achievement_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $achievement_id]);
        
        // Начисляем баллы за регистрацию
        $stmt = $pdo->prepare("UPDATE users SET points = points + 50 WHERE id = ?");
        $stmt->execute([$user_id]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Авторизация пользователя
function loginUser($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['points'] = $user['points'];
        return true;
    }
    
    return false;
}

// Получение информации о текущем пользователе
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Получение списка новостей
function getNews($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT n.*, u.first_name, u.last_name FROM news n JOIN users u ON n.author_id = u.id ORDER BY created_at DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Получение одной новости
function getNewsItem($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT n.*, u.first_name, u.last_name FROM news n JOIN users u ON n.author_id = u.id WHERE n.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Добавление новости
function addNews($title, $content) {
    if (!isLoggedIn()) return false;
    
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO news (title, content, author_id) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $content, $_SESSION['user_id']]);
}

// Обновление новости
function updateNews($id, $title, $content) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
    return $stmt->execute([$title, $content, $id]);
}

// Удаление новости
function deleteNews($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    return $stmt->execute([$id]);
}

// Получение списка wiki статей
function getWikiArticles() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT w.*, u.first_name, u.last_name FROM wiki w JOIN users u ON w.author_id = u.id ORDER BY title");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Получение одной wiki статьи
function getWikiArticle($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT w.*, u.first_name, u.last_name FROM wiki w JOIN users u ON w.author_id = u.id WHERE w.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Добавление wiki статьи
function addWikiArticle($title, $content) {
    if (!isLoggedIn()) return false;
    
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO wiki (title, content, author_id) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $content, $_SESSION['user_id']]);
}

// Обновление wiki статьи
function updateWikiArticle($id, $title, $content) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE wiki SET title = ?, content = ? WHERE id = ?");
    return $stmt->execute([$title, $content, $id]);
}

// Удаление wiki статьи
function deleteWikiArticle($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM wiki WHERE id = ?");
    return $stmt->execute([$id]);
}

// Получение уведомлений пользователя
function getUserNotifications($user_id, $unread_only = false) {
    global $pdo;
    $sql = "SELECT * FROM notifications WHERE user_id = ?";
    if ($unread_only) $sql .= " AND is_read = FALSE";
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Добавление уведомления
function addNotification($user_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    return $stmt->execute([$user_id, $message]);
}

// Отметка уведомления как прочитанного
function markNotificationAsRead($notification_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ?");
    return $stmt->execute([$notification_id]);
}

// Получение достижений пользователя
function getUserAchievements($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT a.* FROM achievements a JOIN user_achievements ua ON a.id = ua.achievement_id WHERE ua.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Получение всех достижений
function getAllAchievements() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM achievements");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Добавление достижения пользователю
function addUserAchievement($user_id, $achievement_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO user_achievements (user_id, achievement_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $achievement_id]);
        
        // Добавляем баллы за достижение
        $stmt = $pdo->prepare("SELECT points FROM achievements WHERE id = ?");
        $stmt->execute([$achievement_id]);
        $points = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
        $stmt->execute([$points, $user_id]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Получение товаров в магазине
function getShopItems() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM shop_items ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Получение одного товара
function getShopItem($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM shop_items WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Добавление товара в магазин
function addShopItem($name, $description, $price, $stock, $image) {
    if (!isAdmin()) return false;
    
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO shop_items (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $description, $price, $stock, $image]);
}

// Обновление товара в магазине
function updateShopItem($id, $name, $description, $price, $stock, $image) {
    if (!isAdmin()) return false;
    
    global $pdo;
    $stmt = $pdo->prepare("UPDATE shop_items SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
    return $stmt->execute([$name, $description, $price, $stock, $image, $id]);
}

// Удаление товара из магазина
function deleteShopItem($id) {
    if (!isAdmin()) return false;
    
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM shop_items WHERE id = ?");
    return $stmt->execute([$id]);
}

// Покупка товара пользователем
function purchaseItem($user_id, $item_id) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Проверяем наличие товара и баллов у пользователя
        $item = getShopItem($item_id);
        $user = getCurrentUser();
        
        if (!$item || $item['stock'] < 1) {
            throw new Exception("Товар отсутствует в наличии");
        }
        
        if ($user['points'] < $item['price']) {
            throw new Exception("Недостаточно баллов для покупки");
        }
        
        // Создаем запись о покупке
        $stmt = $pdo->prepare("INSERT INTO user_purchases (user_id, item_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $item_id]);
        
        // Уменьшаем количество товара
        $stmt = $pdo->prepare("UPDATE shop_items SET stock = stock - 1 WHERE id = ?");
        $stmt->execute([$item_id]);
        
        // Списание баллов
        $stmt = $pdo->prepare("UPDATE users SET points = points - ? WHERE id = ?");
        $stmt->execute([$item['price'], $user_id]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

// Получение рабочего графика пользователя
function getUserWorkSchedule($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM work_schedules WHERE user_id = ? ORDER BY 
        CASE day_of_week 
            WHEN 'monday' THEN 1 
            WHEN 'tuesday' THEN 2 
            WHEN 'wednesday' THEN 3 
            WHEN 'thursday' THEN 4 
            WHEN 'friday' THEN 5 
            WHEN 'saturday' THEN 6 
            WHEN 'sunday' THEN 7 
        END");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Обновление рабочего графика
function updateWorkSchedule($user_id, $schedules) {
    if (!isAdmin()) return false;
    
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Удаляем старый график
        $stmt = $pdo->prepare("DELETE FROM work_schedules WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Добавляем новый
        $stmt = $pdo->prepare("INSERT INTO work_schedules (user_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        
        foreach ($schedules as $day => $times) {
            if ($times['start'] && $times['end']) {
                $stmt->execute([$user_id, $day, $times['start'], $times['end']]);
            }
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

// Получение всех пользователей
function getAllUsers() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, phone, role, points FROM users ORDER BY last_name, first_name");
    $stmt->execute();
    return $stmt->fetchAll();
}
// Поиск по новостям
function searchNews($query, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT n.*, u.first_name, u.last_name FROM news n 
                          JOIN users u ON n.author_id = u.id 
                          WHERE n.title LIKE ? OR n.content LIKE ?
                          ORDER BY n.created_at DESC LIMIT ?");
    $search_query = "%$query%";
    $stmt->bindValue(1, $search_query);
    $stmt->bindValue(2, $search_query);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Поиск по wiki
function searchWiki($query, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT w.*, u.first_name, u.last_name FROM wiki w 
                          JOIN users u ON w.author_id = u.id 
                          WHERE w.title LIKE ? OR w.content LIKE ?
                          ORDER BY w.updated_at DESC LIMIT ?");
    $search_query = "%$query%";
    $stmt->bindValue(1, $search_query);
    $stmt->bindValue(2, $search_query);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
// Получение количества непрочитанных уведомлений
function getUnreadNotificationsCount($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

// Получение последних покупок пользователя
function getUserPurchases($user_id, $limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT up.*, si.name, si.image 
                          FROM user_purchases up 
                          JOIN shop_items si ON up.item_id = si.id 
                          WHERE up.user_id = ? 
                          ORDER BY up.purchased_at DESC 
                          LIMIT ?");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Получение популярных товаров
function getPopularShopItems($limit = 3) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT si.*, COUNT(up.id) as purchases 
                          FROM shop_items si 
                          LEFT JOIN user_purchases up ON si.id = up.item_id 
                          GROUP BY si.id 
                          ORDER BY purchases DESC, si.id 
                          LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
?>
