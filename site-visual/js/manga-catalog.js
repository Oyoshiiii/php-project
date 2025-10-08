//жанры манги с переводом
const MANGA_GENRES_TRANSLATED = {
    "Action": "Экшен",
    "Adventure": "Приключения",
    "Comedy": "Комедия",
    "Drama": "Драма",
    "Ecchi": "Этти",
    "Fantasy": "Фэнтези",
    "Horror": "Ужасы",
    "Mahou Shoujo": "Махо-сёдзё",
    "Mecha": "Меха",
    "Music": "Музыка",
    "Mystery": "Мистика",
    "Psychological": "Психологическое",
    "Romance": "Романтика",
    "Sci-Fi": "Научная фантастика",
    "Slice of Life": "Повседневность",
    "Sports": "Спорт",
    "Supernatural": "Сверхъестественное",
    "Thriller": "Триллер"
};

//жанры манги для каталога
const MANGA_GENRES = Object.keys(MANGA_GENRES_TRANSLATED);

//все ответы будут на английском языке, русского перевода в api нет
const ANILIST_API = "https://graphql.anilist.co"; //url для post запросов

//асинхронная функция для работы с Anilist через graphQL
async function graphQLRequest(query, variables = {}){
    try{
        //формирование ответа
        const response = await fetch(ANILIST_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                query: query,
                variables: variables
            })
        })

        if(!response.ok){
            throw new Error(`Ошибка HTTP, статус: ${response.status}`);
        }

        //преобразование данных ответа в json
        const data = await response.json();

        if(data.errors){
            console.error('Ошибки GraphQL:', data.errors);
            throw new Error(data.errors[0].message);
        }

        return data;
    }
    catch(error){
        console.error("Ошибка с запросом к API", error);
        throw error;
    }
}

//заготовки запросов
const MANGA_QUERIES = {
    //по популярности
    popular: `
        query ($perPage: Int) {
            Page (perPage: $perPage) {
                media (type: MANGA, sort: POPULARITY_DESC) {
                    id
                    title { 
                        romaji
                        english
                    }
                    description
                    averageScore
                    coverImage { large }
                    genres
                    status
                }
            }
        }
    `,
    //по жанрам
    /*
    в запрос передаются:
        массив строк (массив с жанрами, которые выбрал пользователь)
        номер страницы и кол-во элементов на странице (для пагинации, чтобы пользователь не ждал,
                                                        пока прогрузятся все полученные манги по
                                                        данной категории, а сам подгружал дополнительно
                                                        еще мангу, если ему нужно больше вариантов)
    Page - объект пагинации в Anilist, с которым формируется запрос
    далее мы указываем, что хотим получить в ответе:
        мангу
        с жанрами $genres
        и отсортированную по популярности
    название манги возвращается на английском языке и ромадзи
    */
    byGenres: 
    `
        query ($genres: [String], $page: Int, $perPage: Int) {
            Page (page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                }
                media (type: MANGA, genre_in: $genres, sort: POPULARITY_DESC) {
                    id
                    title { 
                        romaji
                        english
                    }
                    description
                    averageScore
                    coverImage { large }
                    genres
                    status
                }
            }
        }
    `,
    
    //поиск по названию
    /*
    запрос строится аналогично, только уже передается строка, а не массив
    пагинация остается такой же
    получение ответа происходит такое же, как и в поиске по категориям
    */
    search: 
    `
        query ($search: String, $page: Int, $perPage: Int) {
            Page (page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                }
                media (type: MANGA, search: $search, sort: POPULARITY_DESC) {
                    id
                    title {
                        romaji
                        english
                    }
                    description
                    averageScore
                    coverImage { large }
                    genres
                    status
                }
            }
        }
    `,
    //запрос для поиска подробной информации по манге, 
    //для вывода после нажатия на иконку с нужной мангой
    details:
    `
        query ($id: Int) {
            Media (id: $id, type: MANGA) {
                id
                title { 
                    romaji
                    english
                }
                description
                averageScore
                meanScore
                popularity
                coverImage { 
                    large
                    extraLarge
                    color
                }
                bannerImage
                genres
                tags {
                    name
                    description
                    rank
                }
                status
                chapters
                volumes
                format
                startDate {
                    year
                    month
                    day
                }
                endDate {
                    year
                    month
                    day
                }
                siteUrl
                characters (perPage: 10, sort: ROLE) {
                    nodes {
                        name {
                            full
                            native
                        }
                        image {
                            large
                        }
                    }
                }
            }
        }
    `
};

