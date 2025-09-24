<?php
$title = "Каталог мерча";
require("blocks/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог мерча - MangaMerchHub</title>
    <link rel="stylesheet" href="css/merch-catalog.css">
</head>
<body>
 
    <div class="container">
        <!-- Фильтры для мерча -->
        <div class="catalog-filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="category">Категория</label>
                    <select id="category">
                        <option value="">Все категории</option>
                        <option value="clothes">Одежда</option>
                        <option value="figures">Фигурки</option>
                        <option value="accessories">Аксессуары</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="series">Аниме/Манга</label>
                    <select id="series">
                        <option value="">Все серии</option>
                        <option value="naruto">Наруто</option>
                        <option value="onepiece">Ван Пис</option>
                        <option value="attackontitan">Атака титанов</option>
                        <option value="sailormoon">Сейлор Мун</option>
                        <option value="myheroacademia">Моя геройская академия</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price">Цена</label>
                    <select id="price">
                        <option value="">Любая цена</option>
                        <option value="0-1000">До 1000 руб.</option>
                        <option value="1000-3000">1000 - 3000 руб.</option>
                        <option value="3000-5000">3000 - 5000 руб.</option>
                        <option value="5000+">От 5000 руб.</option>
                    </select>
                </div>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Поиск товаров...">
                <button>Найти</button>
            </div>
        </div>

        <!-- Сетка товаров мерча (Позже переделать под выгрузку из БД вместо ручного написания.) -->
        <div class="catalog-grid">
            <!-- Товар 1 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #ffe6ee;">👕</div>
                <div class="item-info">
                    <h3 class="item-title">Футболка "Наруто" с символом Конохи</h3>
                    <p class="item-meta">Наруто • Одежда</p>
                    <p class="item-price">1 499 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 2 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #e6f7ff;">🎎</div>
                <div class="item-info">
                    <h3 class="item-title">Фигурка Сейлор Мун (15 см)</h3>
                    <p class="item-meta">Сейлор Мун • Фигурки</p>
                    <p class="item-price">3 299 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 3 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #f0e6ff;">🎒</div>
                <div class="item-info">
                    <h3 class="item-title">Рюкзак "Атака титанов"</h3>
                    <p class="item-meta">Атака титанов • Аксессуары</p>
                    <p class="item-price">2 599 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 4 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #e6ffe6;">👓</div>
                <div class="item-info">
                    <h3 class="item-title">Кепка "Токийский гуль"</h3>
                    <p class="item-meta">Токийский гуль • Аксессуары</p>
                    <p class="item-price">1 199 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 5 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #fff0e6;">🔑</div>
                <div class="item-info">
                    <h3 class="item-title">Брелок "Маска Саске"</h3>
                    <p class="item-meta">Наруто • Аксессуары</p>
                    <p class="item-price">599 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 6 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #e6f0ff;">📿</div>
                <div class="item-info">
                    <h3 class="item-title">Кулон "Драконий жемчуг"</h3>
                    <p class="item-meta">Dragon Ball • Аксессуары</p>
                    <p class="item-price">899 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 7 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #ffe6f0;">🧢</div>
                <div class="item-info">
                    <h3 class="item-title">Кепка "Моя геройская академия"</h3>
                    <p class="item-meta">Моя геройская академия • Одежда</p>
                    <p class="item-price">1 299 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 8 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #f0ffe6;">👘</div>
                <div class="item-info">
                    <h3 class="item-title">Худи "Клинок, рассекающий демонов"</h3>
                    <p class="item-meta">Клинок, рассекающий демонов • Одежда</p>
                    <p class="item-price">2 799 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 9 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #fff2e6;">🥋</div>
                <div class="item-info">
                    <h3 class="item-title">Толстовка "Наруто" с символом Узумаки</h3>
                    <p class="item-meta">Наруто • Одежда</p>
                    <p class="item-price">2 499 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 10 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #e6f2ff;">🗡️</div>
                <div class="item-info">
                    <h3 class="item-title">Фигурка Леви Акермана (20 см)</h3>
                    <p class="item-meta">Атака титанов • Фигурки</p>
                    <p class="item-price">4 299 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 11 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #f0e6ff;">👟</div>
                <div class="item-info">
                    <h3 class="item-title">Кроссовки "Моя геройская академия"</h3>
                    <p class="item-meta">Моя геройская академия • Одежда</p>
                    <p class="item-price">3 999 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
            
            <!-- Товар 12 -->
            <div class="catalog-item">
                <div class="item-img" style="background-color: #e6ffe6;">🎨</div>
                <div class="item-info">
                    <h3 class="item-title">Постер "Сейлор Мун" (А3)</h3>
                    <p class="item-meta">Сейлор Мун • Аксессуары</p>
                    <p class="item-price">799 ₽</p>
                    <div class="item-actions">
                        <button class="btn btn-primary btn-small">В корзину</button>
                        <button class="btn btn-outline btn-small">Подробнее</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Страницы -->
        <div class="pagination">
            <a href="#" class="page-link active">1</a>
            <a href="#" class="page-link">2</a>
            <a href="#" class="page-link">3</a>
            <a href="#" class="page-link">4</a>
            <a href="#" class="page-link">5</a>
            <a href="#" class="page-link">→</a>
        </div>
    </div>

    <script>
        // заглушка для фильтров
        document.querySelectorAll('.catalog-filters select').forEach(select => {
            select.addEventListener('change', function() {
                console.log(`Фильтр изменен: ${this.id} = ${this.value}`);
            });
        });
        
        // Обработка поиска
        document.querySelector('.search-box button').addEventListener('click', function() {
            const searchInput = this.parentElement.querySelector('input');
            const searchTerm = searchInput.value.trim();
            if (searchTerm !== '') {
                alert(`Поиск мерча: "${searchTerm}"\n\n(В реальном приложении здесь будет поиск по каталогу)`);
            }
        });
        
        // Обработка добавления в корзину
        document.querySelectorAll('.btn-primary').forEach(button => {
            if (button.textContent === 'В корзину') {
                button.addEventListener('click', function() {
                    const productTitle = this.closest('.item-info').querySelector('.item-title').textContent;
                    alert(`Товар "${productTitle}" добавлен в корзину!`);
                });
            }
        });
    </script>
</body>
</html>
<?php
include("blocks/ending.php");
?>