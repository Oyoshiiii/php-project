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

//статусы манги
const MANGA_STATUS_TRANSLATED = {
    'FINISHED': 'Завершена',
    'RELEASING': 'Выходит',
    'NOT_YET_RELEASED': 'Скоро выйдет',
    'CANCELLED': 'Отменена',
    'HIATUS': 'Приостановлена'
};

const MANGA_STATUS = Object.keys(MANGA_STATUS_TRANSLATED);

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
    //по нескольким фильтрам
    /*
    в запрос передаются:
        жанр, год, статус, тип (манга)
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
    byFilters: 
    `
        query ($genres: [String], $status: MediaStatus, $startDate_greater: FuzzyDateInt, $startDate_lesser: FuzzyDateInt, $page: Int, $perPage: Int) {
            Page (page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                }
                media (
                    type: MANGA, 
                    genre_in: $genres,
                    status: $status,
                    startDate_greater: $startDate_greater,
                    startDate_lesser: $startDate_lesser,
                    sort: POPULARITY_DESC
                ) {
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
                    startDate {
                        year
                    }
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
        this.currentFilters = {
            genres: [],
            status: '',
            year: '',
            search: ''
        };
        this.currentPage = 1;
    }
    //настройка обработчика событий
    setupEventListeners() {
        const searchInput = document.getElementById('search-input');
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.currentFilters.search = e.target.value.trim();
                this.currentPage = 1;
                this.isSearching = true;
                
                if (this.currentFilters.search.trim() === '') {
                    this.isSearching = false;
                    this.loadPopularManga('manga-container');
                } else {
                    this.searchManga('manga-container', this.currentFilters.search);
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
    getStatusText(status) {
        return MANGA_STATUS_TRANSLATED[status] || status;
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

    async applyFilters(containerId, page = 1) {
        const container = document.getElementById(containerId);
        if (!container) return;

        this.currentPage = page;
        
        if (page === 1) {
            container.innerHTML = '<div class="loading">Поиск манги...</div>';
        }

        try {
            let result;
            
            if (this.currentFilters.search) {
                result = await graphQLRequest(MANGA_QUERIES.search, {
                    search: this.currentFilters.search,
                    page: this.currentPage,
                    perPage: 24
                });
            } else if (this.hasActiveFilters()) {
                result = await this.loadWithFilters(containerId, page, 24);
            } else {
                result = await graphQLRequest(MANGA_QUERIES.popular, {
                    perPage: 24
                });
            }

            if (result && result.data) {
                const mangaList = result.data.Page.media;
                
                if (page === 1) {
                    this.showMangaGrid(mangaList, container);
                } else {
                    const loadMoreBtn = container.querySelector('.load-more-btn');
                    if (loadMoreBtn) loadMoreBtn.remove();
                    this.showMangaGrid(mangaList, container, false);
                }

                //настройка пагинации
                if (result.data.Page.pageInfo) {
                    this.pagination(result.data.Page.pageInfo, containerId);
                }
            }
        } catch(error) {
            console.error('Ошибка применения фильтров:', error);
            container.innerHTML = '<p class="error">Ошибка загрузки манги</p>';
        }
    }

    hasActiveFilters() {
        return this.currentFilters.genres.length > 0 || 
               this.currentFilters.status || 
               this.currentFilters.year;
    }

    //загрузка манги с указанными фильтрами
    async loadWithFilters(containerId, page = 1, perPage = 24) {
        const variables = {
            page: page,
            perPage: perPage
        };

        if (this.currentFilters.genres.length > 0) {
            variables.genres = this.currentFilters.genres;
        }

        if (this.currentFilters.status) {
            variables.status = this.currentFilters.status;
        }

        if (this.currentFilters.year) {
            const dateRange = this.getYearRange(this.currentFilters.year);
            if (dateRange.startDate_greater) {
                variables.startDate_greater = dateRange.startDate_greater;
            }
            if (dateRange.startDate_lesser) {
                variables.startDate_lesser = dateRange.startDate_lesser;
            }
        }

        console.log('Выполняем запрос с фильтрами:', variables);
        
        return await graphQLRequest(MANGA_QUERIES.byFilters, variables);
    }

    getYearRange(yearFilter) {
        let startDate_greater, startDate_lesser;

        switch(yearFilter) {
            case "2020":
                startDate_greater = 20200101; 
                startDate_lesser = null; 
                break;
            case "2010":
                startDate_greater = 20100101; 
                startDate_lesser = 20200101;  
                break;
            case "2000":
                startDate_greater = 20000101; 
                startDate_lesser = 20100101; 
                break;
            case "1990":
                startDate_greater = 19900101;
                startDate_lesser = 20000101;  
                break;
            case "1980":
                startDate_greater = 19800101; 
                startDate_lesser = 19900101;  
                break;
            default:
                startDate_greater = null;
                startDate_lesser = null;
        }
        
        return { startDate_greater, startDate_lesser };
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
    pagination(pageInfo, containerId) {
        const container = document.getElementById(containerId);
        if (!container || !pageInfo || !pageInfo.hasNextPage) return;
        
        const existingBtn = container.querySelector('.load-more-btn');
        if (existingBtn) existingBtn.remove();
        
        const loadMoreBtn = document.createElement('button');
        loadMoreBtn.className = 'load-more-btn';
        loadMoreBtn.innerHTML = `Загрузить еще (${pageInfo.currentPage}/${pageInfo.lastPage})`;
        
        loadMoreBtn.onclick = () => {
            this.applyFilters(containerId, pageInfo.currentPage + 1);
        };
        
        container.appendChild(loadMoreBtn);
    }

    getFormatText(format) {
    const formatMap = {
        'MANGA': 'Манга',
        'NOVEL': 'Роман',
        'ONE_SHOT': 'Ваншот'
    };
    return formatMap[format] || format;
}
}

//экземпляр класса для работы с каталогом
const mangaCatalog = new MangaCatalog();