//класс для работы с каталогом манги
class MangaCatalog{
    constructor() {
        this.genres = [];
        this.currentPage = 1;
        this.currentSearch = '';
        this.isSearching = false;
    }
    //настройка обработчика событий
    setupEventListeners() {
        const searchInput = document.getElementById('search-input');
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.currentSearch = e.target.value;
                this.currentPage = 1;
                this.isSearching = true;
                
                if (this.currentSearch.trim() === '') {
                    this.isSearching = false;
                    this.loadPopularManga('manga-container');
                } else {
                    this.searchManga('manga-container', this.currentSearch);
                }
            }, 500);
        });

        document.getElementById('closeDetail').addEventListener('click', () => {
            document.getElementById('mangaDetail').classList.remove('active');
        });
    }
    //перевод манги
    translateGenre(genre){
        return MANGA_GENRES_TRANSLATED[genre] || genre;
    }
    //перевод нескольких манг
    translateGenres(genres){
        return genres.map(genre => this.translateGenre(genre));
    }
    //перевод просто всех жанров без фильтров по категориям
    translateAllGenres(){
        return MANGA_GENRES;
    }
    //подгружает еще несколько популярных манг (максимальное кол-во 12) 
    //для общей подборки (не по категориям)
    async loadPopularManga(containerId, maxManga = 12){
        console.log('loadPopularManga called with:', containerId, maxManga);
        const container = document.getElementById(containerId);
        console.log('Container found:', container);
    
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        //класс loading условный, его можно поменять вдальнейшем
        /*
        можно заменить на любое другое отображение, которое покажет пользователю,
        что манга подгружается
        */
        container.innerHTML = '<div class="loading">Загрузка популярной манги...</div>';
        
        try{
            //выполнение graphQL запроса с поиском популярной манги
            const result = await graphQLRequest(MANGA_QUERIES.popular, {
                perPage: maxManga
            });

            if (result && result.data){
                //showMangaGrid - метод отображения манги в контейнере
                this.showMangaGrid(result.data.Page.media, container);
            }
        }
        catch(error){
            //класс error условный, его можно поменять вдальнейшем
            container.innerHTML = '<p class="error">Ошибка загрузки манги</p>';
        }
    }


    //загрузка манги по жанрам
    async loadByGenres(containerId, genres = [], page = 1, perPage = 24) {
        const container = document.getElementById(containerId);
        if (!container) return;

        console.log('loadByGenres called with:', containerId, genres, page);

        try {
            const result = await graphQLRequest(MANGA_QUERIES.byGenres, {
                genres: genres,
                page: page,
                perPage: perPage
            });

            if (result && result.data) {
                if (page === 1) {
                    this.showMangaGrid(result.data.Page.media, container);
                } else {
                    const existingManga = container.querySelectorAll('.manga-card');
                    const loadMoreBtn = container.querySelector('.load-more-btn');
                    if (loadMoreBtn) loadMoreBtn.remove();
                    
                    this.showMangaGrid(result.data.Page.media, container, false);
                }
                //настройка пагинации
                this.pagination(result.data.Page.pageInfo, genres, containerId);
            }
        } catch(error) {
            container.innerHTML = '<p class="error">Ошибка загрузки манги</p>';
        }
    }

    //поиск манги
    async searchManga(containerId, searchTerm, page = 1, perPage = 24) {
        const container = document.getElementById(containerId);
        if (!container) return;

        try {
            const result = await graphQLRequest(MANGA_QUERIES.search, {
                search: searchTerm,
                page: page,
                perPage: perPage
            });

        if (result && result.data) {
                if (page === 1) {
                    this.showMangaGrid(result.data.Page.media, container);
                } else {
                    const loadMoreBtn = container.querySelector('.load-more-btn');
                    if (loadMoreBtn) loadMoreBtn.remove();
                    this.showMangaGrid(result.data.Page.media, container, false);
                }
                //настройка пагинации
                this.pagination(result.data.Page.pageInfo, [], containerId, true, searchTerm);
            }
        } catch(error) {
            container.innerHTML = '<p class="error">Ошибка поиска манги</p>';
        }
    }

    //вывод манги в кратком отображении для грида
    //опять же весь визуал условный, его можно менять
    // В классе MangaCatalog исправляем метод showMangaGrid:
    showMangaGrid(mangaList, container, clearContainer = true) {
        if (!mangaList || mangaList.length === 0) {
            if (clearContainer) {
                container.innerHTML = '<p class="no-results">Манга не найдена</p>';
            }
            return;
        }
    
        const mangaHTML = mangaList.map(manga => `
            <div class="manga-card" data-id="${manga.id}">
                <img src="${manga.coverImage.large}" alt="${manga.title.romaji || manga.title.english}">
                <h3>${manga.title.romaji || manga.title.english}</h3>
                <p>⭐ ${manga.averageScore || 'N/A'}/100</p>
                <p>${this.translateGenres(manga.genres).slice(0, 3).join(', ')}</p>
            </div>
        `).join('');

        if (clearContainer) {
            container.innerHTML = mangaHTML;
        } else {
            container.innerHTML += mangaHTML;
        }
    
        //обработчики событий для карточек манги
        container.querySelectorAll('.manga-card').forEach(card => {
            card.addEventListener('click', () => {
                const mangaId = card.getAttribute('data-id');
                this.getMangaDetails(mangaId, 'mangaDetailContent');
            });
        });
    }

    //получение деталей манги после нажатия на иконку с ней
    async getMangaDetails(mangaId, containerId){
        const container = document.getElementById(containerId);
        if(!container) return;

        container.innerHTML = '<div class="loading">Загрузка информации о манге...</div>';
        document.getElementById('mangaDetail').classList.add('active');
        
        try {
            const result = await graphQLRequest(MANGA_QUERIES.details, { id: parseInt(mangaId) });
            
            if (result && result.data) {
                this.displayMangaDetails(result.data.Media, container);
            }
        } catch (error) {
            container.innerHTML = '<p class="error">Ошибка загрузки информации о манге</p>';
        }
    }

    //отображение деталей манги уже на сайте
    displayMangaDetails(manga, container) {
                // Очистка описания от HTML тегов
                const cleanDescription = manga.description 
                    ? manga.description.replace(/<[^>]*>/g, '') 
                    : "Описание отсутствует";
                
                container.innerHTML = `
                    <div class="manga-detail-header">
                        <div class="manga-poster">
                            <img src="${manga.coverImage.extraLarge}" alt="${manga.title.romaji}">
                        </div>
                        <div class="manga-header-info">
                            <h1>${manga.title.romaji || manga.title.english}</h1>
                            ${manga.title.english ? `<p class="english-title">${manga.title.english}</p>` : ''}
                            
                            <div class="manga-stats">
                                <span class="score">⭐ ${manga.averageScore || 'N/A'}/100</span>
                                <span class="popularity">👥 ${manga.popularity || 0}</span>
                                <span class="status">${this.getStatusText(manga.status)}</span>
                            </div>
                            
                            <div class="manga-meta">
                                <span>Глав: ${manga.chapters || 'Неизвестно'}</span>
                                <span>Томов: ${manga.volumes || 'Неизвестно'}</span>
                                <span>Формат: ${this.getFormatText(manga.format)}</span>
                            </div>
                            
                            <div class="manga-genres">
                               ${manga.genres.map(genre => `<span class="genre-tag">${genre}</span>`).join('')}
                            </div>
                            
                            <button class="btn-read-manga" onclick="mangaCatalog.startReading(${manga.id})">
                                📖 Начать читать
                            </button>
                            ${manga.siteUrl ? `<a href="${manga.siteUrl}" target="_blank" class="btn-anilist">🔗 AniList</a>` : ''}
                        </div>
                    </div>
                    
                    <div class="manga-detail-content">
                        <section class="manga-description">
                            <h2>📝 Описание</h2>
                            <p>${cleanDescription}</p>
                        </section>
                        
                        ${manga.characters && manga.characters.nodes.length > 0 ? `
                        <section class="manga-characters">
                            <h2>👥 Персонажи</h2>
                            <div class="characters-grid">
                                ${manga.characters.nodes.slice(0, 6).map(character => `
                                    <div class="character-card">
                                        <img src="${character.image.large}" alt="${character.name.full}">
                                        <p>${character.name.full}</p>
                                    </div>
                                `).join('')}
                            </div>
                        </section>
                        ` : ''}
                    </div>
                `;
            }

    //настройка пагинации
    pagination(pageInfo, genres, containerId, isSearch = false, searchTerm = '') {
        const container = document.getElementById(containerId);
        if (!container || !pageInfo || !pageInfo.hasNextPage) return;
        
        //удаление существующей кнопки "Загрузить еще"
        const existingBtn = container.querySelector('.load-more-btn');
        if (existingBtn) existingBtn.remove();
        
        const loadMoreBtn = document.createElement('button');
        loadMoreBtn.className = 'load-more-btn';
        loadMoreBtn.innerHTML = `Загрузить еще (${pageInfo.currentPage}/${pageInfo.lastPage})`;
        
        //обработчик клика для кнопки "Загрузить еще"
        loadMoreBtn.onclick = () => {
            if (isSearch) {
                this.searchManga(containerId, searchTerm, pageInfo.currentPage + 1);
            } else if (genres.length > 0) {
                this.loadByGenres(containerId, genres, pageInfo.currentPage + 1);
            } else {
                this.loadPopularManga(containerId, 24 * (pageInfo.currentPage + 1));
            }
        };
        
        container.appendChild(loadMoreBtn);
    }

    // Загрузка списка жанров
    async loadGenres() {
        const genreFilter = document.getElementById('genreFilter');
        
        const allBtn = document.createElement('button');
        allBtn.className = 'genre-btn active';
        allBtn.textContent = 'Все';
        allBtn.addEventListener('click', () => {
            document.querySelectorAll('.genre-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            allBtn.classList.add('active');
            
            this.genres = [];
            this.currentPage = 1;
            this.loadPopularManga('mangaContainer');
        });
        genreFilter.appendChild(allBtn);
        
        //кнопки для каждого жанра
        MANGA_GENRES_TRANSLATED.forEach(genre => {
            const genreBtn = document.createElement('button');
            genreBtn.className = 'genre-btn';
            genreBtn.textContent = genre;
            genreBtn.addEventListener('click', () => {
                if (genreBtn.classList.contains('active')) {
                    genreBtn.classList.remove('active');
                    this.genres = this.genres.filter(g => g !== genre);
                } else {
                    genreBtn.classList.add('active');
                    this.genres.push(genre);
                }
                
                if (this.genres.length === 0) {
                    allBtn.classList.add('active');
                } else {
                    allBtn.classList.remove('active');
                }
                
                this.currentPage = 1;
                this.loadByGenres('mangaContainer', this.genres);
            });
            genreFilter.appendChild(genreBtn);
        });
    }

    //перевод статуса манги
    getStatusText(status) {
        const statusMap = {
            'FINISHED': 'Завершена',
            'RELEASING': 'Выходит',
            'NOT_YET_RELEASED': 'Скоро выйдет',
            'CANCELLED': 'Отменена',
            'HIATUS': 'Приостановлена'
        };
        return statusMap[status] || status;
    }

    //перевод формата
    getFormatText(format) {
        const formatMap = {
            'MANGA': 'Манга',
            'NOVEL': 'Новелла',
            'ONE_SHOT': 'Ваншот',
            'DOUJINSHI': 'Додзинси'
        };
        return formatMap[format] || format;
    }
}

//экземпляр класса для работы с каталогом
const mangaCatalog = new MangaCatalog();