<?php
$title = "Каталог манги";
require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог манги - AnimeManga Store</title>
    <link rel="stylesheet" href="css/manga-catalog.css">
</head>
<body>

    <div class="container">
        <!-- Фильтры для манги -->
        <div class="catalog-filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="genre">Жанр</label>
                    <select id="genre">
                        <option value="">Все жанры</option>
                        <option value="shonen">Сёнэн</option>
                        <option value="shoujo">Сёдзё</option>
                        <option value="seinen">Сэйнэн</option>
                        <option value="fantasy">Фэнтези</option>
                        <option value="romance">Романтика</option>
                        <option value="action">Экшен</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status">Статус</label>
                    <select id="status">
                        <option value="">Любой статус</option>
                        <option value="ongoing">Публикуется</option>
                        <option value="completed">Завершена</option>
                        <option value="hiatus">На паузе</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="year">Год выпуска</label>
                    <select id="year">
                        <option value="">Любой год</option>
                        <option value="2020">2020+</option>
                        <option value="2010">2010-2019</option>
                        <option value="2000">2000-2009</option>
                        <option value="1990">1990-1999</option>
                        <option value="1980">1980-1989</option>
                    </select>
                </div>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Поиск манги...">
                <button>Найти</button>
            </div>
        </div>
        <div>
            <style>
                

        .genre-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .genre-btn {
            padding: 8px 15px;
            background-color: #0f3460;
            color: #e6e6e6;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .genre-btn:hover {
            background-color: #16213e;
        }

        .genre-btn.active {
            background-color: #e94560;
        }

        .manga-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .manga-card {
            background-color: #16213e;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .manga-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .manga-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }

        .manga-card-content {
            padding: 15px;
        }

        .manga-card h3 {
            font-size: 1rem;
            margin-bottom: 8px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .manga-card p {
            font-size: 0.9rem;
            color: #b8b8b8;
            margin-bottom: 5px;
        }

        .score {
            color: #e94560;
            font-weight: bold;
        }

        .loading {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #e94560;
        }

        .error {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #ff6b6b;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #b8b8b8;
            grid-column: 1 / -1;
        }

        .load-more-btn {
            display: block;
            margin: 30px auto;
            padding: 12px 25px;
            background-color: #e94560;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .load-more-btn:hover {
            background-color: #ff6b6b;
        }

        .manga-detail {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            overflow-y: auto;
            display: none;
        }

        .manga-detail.active {
            display: block;
        }

        .manga-detail-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
        }

        .manga-detail-header {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .manga-poster {
            flex: 0 0 250px;
        }

        .manga-poster img {
            width: 100%;
            border-radius: 10px;
        }

        .manga-header-info {
            flex: 1;
            min-width: 300px;
        }

        .manga-header-info h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .english-title {
            font-style: italic;
            color: #b8b8b8;
            margin-bottom: 15px;
        }

        .manga-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .manga-stats span {
            background-color: #0f3460;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .manga-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .manga-genres {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .genre-tag {
            background-color: #e94560;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .btn-read-manga, .btn-anilist {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            background-color: #e94560;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-anilist {
            background-color: #0f3460;
        }

        .btn-read-manga:hover, .btn-anilist:hover {
            background-color: #ff6b6b;
        }

        .manga-description {
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .manga-characters h2 {
            margin-bottom: 15px;
        }

        .characters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
        }

        .character-card {
            text-align: center;
        }

        .character-card img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .character-card p {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .manga-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .manga-detail-header {
                flex-direction: column;
            }

            .manga-poster {
                flex: 0 0 auto;
                max-width: 250px;
                margin: 0 auto;
            }
        }
            </style>
            
            <div id="mangaContainer" class="manga-grid"> <!--Позже доделаю, пока просто проверка-->

            </div>

            <div id="mangaDetail" class="manga-detail">
                <button class="close-btn" id="closeDetail">&times;</button>
                <div class="manga-detail-content" id="mangaDetailContent">
                    <!-- Детали манги будут отображаться здесь -->
                </div>
            </div>

            <script src="js/manga-catalog.js"></script>
            <script> 
            document.addEventListener('DOMContentLoaded', () => {
                mangaCatalog.init();
            });
            </script>

        </div>
    </div>
    <script>
        // заглушка для фильтров
        document.querySelectorAll('.catalog-filters select').forEach(select => {
            select.addEventListener('change', function() {
                // В реальном приложении здесь будет фильтрация манги
                console.log(`Фильтр изменен: ${this.id} = ${this.value}`);
            });
        });
        
        // Обработка поиска
        document.querySelector('.search-box button').addEventListener('click', function() {
            const searchInput = this.parentElement.querySelector('input');
            const searchTerm = searchInput.value.trim();
            if (searchTerm !== '') {
                alert(`Поиск манги: "${searchTerm}"\n\n(В реальном приложении здесь будет поиск по каталогу)`);
            }
        });
        
        // Обработка добавления в корзину
        document.querySelectorAll('.btn-primary').forEach(button => {
            if (button.textContent === 'В корзину') {
                button.addEventListener('click', function() {
                    const mangaTitle = this.closest('.item-info').querySelector('.item-title').textContent;
                    alert(`Манга "${mangaTitle}" добавлена в корзину!`);
                });
            }
        });
        
        // Обработка кнопки "Подробнее"
        document.querySelectorAll('.btn-outline').forEach(button => {
            if (button.textContent === 'Подробнее') {
                button.addEventListener('click', function() {
                    const mangaTitle = this.closest('.item-info').querySelector('.item-title').textContent;
                    alert(`Открывается страница с подробной информацией о "${mangaTitle}"\n\n(Здесь будет переход на страницу товара)`);
                });
            }
        });
    </script>
</body>
</html>

<?php
include("blocks/ending.php");
?>