<?php
// registration.php

// Функция для создания градиентного аватара (упрощенная версия без GD)
function createGradientAvatar($username) {
    $firstLetter = strtoupper(substr($username, 0, 1));
    $hash = md5($username);
    
    $color1 = '#' . substr($hash, 0, 6);
    $color2 = '#' . substr($hash, 6, 6);
    
    // Создаем SVG с градиентом
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
    <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="' . $color1 . '" />
                <stop offset="100%" stop-color="' . $color2 . '" />
            </linearGradient>
        </defs>
        <rect width="200" height="200" fill="url(#gradient)" />
        <text x="100" y="120" font-family="Arial, sans-serif" font-size="80" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">' . $firstLetter . '</text>
    </svg>';
    
    $uploadDir = 'uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = 'avatar_gradient_' . uniqid() . '.svg';
    $filePath = $uploadDir . $fileName;
    
    file_put_contents($filePath, $svg);
    
    return $filePath;
}

// Обработка формы регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email обязателен для заполнения";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный формат email";
    } 
    elseif (strlen($email) > 50) {
        $errors[] = "Email не должен превышать 50 символов";
    }
    
    if (empty($phone)) {
        $errors[] = "Номер телефона обязателен";
    } 
    elseif (strlen($phone) > 12) {
        $errors[] = "Номер телефона не должен превышать 12 символов";
    }
    
    if (empty($username)) {
        $errors[] = "Никнейм обязателен";
    } 
    elseif (strlen($username) > 20) {
        $errors[] = "Никнейм не должен превышать 20 символов";
    }
    
    if (empty($password)) {
        $errors[] = "Пароль обязателен";
    } 
    elseif (strlen($password) < 8) {
        $errors[] = "Пароль должен содержать минимум 8 символов";
    } 
    elseif (strlen($password) > 50) {
        $errors[] = "Пароль не должен превышать 50 символов";
    }
    
    // Обработка загрузки аватара
    $avatarPath = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar = $_FILES['avatar'];
        
        // Проверка типа файла
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($avatar['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Разрешены только файлы изображений (JPEG, PNG, GIF, WebP)";
        } 
        elseif ($avatar['size'] > 2 * 1024 * 1024) { // 2MB
            $errors[] = "Размер файла не должен превышать 2MB";
        } 
        else {
            // Создание папки для аватаров, если её нет
            $uploadDir = 'uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Генерация уникального имени файла
            $fileExtension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . uniqid() . '.' . $fileExtension;
            $avatarPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($avatar['tmp_name'], $avatarPath)) {
                $errors[] = "Ошибка при загрузке файла";
                $avatarPath = null;
            }
        }
    }
    
    // Если нет ошибок валидации
    if (empty($errors)) {
        try {
            // Подключение к базе данных
            $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Проверка существования пользователя с таким email, username или номером телефона
            $checkSql = "SELECT id, email, username, number FROM Users WHERE email = :email OR username = :username OR number = :number";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([
                ':email' => $email,
                ':username' => $username,
                ':number' => $phone
            ]);
            
            $existingUsers = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($existingUsers)) {
                $emailExists = false;
                $usernameExists = false;
                $phoneExists = false;
                
                // Проверяем, какие именно поля уже существуют
                foreach ($existingUsers as $user) {
                    if ($user['email'] === $email) {
                        $emailExists = true;
                    }
                    if ($user['username'] === $username) {
                        $usernameExists = true;
                    }
                    if ($user['number'] === $phone) {
                        $phoneExists = true;
                    }
                }
                
                // Добавляем ошибки только один раз для каждого типа
                if ($emailExists) {
                    $errors[] = "Пользователь с таким email уже существует";
                }
                if ($usernameExists) {
                    $errors[] = "Пользователь с таким никнеймом уже существует";
                }
                if ($phoneExists) {
                    $errors[] = "Пользователь с таким номером телефона уже существует";
                }
            } 
            else {
                // Хеширование пароля
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Если аватар не загружен, создаем градиентный фон
                if (!$avatarPath) {
                    $avatarPath = createGradientAvatar($username);
                }
                
                // Вставка нового пользователя
                $insertSql = "INSERT INTO Users (number, email, password, username, avatar) 
                             VALUES (:number, :email, :password, :username, :avatar)";
                $insertStmt = $conn->prepare($insertSql);
                
                $result = $insertStmt->execute([
                    ':number' => $phone,
                    ':email' => $email,
                    ':password' => $hashedPassword,
                    ':username' => $username,
                    ':avatar' => $avatarPath
                ]);
                
                if ($result) {
                    $success = "Регистрация прошла успешно!";
                    // Очистка формы после успешной регистрации
                    $_POST = array();
                } 
                else {
                    $errors[] = "Ошибка при регистрации. Попробуйте еще раз.";
                }
            }
            
        } catch (PDOException $e) {
            $errors[] = "Ошибка базы данных: " . $e->getMessage();
        }
    }
}
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/registerate.css">
    <title>Регистрация</title>
