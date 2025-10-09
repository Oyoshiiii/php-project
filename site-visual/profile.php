<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к базе данных
try {
    $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$error = '';
$success = '';
$user = [];

// Получаем данные пользователя из БД
try {
    $stmt = $conn->prepare("SELECT id, username, email, number, avatar FROM Users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Если пользователь не найден, очищаем сессию и куки
        session_destroy();
        foreach (['user_username', 'user_email', 'user_number', 'user_avatar', 'user_id'] as $cookie) {
            setcookie($cookie, '', time() - 3600, "/");
        }
        header("Location: login.php");
        exit();
    }
} catch(PDOException $e) {
    $error = "Ошибка базы данных: " . $e->getMessage();
}

// Получаем данные из куки для отображения
$cookie_username = $_COOKIE['user_username'] ?? $user['username'] ?? '';
$cookie_email = $_COOKIE['user_email'] ?? $user['email'] ?? '';
$cookie_number = $_COOKIE['user_number'] ?? $user['number'] ?? '';

// Обработка загрузки аватара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $uploadDir = 'uploads/avatars/';
    
    // Создаем директорию если не существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $avatar = $_FILES['avatar'];
    
    // Проверяем ошибки загрузки
    if ($avatar['error'] === UPLOAD_ERR_OK) {
        // Проверяем тип файла по расширению
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $error = "Разрешены только файлы JPG, JPEG, PNG, GIF и WebP!";
        } else {
            // Проверяем размер файла (макс. 5MB)
            if ($avatar['size'] > 5 * 1024 * 1024) {
                $error = "Размер файла не должен превышать 5MB!";
            } else {
                // Генерируем уникальное имя файла
                $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadDir . $fileName;
                
                // Удаляем старый аватар если существует
                if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                
                // Сохраняем файл
                if (move_uploaded_file($avatar['tmp_name'], $filePath)) {
                    // Обновляем путь к аватару в БД
                    try {
                        $stmt = $conn->prepare("UPDATE Users SET avatar = :avatar WHERE id = :user_id");
                        $stmt->bindParam(':avatar', $filePath);
                        $stmt->bindParam(':user_id', $_SESSION['user_id']);
                        
                        if ($stmt->execute()) {
                            $success = "Аватар успешно обновлен!";
                            // Обновляем данные пользователя
                            $user['avatar'] = $filePath;
                            // Сохраняем путь к аватару в куки
                            setcookie('user_avatar', $filePath, time() + (30 * 24 * 60 * 60), "/");
                        } else {
                            $error = "Ошибка при сохранении аватара в базу данных!";
                        }
                    } catch(PDOException $e) {
                        $error = "Ошибка базы данных: " . $e->getMessage();
                    }
                } else {
                    $error = "Ошибка при загрузке файла!";
                }
            }
        }
    } else {
        $error = "Ошибка загрузки файла!";
    }
}

// Обработка изменения профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $number = trim($_POST['number'] ?? '');
    
    if (!empty($username) && !empty($email)) {
        try {
            // Проверяем, не занят ли email другим пользователем
            $stmt = $conn->prepare("SELECT id FROM Users WHERE email = :email AND id != :user_id");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                $error = "Этот email уже используется другим пользователем!";
            } else {
                // Проверяем, не занят ли номер другим пользователем
                if (!empty($number)) {
                    $stmt = $conn->prepare("SELECT id FROM Users WHERE number = :number AND id != :user_id");
                    $stmt->bindParam(':number', $number);
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                    
                    if ($stmt->fetch()) {
                        $error = "Этот номер телефона уже используется другим пользователем!";
                    } else {
                        updateProfile($conn, $username, $email, $number);
                    }
                } else {
                    updateProfile($conn, $username, $email, $number);
                }
            }
        } catch(PDOException $e) {
            $error = "Ошибка базы данных: " . $e->getMessage();
        }
    } else {
        $error = "Заполните все обязательные поля!";
    }
}

// Обработка сброса пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            try {
                // Хэшируем новый пароль
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Обновляем пароль в базе данных
                $stmt = $conn->prepare("UPDATE Users SET password = :password WHERE id = :user_id");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success = "Пароль успешно изменен!";
                } else {
                    $error = "Ошибка при изменении пароля!";
                }
            } catch(PDOException $e) {
                $error = "Ошибка базы данных: " . $e->getMessage();
            }
        } else {
            $error = "Новый пароль и подтверждение не совпадают!";
        }
    } else {
        $error = "Заполните все поля для смены пароля!";
    }
}

