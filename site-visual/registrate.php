<?php
    /*
    $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
    $sql = "SELECT * FROM Users";
    $result = $conn->query($sql);
    
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " .  . "<br>";
        echo "Номер: " . $row['number'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Пароль: " . $row['password'] . "<br>";
    }
    */
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
            <form id="registrationForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="example@domain.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Номер телефона</label>
                        <input type="tel" id="phone" name="phone" required placeholder="+7 (123) 456-78-90">
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Никнейм</label>
                        <input type="text" id="username" name="username" required placeholder="Ваш уникальный никнейм">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" required placeholder="Не менее 8 символов">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Подтверждение пароля</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Повторите пароль">
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
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Пароли не совпадают!');
                return;
            }
            alert('Регистрация прошла успешно!');
        });
    </script>
</body>
</html>