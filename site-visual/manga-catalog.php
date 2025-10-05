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
        <div class="catalog-filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="genre">Жанр</label>
                    <select id="genre">
                        <option value="">Все жанры</option>
                        <!--жанры будут добавлены здесь-->
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
                <input type="text" placeholder="Поиск манги..." id="search-input">
                <button id="search-btn">Найти</button>
            </div>
        </div>
        
        <div id="manga-container" class="manga-catalog">
            <!--манга будет загружена здесь-->
        </div>

        <!--детальная инфа о манге-->
        <div id="mangaDetail" class="manga-detail" style="display: none;">
            <button class="close-btn" id="closeDetail">&times;</button>
            <div class="manga-detail-content" id="mangaDetailContent">
                <!--детали манги будут отображаться здесь-->
            </div>
        </div>
    </div>

    <!--подключение скриптов-->
    <script src="js/manga-catalog.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            console.log('DOM загружен, инициализация каталога...');
            //инициализация жанров в фильтре
            initializeGenreFilter();
            
            //настройка обработчиков
            mangaCatalog.setupEventListeners();
            
            //загрузка популярной манги
            mangaCatalog.loadPopularManga('manga-container', 12);
            
            setupFilters();
        });

        //инициализация фильтра жанров
        function initializeGenreFilter() {
            const genreSelect = document.getElementById('genre');
            
            //очищаем существующие опции (кроме "Все жанры")
            while (genreSelect.children.length > 1) {
                genreSelect.removeChild(genreSelect.lastChild);
            }
            
            //добавляем жанры из MANGA_GENRES_TRANSLATED
            if (typeof MANGA_GENRES_TRANSLATED !== 'undefined') {
                Object.keys(MANGA_GENRES_TRANSLATED).forEach(genreKey => {
                    const option = document.createElement('option');
                    option.value = genreKey;
                    option.textContent = MANGA_GENRES_TRANSLATED[genreKey];
                    genreSelect.appendChild(option);
                });
            } else {
                console.error('MANGA_GENRES_TRANSLATED не определен');
            }
        }

        function setupFilters(){
            const genresSelected = document.getElementById('genre');
            genresSelected.addEventListener('change', function(){
                const genre = this.value;
                console.log('Выбран жанр:', genre);
                if(genre){
                    mangaCatalog.loadByGenres('manga-container', [genre]);
                }
                else{
                    mangaCatalog.loadPopularManga('manga-container', 12);
                }
            });

            //добавляем отладку для поиска
            const search = document.getElementById('search-btn');

            search.addEventListener('click', function(){
                const searchInput = document.getElementById('search-input');
                const searchInputValue = searchInput.value.trim();
                console.log('Поиск:', searchInputValue);
                if(searchInputValue !== ''){
                    mangaCatalog.searchManga('manga-container', searchInputValue);
                }
            });

            //заглушки для нереализованных фильтров
            document.getElementById('status').addEventListener('change', function() {
                console.log('Статус изменен:', this.value);
                alert('Фильтр по статусу пока не реализован');
            });

            document.getElementById('year').addEventListener('change', function() {
                console.log('Год изменен:', this.value);
                alert('Фильтр по году пока не реализован');
            });
        }
    </script>
</body>
</html>

<?php
include("blocks/ending.php");
?>