<?php
    $conn = new PDO("mysql:host=localhost;dbname=mangawebsite", "root", "12345");
    $sql = "SELECT * FROM Users";
    $result = $conn->query($sql);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . "<br>";
        echo "Номер: " . $row['number'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Пароль: " . $row['password'] . "<br>";
    }

?>