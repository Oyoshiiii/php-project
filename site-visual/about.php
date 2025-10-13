<?php
$title = "О нас";
require("blocks/header.php");
?>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AnimeManga Store - Магазин мерча и энциклопедия манги</title>
        <link rel="stylesheet" href="css/index.css" >
        <style>
        :root {
            --primary: #ff6b9d;
            --secondary: #5d5fef;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --accent: #ffd166;
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --card-bg: #1e1e1e;
            --header-bg: #0a0a0a;
            --footer-bg: #050505;
            --border-color: #333;
            --shadow: 0 4px 15px rgba(0,0,0,0.3);
            --muted-text: #aaa;
            --section-bg: #181818;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 15px;
        }

        .about-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .about-header h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .about-header p {
            font-size: 1.2rem;
            color: var(--muted-text);
            max-width: 800px;
            margin: 0 auto;
        }

        .about-section {
            margin-bottom: 60px;
        }

        .about-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--text-color);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
            display: inline-block;
        }

        .about-section p {
            margin-bottom: 20px;
            color: var(--muted-text);
            line-height: 1.8;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
            margin: 40px 0;
        }

        .values-section {
            margin-bottom: 60px;
        }

        .values-section h2 {
            font-size: 2rem;
            margin-bottom: 40px;
            color: var(--text-color);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
            display: inline-block;
        }

        .values-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .values-table th {
            background-color: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.2rem;
        }

        .values-table td {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            color: var(--muted-text);
        }

        .values-table tr:last-child td {
            border-bottom: none;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .value-card {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s;
        }

        .value-card:hover {
            transform: translateY(-5px);
        }

        .value-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .value-card h3 {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        .value-card p {
            color: var(--muted-text);
        }

        .team-section {
            margin-bottom: 60px;
        }

        .team-section h2 {
            font-size: 2rem;
            margin-bottom: 40px;
            color: var(--text-color);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
            display: inline-block;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .team-member {
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .member-photo {
            height: 200px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--secondary);
        }

        .member-info {
            padding: 20px;
        }

        .member-info h3 {
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .member-info p {
            color: var(--muted-text);
        }

        @media (max-width: 768px) {
            .about-header h1 {
                font-size: 2rem;
            }
            
            .about-header p {
                font-size: 1rem;
            }
            
            .values-table {
                display: block;
                overflow-x: auto;
            }
        }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="about-header">
                <h1>О MangaMerchHub</h1>
                <p>Мы — ваш надежный проводник в мир аниме и манги, предлагая качественный мерч и аксессуары для настоящих поклонников японской культуры.</p>
            </div>

            <div class="about-section">
                <div class="about-text">
                    <h2>Наша история</h2>
                    <p>MangaMerchHub был основан в 2025 году группой энтузиастов, которые заметили, как сложно найти качественные товары по любимым аниме и манге в одном месте. Мы начинаем с небольшого онлайн-магазина, предлагая ограниченный ассортимент футболок и фигурок. В будущем мы собираемся развиться до крупного маркетплейса </p>
                <div class="about-image">
                    <!-- Здесь будет изображение -->
                    🎌
                </div>
            </div>

            <div class="values-section">
                <h2>Наши ценности</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">🎯</div>
                        <h3>Аутентичность</h3>
                        <p>Мы предлагаем только лицензионную продукцию от официальных производителей и дистрибьюторов.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">❤️</div>
                        <h3>Страсть к аниме</h3>
                        <p>Мы сами являемся поклонниками аниме и понимаем, что важно для сообщества.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">🚚</div>
                        <h3>Быстрая доставка</h3>
                        <p>Мы стремимся доставить ваш заказ как можно быстрее, независимо от вашего местоположения.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">👥</div>
                        <h3>Сообщество</h3>
                        <p>Мы создаем пространство, где фанаты могут делиться своей любовью к аниме и манге.</p>
                    </div>
                </div>
            </div>

            <div class="team-section">
                <h2>Наша команда</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-photo">👨‍💼</div>
                        <div class="member-info">
                            <h3>Даня Авдеев</h3>
                            <p>Тим-лид и тех-лид</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="member-photo">👩‍💼</div>
                        <div class="member-info">
                            <h3>Ксюша Тарадаева</h3>
                            <p>API, backend</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="member-photo">👨‍🎨</div>
                        <div class="member-info">
                            <h3>Тоха Плясов</h3>
                            <p>БД, backend</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
include("blocks/ending.php");
?>