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
 
    <!-- Индикатор корзины -->
    <a href="cart.php" class="cart-indicator">
        🛒 Корзина: <?php echo getCartCount(); ?> товаров
    </a>

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
            <div class="filter-row">
                <div class="filter-group">
                    <label for="category">Категория</label>
                    <select id="category">
                        <option value="">Все категории</option>
                        <option value="electronics">Электроника</option>
                        <option value="phones">Телефоны</option>
                        <option value="laptops">Ноутбуки</option>
                        <option value="accessories">Аксессуары</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="brand">Бренд</label>
                    <select id="brand">
                        <option value="">Все бренды</option>
                        <option value="apple">Apple</option>
                        <option value="samsung">Samsung</option>
                        <option value="sony">Sony</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price">Цена</label>
                    <select id="price">
                        <option value="">Любая цена</option>
                        <option value="0-500">До 500 руб.</option>
                        <option value="500-1000">500 - 1000 руб.</option>
                        <option value="1000-2000">1000 - 2000 руб.</option>
                        <option value="2000+">От 2000 руб.</option>
                    </select>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Поиск товаров...">
                <button id="search-btn">Найти</button>
            </div>
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