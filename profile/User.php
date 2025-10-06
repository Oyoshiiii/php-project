<?php
// User.php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $number;
    public $email;
    public $password;
    public $username;
    public $avatar;
    public $bio;
    public $fav_genre;
    public $language;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Получить пользователя по ID
    public function getUserById($id) {
        $query = "SELECT id, number, email, username, avatar, bio, fav_genre, language 
                  FROM " . $this->table_name . " 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt;
    }

    // Получить пользователя по email
    public function getUserByEmail($email) {
        $query = "SELECT id, number, email, username, password, avatar, bio, fav_genre, language 
                  FROM " . $this->table_name . " 
                  WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt;
    }

    // Обновить профиль пользователя
    public function updateProfile() {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, email = :email, bio = :bio, 
                      fav_genre = :fav_genre, language = :language 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Очистка данных
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->fav_genre = htmlspecialchars(strip_tags($this->fav_genre));
        $this->language = htmlspecialchars(strip_tags($this->language));

        // Привязка параметров
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':fav_genre', $this->fav_genre);
        $stmt->bindParam(':language', $this->language);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Обновить аватар
    public function updateAvatar($avatar_path) {
        $query = "UPDATE " . $this->table_name . " 
                  SET avatar = :avatar 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avatar', $avatar_path);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Проверить, существует ли email
    public function emailExists() {
        $query = "SELECT id 
                  FROM " . $this->table_name . " 
                  WHERE email = :email AND id != :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>