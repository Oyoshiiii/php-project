<?php
// profile.php
session_start();
include 'config.php';
include 'User.php';

// Проверка авторизации
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User($conn);
$user->id = $_SESSION['user_id'];

// Получение данных пользователя
$stmt = $user->getUserById($user->id);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Обработка формы обновления профиля
if($_POST && isset($_POST['update_profile'])) {
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->bio = $_POST['bio'];
    $user->fav_genre = $_POST['fav_genre'];
    $user->language = $_POST['language'];

    // Проверка уникальности email
    if($user->emailExists()) {
        $error = "Этот email уже используется другим пользователем";
    } else {
        // Обновление профиля
        if($user->updateProfile()) {
            $success = "Профиль успешно обновлен!";
            // Обновляем данные в сессии
            $_SESSION['username'] = $user->username;
            // Перезагружаем данные пользователя
            $stmt = $user->getUserById($user->id);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Ошибка при обновлении профиля";
        }
    }
}

// Обработка загрузки аватара
if($_FILES && isset($_FILES['avatar'])) {
    $avatar = $_FILES['avatar'];
    
    if($avatar['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if(in_array($avatar['type'], $allowed_types) && $avatar['size'] <= $max_size) {
            $file_extension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file_extension;
            $upload_path = 'uploads/avatars/' . $filename;
            
            if(move_uploaded_file($avatar['tmp_name'], $upload_path)) {
                if($user->updateAvatar($upload_path)) {
                    $success = "Аватар успешно обновлен!";
                    $user_data['avatar'] = $upload_path;
                } else {
                    $error = "Ошибка при сохранении аватара в базу данных";
                }
            } else {
                $error = "Ошибка при загрузке файла";
            }
        } else {
            $error = "Недопустимый формат файла или размер превышает 2MB";
        }
    }
}
?>