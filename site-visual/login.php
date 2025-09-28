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
<html>
    <header>
        <meta charset="utf-8">
        <title>Login</title>
        <link rel="stylesheet" href="css/login.css">
    </header>
    <body>
        <div class="container">
        <header>
            <h1>Вход</h1>
        </header>
        
        <div class="forms-container" id="formsContainer">
            <div class="form-wrapper top" id="form1">
                <form method="get">
                    <h2 class="form-title">Вход по Email</h2>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Введите ваш email">
                    </div>
                    <div class="form-group">
                        <label for="password1">Пароль</label>
                        <input type="password" id="password1" name="password" placeholder="Введите пароль">
                    </div>
                </form>
            </div>
            
            <div class="form-wrapper bottom" id="form2">
                <form method="get">
                    <h2 class="form-title">Входа по номеру</h2>
                    <div class="form-group">
                        <label for="number">Номер телефона</label>
                        <input type="text" id="number" name="number" placeholder="Введите ваш номер">
                    </div>
                    <div class="form-group">
                        <label for="password2">Пароль</label>
                        <input type="password" id="password2" name="password" placeholder="Введите пароль">
                    </div>
                </form>
            </div>
        </div>
        
        <button class="swap-button" id="swapButton">
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