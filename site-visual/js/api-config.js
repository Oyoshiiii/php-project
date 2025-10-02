//все ответы будут на английском языке, русского перевода в api нет

const ANILIST_API = "https://graphql.anilist.co"; //url для post запросов

//жанры манги для каталога
const MANGA_GENRES = Object.keys(MANGA_GENRES_TRANSLATED);
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
