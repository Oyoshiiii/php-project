const { startServer } = require('mangadex-full-api');

startServer(3000).then(() => {
    console.log('✅ MangaDex Full API запущен на http://localhost:3000');
}).catch(error => {
    console.error('Ошибка запуска:', error);
});