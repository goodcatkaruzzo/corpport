<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['item_id'])) {
    header('Location: shop.php');
    exit;
}

$item_id = (int)$_POST['item_id'];
$item = getShopItem($item_id);

if (!$item) {
    header('Location: shop.php');
    exit;
}

if (purchaseItem($_SESSION['user_id'], $item_id)) {
    // Добавляем уведомление о покупке
    addAdvancedNotification(
        $_SESSION['user_id'],
        "Вы приобрели товар: {$item['name']} за {$item['price']} баллов",
        'success',
        $item_id,
        'shop_item'
    );
    
    header('Location: shop.php?purchase_success=1');
} else {
    header('Location: shop.php?purchase_error=1');
}
exit;