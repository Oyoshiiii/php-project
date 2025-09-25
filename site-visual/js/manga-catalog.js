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
    //подгружает еще несколько популярных манг (максимальное кол-во 12) 
    //для общей подборки (не по категориям)
    async loadPopularManga(containerId, maxManga = 12){
        const container = document.getElementById(containerId);
        if (!container) return;
        
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
                //showManga - метод отображения манги в контейнере
                this.showMangaGrid(result.data.Page.media, container);
            }
        }
        catch(error){
            //класс error условный, его можно поменять вдальнейшем
            container.innerHTML = '<p class="error">Ошибка загрузки манги</p>';
        }
    }

    //загрузка манги по жанрам
    async loadByGenres(containerId, genres = [], page = 1, perPage = 24){
        const container = document.getElementById(containerId);
        if(!container) return;

        try{
            const result = await graphQLRequest(MANGA_QUERIES.byGenres, {
                genres: genres,
                page: page,
                perPage: perPage
            })

            if(result && result.data){
                this.showMangaGrid(result.data.Page.media, container);
                //настройка пагинации после загрузки
                this.pagination(result.data.Page.pageInfo, genres, containerId);
            }
        }
        catch(error){
            //класс error условный, его можно поменять вдальнейшем
            container.innerHTML = '<p class="error">Ошибка загрузки манги</p>';
        }
    }

    //вывод манги в кратком отображении для грида
    //опять же весь визуал условный, его можно менять
    showMangaGrid(mangaList, container) {
        if (!mangaList || mangaList.length === 0) {
            container.innerHTML = '<p class="no-results">Манга не найдена</p>';
            return;
        }
        container.innerHTML = mangaList.map(manga => `
            <div class="manga-card" data-id="${manga.id}">
                <img src="${manga.coverImage.large}" alt="${manga.title.romaji || manga.title.english}">
                <h3>${manga.title.romaji || manga.title.english}</h3>
                <p>${manga.averageScore + '/10' || 'N/A'}</p>
                <p>${manga.genres.slice(0, 3).join(', ')}</p>
            </div>
        `).join('');
    }

    //получение деталей манги после нажатия на иконку с ней
    async getMangaDetails(mangaId, containerId){
        const container = document.getElementById(containerId);
        if(!container) return;

        container.innerHTML = '<div class="loading">Загрузка информации о манге...</div>';
        
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
    displayMangaDetails(manga, container){
        const description = manga.description;
        if(description.trim() === ''){
            description = "Описание отсутствует";
        }

        //этот html код тоже по сути надо, чтобы Даниил глянул
        container.innerHTML = `
        <div class="manga-detail-header" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('${manga.bannerImage || manga.coverImage.extraLarge}')">
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
    pagination(pageInfo, genres, containerId) {
        const container = document.getElementById(containerId);
        if (!container || !pageInfo || !pageInfo.hasNextPage) return;
        const loadMoreBtn = document.createElement('button');
        loadMoreBtn.className = 'load-more-btn';
        loadMoreBtn.innerHTML = `Загрузить еще (${pageInfo.currentPage}/${pageInfo.lastPage})`;
        //обработчик клика для кнопки Загрузить еще
        loadMoreBtn.onclick = () => {
            this.loadByGenres(containerId, genres, pageInfo.currentPage + 1);
        };
        container.appendChild(loadMoreBtn);
    }
}

//экземпляр класса для работы с каталогом
const mangaCatalog = new MangaCatalog();