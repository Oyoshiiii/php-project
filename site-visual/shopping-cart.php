<?php
session_start();

$title = "Корзина товаров";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345"); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Функции корзины (те же что в catalog.php)
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCart($product_id, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

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

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $quantity) {
            $count += $quantity;
        }
    }
    return $count;
}

// Обработка действий с корзиной
if ($_POST) {
    if (isset($_POST['remove'])) {
        removeFromCart($_POST['product_id']);
        $_SESSION['cart_message'] = "Товар удален из корзины";
    } elseif (isset($_POST['update'])) {
        $quantity = intval($_POST['quantity']);
        updateCart($_POST['product_id'], $quantity);
        $_SESSION['cart_message'] = "Количество обновлено";
    } elseif (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['cart_message'] = "Корзина очищена";
    }
    
    header("Location: cart.php");
    exit();
}

// Получение товаров корзины
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - MangaMerchHub</title>
    <link rel="stylesheet" href="css/merch-catalog.css">
    <style>
        
        .cart-container {
            max-width: fit-content;
            margin: 0 auto;
            padding: 20px;
            background-color: #1a1a2e;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-evenly;
            color: white;
            align-items: center;
            margin-bottom: 30px;
        }

        .cart-header h1{
            color: white;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #1a1a2e;
        }
        
        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            background-color: #1a1a2e;
        }
        
        .cart-table th {
            background-color: #1a1a2e;
            font-weight: bold;
        }
        
        .cart-product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cart-product-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .quantity-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-input {
            width: 70px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .update-btn, .remove-btn, .checkout-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .update-btn {
            background: #17a2b8;
            color: white;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
        }
        
        .checkout-btn {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
        }
        
        .cart-total {
            text-align: right;
            padding: 20px;
            background-color: #1a1a2e;
            border-radius: 8px;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        
        .empty-cart a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .cart-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        
        .clear-cart-btn {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .thead{
            background-color: #1a1a2e;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Корзина покупок</h1>
            <a href="catalog.php" class="checkout-btn">← Вернуться к покупкам</a>
        </div>

        <?php if (isset($_SESSION['cart_message'])): ?>
            <div class="cart-message">
                <?php echo $_SESSION['cart_message']; ?>
                <?php unset($_SESSION['cart_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h2>Ваша корзина пуста</h2>
                <p>Добавьте товары из каталога, чтобы сделать заказ</p>
                <a href="merch-catalog.php">Перейти в каталог</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Итого</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-product-info">
                                <?php if (!empty($item['product']['image'])): ?>
                                    <img src="images/<?php echo htmlspecialchars($item['product']['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                         onerror="this.src='images/template.jpg'">
                                <?php else: ?>
                                    <img src="images/template.jpg" 
                                         alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                                <?php endif; ?>
                                <div>
                                    <h4><?php echo htmlspecialchars($item['product']['name']); ?></h4>
                                    <p class="product-description">
                                        <?php 
                                            $description = htmlspecialchars($item['product']['description']);
                                            echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                        <td>
                            <form method="post" class="quantity-form">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="10" class="quantity-input">
                                <button type="submit" name="update" class="update-btn">Обновить</button>
                            </form>
                        </td>
                        <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <button type="submit" name="remove" class="remove-btn">Удалить</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-total">
                <h2>Общая сумма: $<?php echo number_format($total, 2); ?></h2>
            </div>
            
            <div class="cart-actions">
                <form method="post">
                    <button type="submit" name="clear_cart" class="clear-cart-btn" 
                            onclick="return confirm('Вы уверены, что хотите очистить корзину?')">
                        Очистить корзину
                    </button>
                </form>
                
                <a href="checkout.php" class="checkout-btn">Оформить заказ</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Подтверждение удаления
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Подтверждение очистки корзины
        document.querySelector('.clear-cart-btn')?.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите очистить всю корзину?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
<?php
include("blocks/ending.php");
?>