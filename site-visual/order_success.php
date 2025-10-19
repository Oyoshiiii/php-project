<?php
session_start();

$title = "Заказ успешно оформлен";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Если заказ еще не создан (например, при прямом переходе)
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0 && !empty($_SESSION['cart'])) {
    try {
        $pdo->beginTransaction();

        $session_id = session_id();

        // Получаем товары по текущей сессии
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            throw new Exception("Нет товаров в корзине для оформления заказа.");
        }

        // Подсчет общей суммы
        $total_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Получаем данные покупателя (можно заменить своими полями, если нужно)
        $customer_name = isset($_COOKIE['user_username']) ? urldecode($_COOKIE['user_username']) : 'Гость';
        $customer_email = isset($_COOKIE['user_email']) ? urldecode($_COOKIE['user_email']) : '';
        $customer_phone = isset($_COOKIE['user_number']) ? urldecode($_COOKIE['user_number']) : '';

        // Создаем заказ
        $stmt = $pdo->prepare("
            INSERT INTO orders (customer_name, customer_email, customer_phone, total_amount, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$customer_name, $customer_email, $customer_phone, $total_amount]);

        $order_id = $pdo->lastInsertId();

        // Привязываем товары к заказу
        $update = $pdo->prepare("UPDATE order_items SET order_id = ?, session_id = NULL WHERE session_id = ?");
        $update->execute([$order_id, $session_id]);

        // Очищаем корзину
        $_SESSION['cart'] = [];

        $pdo->commit();

        // Перенаправляем на страницу с order_id
        header("Location: order_success.php?order_id=" . $order_id);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Ошибка при оформлении заказа: " . $e->getMessage());
    }
}

// ---------- Получаем информацию о заказе ----------
$order_info = [];
$order_items = [];

if ($order_id > 0) {
    // Основная информация о заказе
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Товары из заказа
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - MangaMerchHub</title>
    <style>
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .order-details {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .order-items {
            margin: 20px 0;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .actions {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #007bff;
        }
        
        .btn-success {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        
        <h1>Заказ успешно оформлен!</h1>
        
        <?php if ($order_info): ?>
            <div class="order-details">
                <h3>Детали заказа #<?php echo $order_info['id']; ?></h3>
                <p><strong>Имя:</strong> <?php echo htmlspecialchars($order_info['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order_info['customer_email']); ?></p>
                <p><strong>Телефон:</strong> <?php echo htmlspecialchars($order_info['customer_phone']); ?></p>
                <p><strong>Дата заказа:</strong> <?php echo $order_info['created_at']; ?></p>
                
                <div class="order-items">
                    <h4>Состав заказа:</h4>
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: right; font-size: 20px; font-weight: bold; margin-top: 20px;">
                    Итого: $<?php echo number_format($order_info['total_amount'], 2); ?>
                </div>
            </div>
            
            <p>Мы свяжемся с вами в ближайшее время для подтверждения заказа и уточнения деталей доставки.</p>
        <?php else: ?>
            <p>Информация о заказе не найдена.</p>
        <?php endif; ?>
        
        <div class="actions">
            <a href="catalog.php" class="btn btn-primary">Продолжить покупки</a>
            <a href="index.php" class="btn btn-success">На главную</a>
        </div>
    </div>
</body>
</html>
<?php
include("blocks/ending.php");
?>