function updateProfile($conn, $username, $email, $number) {
    $stmt = $conn->prepare("UPDATE Users SET username = :username, email = :email, number = :number WHERE id = :user_id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':number', $number);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        // Обновляем сессию
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        // Обновляем куки
        setcookie('user_username', $username, time() + (30 * 24 * 60 * 60), "/");
        setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), "/");
        setcookie('user_number', $number, time() + (30 * 24 * 60 * 60), "/");
        
        $GLOBALS['success'] = "Профиль успешно обновлен!";
    } else {
        $GLOBALS['error'] = "Ошибка при обновлении профиля!";
    }
}

// Получаем аватар из куки или БД
$avatar_path = $_COOKIE['user_avatar'] ?? $user['avatar'] ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="avatar-section">
                <div class="avatar-container">
                    <?php if (!empty($avatar_path) && file_exists($avatar_path)): ?>
                        <img src="<?php echo htmlspecialchars($avatar_path); ?>" 
                             alt="Аватар" 
                             class="user-avatar"
                             onclick="document.getElementById('avatarInput').click()">
                    <?php else: ?>
                        <div class="avatar-placeholder" onclick="document.getElementById('avatarInput').click()">
                            <?php echo strtoupper(substr($cookie_username, 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <form method="post" enctype="multipart/form-data" id="avatarForm">
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="this.form.submit()">
                    <div style="margin-top: 10px;">
                        <button type="button" class="avatar-upload-btn" onclick="document.getElementById('avatarInput').click()">
                            📷 Сменить аватар
                        </button>
                    </div>
                </form>
            </div>
            <h1>Мой профиль</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="message error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="message success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="user-info">
            <p><span class="info-label">Имя пользователя:</span> <?php echo htmlspecialchars($cookie_username); ?></p>
            <p><span class="info-label">Email:</span> <?php echo htmlspecialchars($cookie_email); ?></p>
            <p><span class="info-label">Номер телефона:</span> <?php echo htmlspecialchars($cookie_number ?: 'Не указан'); ?></p>
        </div>

        <!-- Форма редактирования профиля -->
        <div class="form-section">
            <h2>Редактировать профиль</h2>
            <form method="post">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label for="username">Имя пользователя *</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($cookie_username); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($cookie_email); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label for="number">Номер телефона</label>
                    <input type="text" id="number" name="number" 
                           value="<?php echo htmlspecialchars($cookie_number); ?>" 
                           placeholder="Не указан">
                </div>
                <button type="submit" class="btn">Сохранить изменения</button>
            </form>
        </div>

        <!-- Форма сброса пароля -->
        <div class="form-section">
            <h2>Сменить пароль</h2>
            <div class="password-note">
                <strong>Забыли фигню которые вы поставили на пароль?</strong> Можете придумать новую:3
            </div>
            <form method="post">
                <input type="hidden" name="reset_password" value="1">
                <div class="form-group">
                    <label for="new_password">Новый пароль *</label>
                    <input type="password" id="new_password" name="new_password" 
                           placeholder="Введите новый пароль" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Подтвердите новый пароль *</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Повторите новый пароль" required>
                </div>
                <button type="submit" class="btn btn-danger">Установить новый пароль</button>
            </form>
        </div>

        <div class="navigation">
            <a href="index.php">На главную</a> | 
            <a href="logout.php" onclick="clearCookies()">Выйти</a>
        </div>
    </div>

    <script>
        // Автоматическое обновление аватара после загрузки
        document.getElementById('avatarInput').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.querySelector('.user-avatar');
                    const avatarPlaceholder = document.querySelector('.avatar-placeholder');
                    
                    if (avatarImg) {
                        avatarImg.src = e.target.result;
                    } else if (avatarPlaceholder) {
                        // Заменяем placeholder на изображение
                        avatarPlaceholder.outerHTML = `<img src="${e.target.result}" alt="Аватар" class="user-avatar" onclick="document.getElementById('avatarInput').click()">`;
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        function clearCookies() {
            // Очищаем пользовательские куки на клиентской стороне
            document.cookie = "user_username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "user_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "user_number=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "user_avatar=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    </script>
</body>
</html>