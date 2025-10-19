<?php
session_start();

$title = "Оформление заказа";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345"); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Функции корзины
function getCartTotal($pdo) {
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        
        $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $product) {
            $total += $product['price'] * $_SESSION['cart'][$product['id']];
        }
    }
    return $total;
}

function createOrder($pdo, $customer_data) {
    try {
        $pdo->beginTransaction();
        
        $total_amount = getCartTotal($pdo);
        $stmt = $pdo->prepare("
            INSERT INTO orders (customer_name, customer_email, customer_phone, total_amount, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $customer_data['name'],
            $customer_data['email'],
            $customer_data['phone'],
            $total_amount
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Добавляем товары в order_items
        if (!empty($_SESSION['cart'])) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product_stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $product_stmt->execute([$product_id]);
                $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $stmt->execute([
                        $order_id,
                        $product_id,
                        $quantity,
                        $product['price']
                    ]);
                }
            }
        }
        
        $pdo->commit();
        return $order_id;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Получение данных пользователя из куки
function getUserDataFromCookies() {
    $user_data = [
        'name' => '',
        'email' => '',
        'phone' => ''
    ];
    
    // Получаем данные из куки
    if (isset($_COOKIE['user_username'])) {
        $user_data['name'] = urldecode($_COOKIE['user_username']);
    }
    
    if (isset($_COOKIE['user_email'])) {
        $user_data['email'] = urldecode($_COOKIE['user_email']);
    }
    
    if (isset($_COOKIE['user_number'])) {
        $user_data['phone'] = urldecode($_COOKIE['user_number']);
    }
    
    return $user_data;
}

// Обработка оформления заказа
if ($_POST && isset($_POST['place_order'])) {
    if (!empty($_SESSION['cart'])) {
        try {
            // Используем данные из формы или из куки
            $customer_data = [
                'name' => !empty($_POST['customer_name']) ? $_POST['customer_name'] : (isset($_COOKIE['user_username']) ? urldecode($_COOKIE['user_username']) : ''),
                'email' => !empty($_POST['customer_email']) ? $_POST['customer_email'] : (isset($_COOKIE['user_email']) ? urldecode($_COOKIE['user_email']) : ''),
                'phone' => !empty($_POST['customer_phone']) ? $_POST['customer_phone'] : (isset($_COOKIE['user_number']) ? urldecode($_COOKIE['user_number']) : '')
            ];
            
            // Проверяем, что все обязательные поля заполнены
            if (empty($customer_data['name']) || empty($customer_data['email']) || empty($customer_data['phone'])) {
                throw new Exception("Пожалуйста, заполните все обязательные поля");
            }
            
            $order_id = createOrder($pdo, $customer_data);
            
            // Очищаем корзину после успешного оформления
            $_SESSION['cart'] = [];
            
            $_SESSION['order_success'] = "Заказ #$order_id успешно оформлен!";
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
            
        } catch (Exception $e) {
            $error = "Ошибка при оформлении заказа: " . $e->getMessage();
        }
    } else {
        $error = "Корзина пуста!";
    }
}

// Если корзина пуста, перенаправляем
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Получаем данные пользователя из куки для автозаполнения
$user_data = getUserDataFromCookies();

require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - MangaMerchHub</title>
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-form">
            <h1>Оформление заказа</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($user_data['name']) || !empty($user_data['email']) || !empty($user_data['phone'])): ?>
                <div class="user-data-info">
                    <h3>Ваши данные из профиля:</h3>
                    <?php if (!empty($user_data['name'])): ?>
                        <p><strong>Имя:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user_data['email'])): ?>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user_data['phone'])): ?>
                        <p><strong>Телефон:</strong> <?php echo htmlspecialchars($user_data['phone']); ?></p>
                    <?php endif; ?>
                    <p><small>Эти данные будут использованы для заказа. Вы можете изменить их ниже.</small></p>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="customer_name">Имя и фамилия *</label>
                    <input type="text" id="customer_name" name="customer_name" required 
                           value="<?php 
                               echo isset($_POST['customer_name']) 
                                   ? htmlspecialchars($_POST['customer_name']) 
                                   : (!empty($user_data['name']) ? htmlspecialchars($user_data['name']) : '');
                           ?>">
                </div>
                
                <div class="form-group">
                    <label for="customer_email">Email *</label>
                    <input type="email" id="customer_email" name="customer_email" required
                           value="<?php 
                               echo isset($_POST['customer_email']) 
                                   ? htmlspecialchars($_POST['customer_email']) 
                                   : (!empty($user_data['email']) ? htmlspecialchars($user_data['email']) : '');
                           ?>">
                </div>
                
                <div class="form-group">
                    <label for="customer_phone">Телефон *</label>
                    <input type="tel" id="customer_phone" name="customer_phone" required
                           value="<?php 
                               echo isset($_POST['customer_phone']) 
                                   ? htmlspecialchars($_POST['customer_phone']) 
                                   : (!empty($user_data['phone']) ? htmlspecialchars($user_data['phone']) : '');
                           ?>">
                </div>
                
                <button type="submit" name="place_order" class="submit-btn">
                    ✅ Подтвердить заказ
                </button>
            </form>
            
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; border: 1px solid #ffeaa7;">
                <h4>ℹ️ Информация о заказе</h4>
                <p>После оформления заказа:</p>
                <ul>
                    <li>Вы получите номер заказа для отслеживания</li>
                    <li>С вами свяжутся для подтверждения заказа</li>
                    <li>Заказ будет обработан в течение 24 часов</li>
                </ul>
            </div>
        </div>
        
        <div class="order-summary">
            <h2>Ваш заказ</h2>
            <div class="order-items">
                <?php
                $total = 0;
                $item_count = 0;
                
                foreach ($_SESSION['cart'] as $product_id => $quantity):
                    $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product):
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                        $item_count += $quantity;
                ?>
                    <div class="order-item">
                        <div>
                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                            <br>
                            <small>Количество: <?php echo $quantity; ?> × $<?php echo number_format($product['price'], 2); ?></small>
                        </div>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
            
            <div class="order-total">
                <div style="margin-bottom: 10px;">
                    <strong>Товаров: <?php echo $item_count; ?> шт.</strong>
                </div>
                <div style="font-size: 24px; color: #28a745;">
                    Итого: $<?php echo number_format($total, 2); ?>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: #e8f5e8; border-radius: 5px;">
                <h4>🚚 Доставка</h4>
                <p>Стоимость доставки рассчитывается отдельно после подтверждения заказа.</p>
                <p>Срок доставки: 2-5 рабочих дней</p>
            </div>
        </div>
    </div>

    <script>
        // Автоподстановка данных при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Проверяем, заполнены ли поля, если нет - пытаемся взять из куки
            const nameField = document.getElementById('customer_name');
            const emailField = document.getElementById('customer_email');
            const phoneField = document.getElementById('customer_phone');
            
            // Если поля пустые, можно попробовать получить данные из localStorage (если они там есть)
            if (!nameField.value) {
                // Можно добавить дополнительную логику для получения данных
                console.log('Поля формы готовы к заполнению');
            }
        });

        // Валидация формы перед отправкой
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('customer_name').value.trim();
            const email = document.getElementById('customer_email').value.trim();
            const phone = document.getElementById('customer_phone').value.trim();
            
            if (!name || !email || !phone) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
                return false;
            }
            
            // Простая валидация email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Пожалуйста, введите корректный email адрес');
                return false;
            }
            
            // Можно добавить дополнительную валидацию телефона
            if (phone.length < 5) {
                e.preventDefault();
                alert('Пожалуйста, введите корректный номер телефона');
                return false;
            }
            
            // Показываем сообщение о обработке
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.innerHTML = '⏳ Обработка заказа...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
<?php
include("blocks/ending.php");
?>