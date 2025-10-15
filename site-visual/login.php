<?php
session_start();

// Подключение к базе данных
try {
    $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$error = '';
$success = '';

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'] ?? '';
    $password = $_POST['password'] ?? '';
    $login_type = $_POST['login_type'] ?? 'email';
    
    if (!empty($identifier) && !empty($password)) {
        if ($login_type === 'email') {
            $sql = "SELECT * FROM Users WHERE email = :identifier";
        } else {
            $sql = "SELECT * FROM Users WHERE number = :identifier";
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':identifier', $identifier);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    
                    // Сохраняем данные в куки на 30 дней
                    setcookie('user_username', $user['username'], time() + (30 * 24 * 60 * 60), "/");
                    setcookie('user_email', $user['email'], time() + (30 * 24 * 60 * 60), "/");
                    setcookie('user_number', $user['number'], time() + (30 * 24 * 60 * 60), "/");
                    setcookie('user_id', $user['id'], time() + (30 * 24 * 60 * 60), "/");
                    
                    $success = "Успешный вход!";
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Неверный пароль!";
                }
            } else {
                $error = "Пользователь не найден!";
            }
        } catch(PDOException $e) {
            $error = "Ошибка базы данных: " . $e->getMessage();
        }
    } else {
        $error = "Заполните все поля!";
    }
}

// Получаем данные из куки для автозаполнения
$saved_username = $_COOKIE['user_username'] ?? '';
$saved_email = $_COOKIE['user_email'] ?? '';
$saved_number = $_COOKIE['user_number'] ?? '';
?>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>
        <div class="container">
            <header>
                <h1>Вход</h1>
            </header>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="forms-container" id="formsContainer">
                <div class="form-wrapper top" id="form1">
                    <form method="post">
                        <input type="hidden" name="login_type" value="email">
                        <h2 class="form-title">Вход по Email</h2>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="identifier" 
                                placeholder="Введите ваш email" 
                                value="<?php echo htmlspecialchars($saved_email); ?>" 
                                required>
                        </div>
                        <div class="form-group">
                            <label for="password1">Пароль</label>
                            <input type="password" id="password1" name="password" placeholder="Введите пароль" required>
                        </div>
                        <button type="submit" class="regButton">
                            <span class="swap-icon"></span> Войти
                        </button>
                    </form>
                </div>
                
                <div class="form-wrapper bottom" id="form2">
                    <form method="post">
                        <input type="hidden" name="login_type" value="number">
                        <h2 class="form-title">Вход по номеру</h2>
                        <div class="form-group">
                            <label for="number">Номер телефона</label>
                            <input type="text" id="number" name="identifier" 
                                placeholder="Введите ваш номер" 
                                value="<?php echo htmlspecialchars($saved_number); ?>" 
                                required>
                        </div>
                        <div class="form-group">
                            <label for="password2">Пароль</label>
                            <input type="password" id="password2" name="password" placeholder="Введите пароль" required>
                        </div>
                        <button type="submit" class="regButton">
                            <span class="swap-icon"></span> Войти
                        </button>
                    </form>
                </div>
            </div>
            
            <button class="swap-button" id="swapButton" type="button">
                <span class="swap-icon">⇅</span> Хочу войти по-другому
            </button>
            
            <a class="instructions" href="registrate.php">Зарегистрироваться</a>
            
            <footer>
            </footer>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const swapButton = document.getElementById('swapButton');
                const form1 = document.getElementById('form1');
                const form2 = document.getElementById('form2');
                const formsContainer = document.getElementById('formsContainer');
                
                let isSwapped = false;
                
                swapButton.addEventListener('click', function() {
                    formsContainer.classList.add('animation-active');
                    
                    if (isSwapped) {
                        form1.classList.remove('bottom');
                        form1.classList.add('top');
                        form2.classList.remove('top');
                        form2.classList.add('bottom');
                    } else {
                        form1.classList.remove('top');
                        form1.classList.add('bottom');
                        form2.classList.remove('bottom');
                        form2.classList.add('top');
                    }
                    
                    isSwapped = !isSwapped;
                    
                    setTimeout(() => {
                        formsContainer.classList.remove('animation-active');
                    }, 500);
                });
            });
        </script>
    </body>
</html>