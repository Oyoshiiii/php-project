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
                .manga-catalog {
                display: grid; 
                grid-template-columns: repeat(4, 1fr); 
                gap: 1em;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-column-gap: 0px;
                grid-row-gap: 0px;
                }

                .manga-catalog > div {
                background-color: #c25f5fff; 
                padding: 1em;
                text-align: center;
                }
            </style>
            <div class="manga-catalog"> <!--Позже доделаю, пока просто проверка-->
                <div>test</div>
                <div>test</div>
                <div>test</div>
                <div>test</div>
                <div>test</div>
            </div>
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