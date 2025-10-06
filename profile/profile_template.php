<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя - MangaSite</title>
    <style>
        /* Стили из предыдущего примера */
        :root {
            --primary: #ff4081;
            --secondary: #3f51b5;
            --dark: #1a1a2e;
            --light: #f5f5f5;
            --accent: #e91e63;
            --text: #333;
            --text-light: #777;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Стили header, profile-section, footer и т.д. */
        /* ... (используйте стили из предыдущего HTML примера) ... */
        
        .alert {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">Manga<span>Site</span></a>
                <nav>
                    <ul>
                        <li><a href="#">Главная</a></li>
                        <li><a href="#">Каталог</a></li>
                        <li><a href="#">Новинки</a></li>
                        <li><a href="#" class="active">Профиль</a></li>
                        <li><a href="logout.php">Выйти</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="profile-container">
            <div class="profile-sidebar">
                <img src="<?php echo isset($user_data['avatar']) ? $user_data['avatar'] : 'https://via.placeholder.com/150'; ?>" 
                     alt="Аватар пользователя" class="avatar">
                
                <form method="post" enctype="multipart/form-data" style="margin: 10px 0;">
                    <input type="file" name="avatar" accept="image/*" style="margin-bottom: 10px;">
                    <button type="submit" class="btn">Обновить аватар</button>
                </form>
                
                <h2 class="username"><?php echo htmlspecialchars($user_data['username']); ?></h2>
                <p class="user-title">Фанат манги</p>
                
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-value">127</div>
                        <div class="stat-label">Прочитано</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">42</div>
                        <div class="stat-label">В избранном</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">15</div>
                        <div class="stat-label">Отзывов</div>
                    </div>
                </div>
            </div>
            
            <div class="profile-main">
                <div class="profile-tabs">
                    <div class="tab active" data-tab="info">Информация</div>
                    <div class="tab" data-tab="favorites">Избранное</div>
                    <div class="tab" data-tab="settings">Настройки</div>
                </div>
                
                <div class="tab-content active" id="info">
                    <h3>О пользователе</h3>
                    <p><?php echo !empty($user_data['bio']) ? htmlspecialchars($user_data['bio']) : 'Пользователь еще не добавил информацию о себе.'; ?></p>
                    
                    <h3 style="margin-top: 20px;">Контактная информация</h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <?php if(!empty($user_data['number'])): ?>
                        <p><strong>Номер:</strong> <?php echo htmlspecialchars($user_data['number']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content" id="favorites">
                    <h3>Избранная манга</h3>
                    <div class="manga-grid">
                        <!-- Динамическое содержимое избранного -->
                    </div>
                </div>
                
                <div class="tab-content" id="settings">
                    <h3>Настройки профиля</h3>
                    <form method="post">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Имя пользователя</label>
                                <input type="text" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="bio">О себе</label>
                            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fav_genre">Любимый жанр</label>
                                <select id="fav_genre" name="fav_genre">
                                    <option value="">Выберите жанр</option>
                                    <option value="Сёнэн" <?php echo ($user_data['fav_genre'] ?? '') == 'Сёнэн' ? 'selected' : ''; ?>>Сёнэн</option>
                                    <option value="Сёдзё" <?php echo ($user_data['fav_genre'] ?? '') == 'Сёдзё' ? 'selected' : ''; ?>>Сёдзё</option>
                                    <option value="Фэнтези" <?php echo ($user_data['fav_genre'] ?? '') == 'Фэнтези' ? 'selected' : ''; ?>>Фэнтези</option>
                                    <option value="Романтика" <?php echo ($user_data['fav_genre'] ?? '') == 'Романтика' ? 'selected' : ''; ?>>Романтика</option>
                                    <option value="Приключения" <?php echo ($user_data['fav_genre'] ?? '') == 'Приключения' ? 'selected' : ''; ?>>Приключения</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="language">Язык интерфейса</label>
                                <select id="language" name="language">
                                    <option value="Русский" <?php echo ($user_data['language'] ?? '') == 'Русский' ? 'selected' : ''; ?>>Русский</option>
                                    <option value="English" <?php echo ($user_data['language'] ?? '') == 'English' ? 'selected' : ''; ?>>English</option>
                                    <option value="日本語" <?php echo ($user_data['language'] ?? '') == '日本語' ? 'selected' : ''; ?>>日本語</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <!-- Футер из предыдущего примера -->
    </footer>

    <script>
        // JavaScript для табов
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>