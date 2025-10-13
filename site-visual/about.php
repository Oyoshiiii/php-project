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
            /* ====== Общие стили ====== */
            body {
                font-family: "Inter", sans-serif;
                background-color: #1e1e1e;
                color: #e6e6e6;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 60px 20px 80px 20px;
            }

            /* ====== Заголовки ====== */
            h1, h2, h3 {
                color: #ffffff;
                margin-bottom: 12px;
            }

            h1 {
                font-size: 2.2rem;
                text-align: center;
                margin-bottom: 24px;
            }

            h2 {
                font-size: 1.6rem;
                margin-top: 40px;
                border-left: 4px solid #8b5cf6;
                padding-left: 10px;
            }

            p {
                line-height: 1.6;
            }

            /* ====== Блок “О компании” ====== */
            .about-header {
                text-align: center;
                margin-bottom: 40px;
            }

            .about-header p {
                max-width: 800px;
                margin: 0 auto;
                font-size: 1.1rem;
                color: #d0d0d0;
            }

            /* ====== История ====== */
            .about-section {
                background-color: #262626;
                border-radius: 16px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.4);
                padding: 40px;
                margin-bottom: 40px;
            }

            .about-image {
                text-align: center;
                font-size: 2rem;
                margin-top: 20px;
            }

            /* ====== Ценности ====== */
            .values-section {
                margin-top: 40px;
            }

            .values-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 24px;
                margin-top: 20px;
            }

            .value-card {
                background-color: #2f2f2f;
                border-radius: 12px;
                padding: 24px;
                text-align: center;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .value-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 6px 18px rgba(0,0,0,0.35);
            }

            .value-icon {
                font-size: 2rem;
                margin-bottom: 10px;
            }

            /* ====== Команда ====== */
            .team-section {
                margin-top: 60px;
            }

            .team-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 24px;
                margin-top: 20px;
            }

            .team-member {
                display: flex;
                align-items: center;
                background-color: #2f2f2f;
                border-radius: 12px;
                padding: 16px 20px;
                gap: 16px;
                box-shadow: 0 3px 10px rgba(0,0,0,0.3);
                transition: transform 0.2s ease;
            }

            .team-member:hover {
                transform: translateY(-3px);
            }

            .member-photo {
                font-size: 2.4rem;
            }

            .member-info h3 {
                margin: 0;
                font-size: 1.1rem;
            }

            .member-info p {
                color: #b5b5b5;
                margin: 4px 0 0 0;
            }

            /* ====== Адаптивность ====== */
            @media (max-width: 600px) {
                .container {
                    padding: 40px 16px;
                }

                h1 {
                    font-size: 1.8rem;
                }

                .team-member {
                    flex-direction: column;
                    text-align: center;
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