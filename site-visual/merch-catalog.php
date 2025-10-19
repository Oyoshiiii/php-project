<?php
session_start(); 

$title = "Каталог товаров";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345"); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Функции для работы с корзиной
function addToCart($product_id, $quantity = 1) {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}
function removeFromCartDB($pdo, $product_id) {
    $session_id = session_id();

    $stmt = $pdo->prepare("DELETE FROM order_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
}
function addToCartDB($pdo, $product_id, $quantity) {
    $session_id = session_id();

    // Проверяем, есть ли уже такой товар в order_items
    $stmt = $pdo->prepare("SELECT id, quantity FROM order_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Обновляем количество
        $new_qty = $item['quantity'] + $quantity;
        $update = $pdo->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
        $update->execute([$new_qty, $item['id']]);
    } else {
        // Добавляем новый товар
        $price_stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $price_stmt->execute([$product_id]);
        $product = $price_stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $insert = $pdo->prepare("INSERT INTO order_items (session_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $insert->execute([$session_id, $product_id, $quantity, $product['price']]);
        }
    }
}
function removeFromCart($pdo, $product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    // Удаляем из базы
    removeFromCartDB($pdo, $product_id);
}

function updateCart($pdo, $product_id, $quantity) {
    $session_id = session_id();

    if ($quantity <= 0) {
        // Удаляем из корзины и из БД
        removeFromCart($pdo, $product_id);
    } else {
        // Обновляем в сессии
        $_SESSION['cart'][$product_id] = $quantity;

        // Проверяем, есть ли уже запись в order_items
        $stmt = $pdo->prepare("SELECT id FROM order_items WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Обновляем количество
            $update = $pdo->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
            $update->execute([$quantity, $item['id']]);
        } else {
            // Добавляем новую запись, если по какой-то причине отсутствует
            $price_stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $price_stmt->execute([$product_id]);
            $product = $price_stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $insert = $pdo->prepare("INSERT INTO order_items (session_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $insert->execute([$session_id, $product_id, $quantity, $product['price']]);
            }
        }
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

// Функция для создания заказа
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

// Инициализация корзины
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка добавления в корзину
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'add_to_cart') {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity > 0) {
        addToCart($product_id, $quantity);
        addToCartDB($pdo, $product_id, $quantity);
        $_SESSION['cart_message'] = "Товар добавлен в корзину!";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров - MangaMerchHub</title>
    <link rel="stylesheet" href="css/merch-catalog.css">
    <style>
        .cart-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            text-decoration: none;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .cart-indicator:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        
        .cart-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        
        .quantity-selector {
            margin: 10px 0;
        }
        
        .quantity-selector input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Сообщения -->
        <?php if (isset($_SESSION['cart_message'])): ?>
            <div class="cart-message">
                <?php echo $_SESSION['cart_message']; ?>
                <?php unset($_SESSION['cart_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Фильтры для товаров -->
        <div class="catalog-filters">
            
        </div>
        
        <?php
            // Получение товаров из базы данных
            $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 100");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <!-- Сетка товаров -->
        <div class="catalog-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>Товары не найдены</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="catalog-item">
                        <div class="item-img">
                            <?php if (!empty($product['image']) && file_exists('images/' . $product['image'])): ?>
                                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <img src="images/template.jpg" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="item-info">
                            <h3 class="item-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description">
                                <?php 
                                    $description = htmlspecialchars($product['description']);
                                    echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                ?>
                            </p>
                            <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                            
                            <form method="post" class="add-to-cart-form">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                
                                <div class="quantity-selector">
                                    <label for="quantity_<?php echo $product['id']; ?>">Количество:</label>
                                    <input type="number" name="quantity" id="quantity_<?php echo $product['id']; ?>" 
                                           value="1" min="1" max="10">
                                </div>
                                
                                <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Страницы -->
        <div class="pagination">
            <a href="#" class="page-link active">1</a>
            <a href="#" class="page-link">2</a>
            <a href="#" class="page-link">3</a>
            <a href="#" class="page-link">4</a>
            <a href="#" class="page-link">5</a>
            <a href="#" class="page-link">→</a>
        </div>
    </div>

    <script>
        // Обработка фильтров
        document.querySelectorAll('.catalog-filters select').forEach(select => {
            select.addEventListener('change', function() {
                applyFilters();
            });
        });
        
        // Обработка поиска
        document.querySelector('#search-btn').addEventListener('click', applyFilters);
        document.querySelector('#search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });
        
        function applyFilters() {
            const category = document.querySelector('#category').value;
            const brand = document.querySelector('#brand').value;
            const price = document.querySelector('#price').value;
            const searchTerm = document.querySelector('#search-input').value.trim();
            
            console.log('Применение фильтров:', { category, brand, price, searchTerm });
        }
        
        // Обработка добавления в корзину
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const productTitle = this.closest('.item-info').querySelector('.item-title').textContent;
                const quantity = this.querySelector('input[name="quantity"]').value;
                
                console.log(`Добавление в корзину: "${productTitle}" (${quantity} шт.)`);
            });
        });
    </script>
</body>
</html>
<?php
include("blocks/ending.php");
?>