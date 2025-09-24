<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php date_default_timezone_set('Asia/Krasnoyarsk'); ?>
    <!--<header>
        <nav>
            <a href="index.php">Домашняя</a>
            <a href="about.php">О нас</a>
            <a href="manga-catalog.php">Каталог манги</a>
            <a href="merch-catalog.php">Каталог мерча</a>
            <a href="shopping-cart.php" id="shopping-cart">
                <img src="images/shopping cart.png" id="shopping-cart-img">
            </a>
        </nav>
    </header>-->

    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span class="logo-icon">🎌</span>
                    <span>MangaMerchHub</span>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="merch-catalog.php">Магазин</a></li>
                        <li><a href="manga-catalog.php">Манга</a></li>
                        <li><a href="#">Энциклопедия</a></li>
                        <li><a href="#">Новости</a></li>
                        <li><a href="about.php">О нас</a></li>
                    </ul>
                </nav>
                <div class="user-actions">
                    <button class="btn btn-outline">Войти</button>
                    <button class="btn btn-primary">Корзина (0)</button>
                </div>
            </div>
        </div>
    </header>

    <main></main>