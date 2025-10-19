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
    
    <?php
    // ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ
    try {
        $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Ошибка подключения: ". $e->getMessage());
    }

    // Проверяем, авторизован ли пользователь
    $isLoggedIn = isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id']);
    $userAvatar = '';
    $userId = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : '';

    // Получаем аватар из базы данных
    if ($isLoggedIn && $userId) {
        try {
            $stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData && !empty($userData['avatar']) && $userData['avatar'] !== '(NULL)') {
                $userAvatar = $userData['avatar'];
            }
        } catch(PDOException $e) {
            // В случае ошибки просто продолжаем без аватара
            error_log("Ошибка получения аватара: " . $e->getMessage());
        }
    }
    ?>

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
                        <li><a href="about.php">О нас</a></li>
                    </ul>
                </nav>
                <div class="user-actions">
                    <?php if ($isLoggedIn): ?>
                        <!-- Показываем аватар для авторизованных -->
                        <a href="profile.php" class="user-profile-link">
                            <?php if (!empty($userAvatar)): ?>
                                <img src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Аватар" class="user-avatar">
                            <?php else: ?>
                                <div class="default-avatar">
                                    <img src="images/default-avatar.png" alt="Аватар" class="user-avatar">
                                </div>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <!-- Показываем кнопку входа для неавторизованных -->
                        <a href="login.php" class="btn btn-outline">Войти</a>
                    <?php endif; ?>
                    <a href="shopping-cart.php" class="btn btn-primary">Корзина (0)</a>
                </div>
            </div>
        </div>
    </header>

    <main>