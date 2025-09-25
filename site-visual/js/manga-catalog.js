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
                <img src="${manga.coverImage.large}" 
                     alt="${manga.title.romaji || manga.title.english}">
                <h3>${manga.title.romaji || manga.title.english}</h3>
                <p>${manga.averageScore + '/10' || 'N/A'}</p>
                <p>${manga.genres.slice(0, 3).join(', ')}</p>
            </div>
        `).join('');
    }

    //отображение деталей манги после нажатия на иконку с ней
    async showMangaDetails(){

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