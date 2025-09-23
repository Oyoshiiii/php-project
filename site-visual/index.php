<?php
$title = "Домашняя страница";
require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimeManga Store - Магазин мерча и энциклопедия манги</title>
    <link rel="stylesheet" href="css/index.css">
    <!--
    надо наверное поудалять лишние стили в styles.css, которые уже есть в index.css
    либо их скопировать и такими же сделать под остальные страницы
    -->
</head>
<body>
    <!-- Шапка сайта 
     может вынесем этот header в header.php? зачем нам два хедера
     -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span class="logo-icon">🎌</span>
                    <span>AnimeManga Store</span>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="merch-catalog.php">Магазин</a></li>
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

    <!-- Герой-секция -->
    <section class="hero">
        <div class="container">
            <h1>Аниме-мерч и энциклопедия манги в одном месте</h1>
            <p>Покупайте эксклюзивные товары с любимыми персонажами и узнавайте больше о вселенных аниме и манги</p>
            <button class="btn btn-primary">Перейти к покупкам</button>
        </div>
    </section>

    <!-- Секция категорий -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Категории товаров</h2>
            <div class="category-grid">
                <div class="category-card">
                    <div class="category-img">👕</div>
                    <div class="category-info">
                        <h3>Футболки и одежда</h3>
                        <p>Стильная одежда с принтами популярных аниме и манги</p>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">🎎</div>
                    <div class="category-info">
                        <h3>Фигурки и коллекции</h3>
                        <p>Детализированные фигурки персонажей для настоящих фанатов</p>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">🎒</div>
                    <div class="category-info">
                        <h3>Аксессуары</h3>
                        <p>Рюкзаки, брелоки, значки и другие аксессуары</p>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">📚</div>
                    <div class="category-info">
                        <h3>Манга и артбуки</h3>
                        <p>Оригинальная манга и коллекционные издания</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Секция популярных товаров -->
    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">Популярные товары</h2>
            <div class="product-grid">
                <div class="product-card">
                    <div class="product-img">👘</div>
                    <div class="product-info">
                        <h3 class="product-title">Футболка "Наруто"</h3>
                        <p class="product-price">1 499 ₽</p>
                        <button class="btn btn-primary">В корзину</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-img">🔮</div>
                    <div class="product-info">
                        <h3 class="product-title">Фигурка Сейлор Мун</h3>
                        <p class="product-price">3 299 ₽</p>
                        <button class="btn btn-primary">В корзину</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-img">🎭</div>
                    <div class="product-info">
                        <h3 class="product-title">Брелок "Маска Саске"</h3>
                        <p class="product-price">599 ₽</p>
                        <button class="btn btn-primary">В корзину</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-img">👓</div>
                    <div class="product-info">
                        <h3 class="product-title">Кепка "Токийский гуль"</h3>
                        <p class="product-price">1 199 ₽</p>
                        <button class="btn btn-primary">В корзину</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Секция информации о манге -->
    <section class="manga-info">
        <div class="container">
            <h2 class="section-title">Узнайте больше о манге</h2>
            <div class="manga-search">
                <input type="text" placeholder="Найдите мангу или персонажа...">
                <button>Поиск</button>
            </div>
            <div class="manga-grid">
                <div class="manga-card">
                    <div class="manga-img">📖</div>
                    <div class="manga-details">
                        <h3 class="manga-title">Наруто</h3>
                        <p class="manga-meta">Масаси Кисимото • Сёнэн</p>
                        <p>История о мальчике-ниндзя, мечтающем стать Хокагэ своей деревни.</p>
                    </div>
                </div>
                <div class="manga-card">
                    <div class="manga-img">📖</div>
                    <div class="manga-details">
                        <h3 class="manga-title">Атака титанов</h3>
                        <p class="manga-meta">Хадзимэ Исаяма • Сёнэн</p>
                        <p>Человечество борется за выживание против гигантских титанов.</p>
                    </div>
                </div>
                <div class="manga-card">
                    <div class="manga-img">📖</div>
                    <div class="manga-details">
                        <h3 class="manga-title">Ван-Пис</h3>
                        <p class="manga-meta">Эйитиро Ода • Сёнэн</p>
                        <p>Приключения пирата Манки Д. Луффи и его команды.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Секция преимуществ -->
    <section class="benefits">
        <div class="container">
            <h2 class="section-title">Почему выбирают нас</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">🚚</div>
                    <h3>Быстрая доставка</h3>
                    <p>Доставляем заказы по всей России за 3-7 дней</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">✅</div>
                    <h3>Официальный мерч</h3>
                    <p>Все товары лицензированы и высокого качества</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">💬</div>
                    <h3>Сообщество фанатов</h3>
                    <p>Присоединяйтесь к нашему активному сообществу</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🔍</div>
                    <h3>Энциклопедия манги</h3>
                    <p>Узнавайте больше о любимых произведениях</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Подвал 
     его можно наоборот уже в ending.php
     -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Магазин</h3>
                    <ul>
                        <li><a href="#">Футболки и одежда</a></li>
                        <li><a href="#">Фигурки</a></li>
                        <li><a href="#">Аксессуары</a></li>
                        <li><a href="#">Манга и книги</a></li>
                        <li><a href="#">Распродажа</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Информация</h3>
                    <ul>
                        <li><a href="#">О нас</a></li>
                        <li><a href="#">Доставка и оплата</a></li>
                        <li><a href="#">Возврат и обмен</a></li>
                        <li><a href="#">Контакты</a></li>
                        <li><a href="#">Блог</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Энциклопедия</h3>
                    <ul>
                        <li><a href="#">Персонажи</a></li>
                        <li><a href="#">Манга</a></li>
                        <li><a href="#">Аниме</a></li>
                        <li><a href="#">Авторы</a></li>
                        <li><a href="#">Жанры</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Контакты</h3>
                    <p>Email: info@animemangastore.ru</p>
                    <p>Телефон: +7 (999) 999-99-99</p>
                    <div class="social-links">
                        <a href="#" class="social-icon">VK</a>
                        <a href="#" class="social-icon">TG</a>
                        <a href="#" class="social-icon">YT</a>
                        <a href="#" class="social-icon">IG</a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 AnimeManga Store. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script>
        // API (заглушка)
        document.querySelector('.manga-search button').addEventListener('click', function() {
            const searchTerm = document.querySelector('.manga-search input').value;
            if (searchTerm.trim() !== '') {
                alert(`Поиск информации о: "${searchTerm}"\n\n(В реальном приложении здесь будет запрос к API манги)`);
                // Здесь будет реальный запрос к API
                // fetch(`https://api.manga.info/search?q=${encodeURIComponent(searchTerm)}`)
                //   .then(response => response.json())
                //   .then(data => displayMangaResults(data));
            }
        });
        
        // Функция для отображения результатов поиска (заглушка)
        function displayMangaResults(data) {
            // Реализация отображения результатов поиска
            console.log('Результаты поиска:', data);
        }
    </script>
</body>
</html>

<?php
include("blocks/ending.php");
?>