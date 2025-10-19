<?php
$title = "Корзина товаров";
require("blocks/header.php");
?>

<?php
/*
$database = new Database();
$pdo = $database->getConnection();

// Обработка действий с корзиной
if ($_POST) {
    if (isset($_POST['remove'])) {
        removeFromCart($_POST['product_id']);
    } elseif (isset($_POST['update'])) {
        updateCart($_POST['product_id'], $_POST['quantity']);
    }
    header("Location: cart.php");
    exit();
}

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
    */
?>

<h2>Корзина покупок</h2>

<?php if (empty($cart_items)): ?>
    <p>Ваша корзина пуста.</p>
<?php else: ?>
    <div class="cart-items">
        <table>
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
                            <img src="images/<?php echo $item['product']['image']; ?>" 
                                 alt="<?php echo $item['product']['name']; ?>"
                                 onerror="this.src="<?php echo urlencode($item['product']['name']); ?>'">
                            <span><?php echo htmlspecialchars($item['product']['name']); ?></span>
                        </div>
                    </td>
                    <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                    <td>
                        <form method="post" class="quantity-form">
                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                            <button type="submit" name="update" class="update-btn">Обновить</button>
                        </form>
                    </td>
                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                            <button type="submit" name="remove" class="remove-btn">Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-total">
            <h3>Общая сумма: $<?php echo number_format($total, 2); ?></h3>
            <a href="checkout.php" class="checkout-btn">Оформить заказ</a>
        </div>
    </div>
<?php endif; ?>

<?php
include("blocks/ending.php");
?>