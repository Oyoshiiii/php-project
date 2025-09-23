//заготовки запросов
const MANGA_QUERIES = {
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