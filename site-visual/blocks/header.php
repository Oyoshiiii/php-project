<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php date_default_timezone_set('Asia/Krasnoyarsk'); ?>
    <header>
        <nav>
            <a href="index.php">Домашняя</a>
            <a href="about.php">О нас</a>
            <a href="manga-catalog.php">Каталог манги</a>
            <a href="merch-catalog.php">Каталог мерча</a>
            <a href="shopping-cart.php" id="shopping-cart">
                <img src="images/shopping cart.png" id="shopping-cart-img">
            </a>
        </nav>
    </header>

    <main></main>