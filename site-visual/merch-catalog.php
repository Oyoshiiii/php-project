<?php
$title = "Каталог мерча";
require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог мерча - MangaMerchHub</title>
    <link rel="stylesheet" href="css/merch-catalog.css">
</head>
<body>
 
    <div class="container">
        <!-- Фильтры для мерча -->
        <div class="catalog-filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="category">Категория</label>
                    <select id="category">
                        <option value="">Все категории</option>
                        <option value="clothes">Одежда</option>
                        <option value="figures">Фигурки</option>
                        <option value="accessories">Аксессуары</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="series">Аниме/Манга</label>
                    <select id="series">
                        <option value="">Все серии</option>
                        <option value="naruto">Наруто</option>
                        <option value="onepiece">Ван Пис</option>
                        <option value="attackontitan">Атака титанов</option>
                        <option value="sailormoon">Сейлор Мун</option>
                        <option value="myheroacademia">Моя геройская академия</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price">Цена</label>
                    <select id="price">
                        <option value="">Любая цена</option>
                        <option value="0-1000">До 1000 руб.</option>
                        <option value="1000-3000">1000 - 3000 руб.</option>
                        <option value="3000-5000">3000 - 5000 руб.</option>
                        <option value="5000+">От 5000 руб.</option>
                    </select>
                </div>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Поиск товаров...">
                <button>Найти</button>
            </div>
        </div>
        <?php
            /*
            
            class Database {
                private $host = "localhost";
                private $db_name = "online_store";
                private $username = "root";
                private $password = "12345";
                public $conn;

                public function getConnection() {
                    $this->conn = null;
                    try {
                        $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                        $this->conn->exec("set names utf8");
                        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch(PDOException $exception) {
                        echo "Ошибка подключения: " . $exception->getMessage();
                    }
                    return $this->conn;
                }
            }
            
            $database = new Database();
            $pdo = $database->getConnection();

            // Обработка добавления в корзину
            if ($_POST && isset($_POST['product_id'])) {
                addToCart($_POST['product_id'], 1);
                header("Location: products.php");
                exit();
            }

            // Получение товаров из базы данных
            $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            session_start();

            // Инициализация корзины
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Добавление товара в корзину
            function addToCart($product_id, $quantity = 1) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
            }

            // Удаление товара из корзины
            function removeFromCart($product_id) {
                if (isset($_SESSION['cart'][$product_id])) {
                    unset($_SESSION['cart'][$product_id]);
                }
            }

            // Обновление количества товара
            function updateCart($product_id, $quantity) {
                if ($quantity <= 0) {
                    removeFromCart($product_id);
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
            }

            // Получение общей стоимости корзины
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

            // Получение количества товаров в корзине
            function getCartCount() {
                $count = 0;
                foreach ($_SESSION['cart'] as $quantity) {
                    $count += $quantity;
                }
                return $count;
            }
            */
        ?>
        <!-- Сетка товаров мерча (Позже переделать под выгрузку из БД вместо ручного написания.) -->
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" 
                            onerror="this.src="<?php echo urlencode($product['name']); ?>'">
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
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
        // заглушка для фильтров
        document.querySelectorAll('.catalog-filters select').forEach(select => {
            select.addEventListener('change', function() {
                console.log(`Фильтр изменен: ${this.id} = ${this.value}`);
            });
        });
        
        // Обработка поиска
        document.querySelector('.search-box button').addEventListener('click', function() {
            const searchInput = this.parentElement.querySelector('input');
            const searchTerm = searchInput.value.trim();
            if (searchTerm !== '') {
                alert(`Поиск мерча: "${searchTerm}"\n\n(В реальном приложении здесь будет поиск по каталогу)`);
            }
        });
        
        // Обработка добавления в корзину
        document.querySelectorAll('.btn-primary').forEach(button => {
            if (button.textContent === 'В корзину') {
                button.addEventListener('click', function() {
                    const productTitle = this.closest('.item-info').querySelector('.item-title').textContent;
                    alert(`Товар "${productTitle}" добавлен в корзину!`);
                });
            }
        });
    </script>
</body>
</html>
<?php
include("blocks/ending.php");
?>