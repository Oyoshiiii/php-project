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
                        <!--статусы будут добавлены здесь-->
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
            <div class="search-box" id="searchInput">
                <input type="text" placeholder="Поиск манги..." id="search-input">
                <button id="search-btn">Найти</button>
            </div>
        </div>

        <div>
            <div id="manga-container" class="manga-catalog">
                <!--манга будет загружена здесь-->
            </div>

            <div id="pagination" class="pagination">
            <!-- Кнопки пагинации будут добавлены через JavaScript -->
            </div>

            <!--детальная инфа о манге-->
            <div id="mangaDetail" class="manga-detail">
            <div class="manga-detail-overlay"></div>
            <div class="manga-detail-modal">
                <button class="close-btn" id="closeDetail">&times;</button>
                <div class="manga-detail-content" id="mangaDetailContent">
                    <!-- Детали манги будут отображаться здесь -->
                </div>
            </div>
        </div>
        </div>
    </div>

    <!--подключение скриптов-->
    <script src="js/read-manga.js"></script>
    <script src="js/manga-catalog.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM загружен, инициализация каталога...');
            
            initializeGenreFilter();
            initializeStatusFilter();
            
            if (typeof mangaCatalog !== 'undefined') {
                mangaCatalog.setupEventListeners();
                mangaCatalog.loadPopularManga('manga-container', 12);
            } else {
                console.error('mangaCatalog не инициализирован');
            }
            
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

        //инициализация фильтра статусов
        function initializeStatusFilter(){
            const statusSelect = document.getElementById('status');

            //очищаем существующие опции (кроме "Любой статус")
            while (statusSelect.children.length > 1) {
                statusSelect.removeChild(statusSelect.lastChild);
            }

            if (typeof MANGA_STATUS_TRANSLATED !== 'undefined') {
                Object.keys(MANGA_STATUS_TRANSLATED).forEach(statusKey => {
                    const option = document.createElement('option');
                    option.value = statusKey;
                    option.textContent = MANGA_STATUS_TRANSLATED[statusKey];
                    statusSelect.appendChild(option);
                });
            } else {
                console.error('MANGA_STATUS_TRANSLATED не определен');
            }
        }

        function setupFilters(){
            const genresSelected = document.getElementById('genre');

            genresSelected.addEventListener('change', function(){
                const genre = this.value;
                console.log('Выбран жанр:', genre);
                if(genre){
                    mangaCatalog.currentFilters.genres = [genre];
                    mangaCatalog.currentFilters.status = document.getElementById('status').value;
                    mangaCatalog.currentFilters.year = document.getElementById('year').value;
                    mangaCatalog.applyFilters('manga-container');
                }
                else{
                    mangaCatalog.loadPopularManga('manga-container', 12);
                }
            });

            const search = document.getElementById('search-btn');

            search.addEventListener('click', function(){
                const searchInput = document.getElementById('search-input');
                const searchInputValue = searchInput.value.trim();
                console.log('Поиск:', searchInputValue);
                if(searchInputValue !== ''){
                    mangaCatalog.currentFilters.search = searchInputValue;
                    mangaCatalog.applyFilters('manga-container');
                }
            });

            const statusSelected = document.getElementById('status');

            statusSelected.addEventListener('change', function() {
                console.log('Статус изменен:', this.value);
                mangaCatalog.currentFilters.status = this.value;
                mangaCatalog.currentFilters.genres = document.getElementById('genre').value ? [document.getElementById('genre').value] : [];
                mangaCatalog.currentFilters.year = document.getElementById('year').value;
                mangaCatalog.applyFilters('manga-container');
            });

            document.getElementById('year').addEventListener('change', function() {
                const selectedYear = this.value;
                console.log('Год изменен:', selectedYear);
                
                let rangeDescription = '';
                switch(selectedYear) {
                    case "2020":
                        rangeDescription = "2020+ года";
                        break;
                    case "2010":
                        rangeDescription = "2010-2019 года";
                        break;
                    case "2000":
                        rangeDescription = "2000-2009 года";
                        break;
                    case "1990":
                        rangeDescription = "1990-1999 года";
                        break;
                    case "1980":
                        rangeDescription = "1980-1989 года";
                        break;
                    default:
                        rangeDescription = "все года";
                }
                console.log('Ищем мангу за:', rangeDescription);
                
                mangaCatalog.currentFilters.year = selectedYear;
                mangaCatalog.currentFilters.genres = document.getElementById('genre').value ? [document.getElementById('genre').value] : [];
                mangaCatalog.currentFilters.status = document.getElementById('status').value;
                mangaCatalog.applyFilters('manga-container');
        });
    }
    </script>
</body>
</html>

<?php
include("blocks/ending.php");
?>