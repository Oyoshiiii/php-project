<?php
// registration.php

// Обработка формы регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Базовая валидация
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email обязателен для заполнения";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный формат email";
    } elseif (strlen($email) > 50) {
        $errors[] = "Email не должен превышать 50 символов";
    }
    
    if (empty($phone)) {
        $errors[] = "Номер телефона обязателен";
    } elseif (strlen($phone) > 12) {
        $errors[] = "Номер телефона не должен превышать 12 символов";
    }
    
    if (empty($username)) {
        $errors[] = "Никнейм обязателен";
    } elseif (strlen($username) > 20) {
        $errors[] = "Никнейм не должен превышать 20 символов";
    }
    
    if (empty($password)) {
        $errors[] = "Пароль обязателен";
    } elseif (strlen($password) < 8) {
        $errors[] = "Пароль должен содержать минимум 8 символов";
    } elseif (strlen($password) > 50) {
        $errors[] = "Пароль не должен превышать 50 символов";
    }
    
    // Если нет ошибок валидации
    if (empty($errors)) {
        try {
            // Подключение к базе данных
            $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Проверка существования пользователя с таким email или username
            $checkSql = "SELECT id FROM Users WHERE email = :email OR username = :username";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([
                ':email' => $email,
                ':username' => $username
            ]);
            
            if ($checkStmt->fetch()) {
                $errors[] = "Пользователь с таким email или никнеймом уже существует";
            } else {
                // Хеширование пароля
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Вставка нового пользователя
                $insertSql = "INSERT INTO Users (number, email, password, username) 
                             VALUES (:number, :email, :password, :username)";
                $insertStmt = $conn->prepare($insertSql);
                
                $result = $insertStmt->execute([
                    ':number' => $phone,
                    ':email' => $email,
                    ':password' => $hashedPassword,
                    ':username' => $username
                ]);
                
                if ($result) {
                    $success = "Регистрация прошла успешно!";
                    // Очистка формы после успешной регистрации
                    $_POST = array();
                } else {
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
            
            <form id="registrationForm" method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="example@domain.com"
                               maxlength="50">
                        <div class="character-count"><span id="emailCount">0</span>/50</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Номер телефона</label>
                        <input type="tel" id="phone" name="phone" required 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               placeholder="+71234567890"
                               maxlength="12">
                        <div class="character-count"><span id="phoneCount">0</span>/12</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Никнейм</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               placeholder="Ваш уникальный никнейм"
                               maxlength="20">
                        <div class="character-count"><span id="usernameCount">0</span>/20</div>
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
        
        // Инициализация счетчиков при загрузке
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
            
            let errors = [];
            
            if (password !== confirmPassword) {
                errors.push('Пароли не совпадают!');
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