</head>
<body>
    <div class="container">
        <header>
            <h1>Регистрация</h1>
        </header>
        
        <section class="registration-section">
            <h2 class="section-title">Основная информация</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form id="registrationForm" method="POST" action="" enctype="multipart/form-data">
                <div class="avatar-upload">
                    <div id="defaultAvatar">
                        <?php 
                        $defaultLetter = isset($_POST['username']) && !empty($_POST['username']) 
                            ? strtoupper(substr($_POST['username'], 0, 1)) 
                            : '?';
                        echo $defaultLetter;
                        ?>
                    </div>
                    <img id="avatarPreview" src="" alt="Предпросмотр аватара" class="avatar-preview" style="display: none;">
                    <label for="avatarInput">Выберите аватар</label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*">
                    <div id="removeAvatar" class="remove-avatar" style="display: none;">Удалить фото</div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="example@domain.com"
                               maxlength="50">
                        <div class="character-count"><span id="emailCount">0</span>/50</div>
                        <div id="emailError" class="field-error">Пользователь с таким email уже существует</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Номер телефона</label>
                        <input type="tel" id="phone" name="phone" required 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               placeholder="+71234567890"
                               maxlength="12">
                        <div class="character-count"><span id="phoneCount">0</span>/12</div>
                        <div id="phoneError" class="field-error">Пользователь с таким номером телефона уже существует</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Никнейм</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               placeholder="Ваш уникальный никнейм"
                               maxlength="20">
                        <div class="character-count"><span id="usernameCount">0</span>/20</div>
                        <div id="usernameError" class="field-error">Пользователь с таким никнеймом уже существует</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Не менее 8 символов"
                               maxlength="50">
                        <div class="character-count"><span id="passwordCount">0</span>/50</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Подтверждение пароля</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required 
                               placeholder="Повторите пароль"
                               maxlength="50">
                        <div id="passwordMatchError" class="field-error">Пароли не совпадают</div>
                    </div>
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
                
                <div class="form-footer">
                    <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
                </div>
            </form>
        </section>
    </div>

    <script>
        // Обработка загрузки аватара
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');
        const defaultAvatar = document.getElementById('defaultAvatar');
        const removeAvatar = document.getElementById('removeAvatar');
        
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    avatarPreview.style.display = 'block';
                    defaultAvatar.style.display = 'none';
                    removeAvatar.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        removeAvatar.addEventListener('click', function() {
            avatarInput.value = '';
            avatarPreview.style.display = 'none';
            defaultAvatar.style.display = 'flex';
            removeAvatar.style.display = 'none';
        });
        
        // Обновление дефолтного аватара при вводе имени пользователя
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const firstLetter = username ? username.charAt(0).toUpperCase() : '?';
            defaultAvatar.textContent = firstLetter;
        });
        
        // Счетчики символов
        document.getElementById('email').addEventListener('input', function() {
            document.getElementById('emailCount').textContent = this.value.length;
        });
        
        document.getElementById('phone').addEventListener('input', function() {
            document.getElementById('phoneCount').textContent = this.value.length;
        });
        
        document.getElementById('username').addEventListener('input', function() {
            document.getElementById('usernameCount').textContent = this.value.length;
        });
        
        document.getElementById('password').addEventListener('input', function() {
            document.getElementById('passwordCount').textContent = this.value.length;
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('emailCount').textContent = document.getElementById('email').value.length;
            document.getElementById('phoneCount').textContent = document.getElementById('phone').value.length;
            document.getElementById('usernameCount').textContent = document.getElementById('username').value.length;
            document.getElementById('passwordCount').textContent = document.getElementById('password').value.length;
        });

        // Валидация формы
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const username = document.getElementById('username').value;
            const avatar = document.getElementById('avatarInput').files[0];
            
            let errors = [];
            
            document.querySelectorAll('.field-error').forEach(error => {
                error.style.display = 'none';
            });
            
            if (password !== confirmPassword) {
                errors.push('Пароли не совпадают!');
                document.getElementById('passwordMatchError').style.display = 'block';
            }
            
            if (password.length < 8) {
                errors.push('Пароль должен содержать минимум 8 символов!');
            }
            
            if (email.length > 50) {
                errors.push('Email не должен превышать 50 символов!');
            }
            
            if (phone.length > 12) {
                errors.push('Номер телефона не должен превышать 12 символов!');
            }
            
            if (username.length > 20) {
                errors.push('Никнейм не должен превышать 20 символов!');
            }
            
            if (avatar) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(avatar.type)) {
                    errors.push('Разрешены только файлы изображений (JPEG, PNG, GIF, WebP)!');
                }
                
                if (avatar.size > 2 * 1024 * 1024) {
                    errors.push('Размер файла не должен превышать 2MB!');
                }
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });

        // Форматирование номера телефона
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('7') || value.startsWith('8')) {
                value = '+' + value;
            }
            e.target.value = value.substring(0, 12);
        });
    </script>
</body>
</html>