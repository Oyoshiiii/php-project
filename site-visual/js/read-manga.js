const MANGADEX_API = "https://api.mangadex.org"; //url для post запросов к API MangaDex

class ReadManga {
    async findMangaByAnilistId(anilistManga) {
        try {
            const title = anilistManga.title.romaji || anilistManga.title.english;
            console.log(`Поиск в MangaDex: "${title}"`);
            
            const searchQueries = [
                title,
                this.normalizeTitle(title),
                anilistManga.title.english || '',
                anilistManga.title.native || ''
            ].filter(q => q && q.trim());
            
            for (const query of searchQueries) {
                const response = await fetch(
                    `${MANGADEX_API}/manga?` + 
                    `title=${encodeURIComponent(query)}&` +
                    `limit=5&` +
                    `includes[]=cover_art&` +
                    `includes[]=author&` +
                    `order[relevance]=desc`
                );
                
                if (!response.ok) {
                    console.warn(`Ошибка запроса для "${query}": ${response.status}`);
                    continue;
                }
                
                const data = await response.json();
                if (data.data && data.data.length > 0) {
                    console.log(`Найдена манга в MangaDex: ${data.data[0].attributes.title.en}`);
                    return data.data[0];
                }
            }
            
            console.error('Манга не найдена в MangaDex по всем запросам');
            return null;
            
        } catch (error) {
            console.error('Ошибка поиска в MangaDex:', error);
            return null;
        }
    }

    normalizeTitle(title) {
        return title
            .toLowerCase()
            .replace(/[^\w\s]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    async getAllChapters(mangaId) {
        try {
            console.log(`Получение всех глав для манги: ${mangaId}`);
            let allChapters = [];
            let offset = 0;
            const limit = 100; 

            while (true) {
                const response = await fetch(
                    `${MANGADEX_API}/manga/${mangaId}/feed?` + 
                    `translatedLanguage[]=ru&translatedLanguage[]=en&` +
                    `order[chapter]=desc&limit=${limit}&offset=${offset}&includes[]=scanlation_group`
                );
                
                if (!response.ok) break;
                
                const data = await response.json();
                
                if (!data.data || data.data.length === 0) break;
                
                allChapters = allChapters.concat(data.data);
                offset += limit;
                
                if (data.data.length < limit) break;
                
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            
            console.log(`Получено глав: ${allChapters.length}`);
            return allChapters;
        } catch (error) {
            console.error('Ошибка получения глав:', error);
            return null;
        }
    }

    async getChapterPages(chapterId) {
        try {
            console.log(`Получение страниц главы: ${chapterId}`);
            const response = await fetch(`${MANGADEX_API}/at-home/server/${chapterId}`);
            
            if (!response.ok) {
                throw new Error(`MangaDex API error: ${response.status}`);
            }
            
            const data = await response.json();
            
            const baseUrl = data.baseUrl;
            const chapterHash = data.chapter.hash;
            const pages = data.chapter.data;
            
            return pages.map(page => `${baseUrl}/data/${chapterHash}/${page}`);
        } catch (error) {
            console.error('Ошибка получения страниц:', error);
            return null;
        }
    }

    getCoverUrl(manga, size = '512') {
        if (!manga.relationships) return null;
        
        const coverArt = manga.relationships.find(rel => rel.type === 'cover_art');
        if (coverArt && coverArt.attributes) {
            const fileName = coverArt.attributes.fileName;
            return `https://uploads.mangadex.org/covers/${manga.id}/${fileName}.${size}.jpg`;
        }
        return null;
    }
}