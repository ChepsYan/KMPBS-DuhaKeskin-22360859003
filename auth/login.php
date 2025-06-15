<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Validasyon
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'E-posta ve şifre alanları zorunludur.';
        header('Location: ../index.php');
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Geçerli bir e-posta adresi girin.';
        header('Location: ../index.php');
        exit;
    }
    
    try {
        // Kullanıcıyı veritabanından bul
        $query = "SELECT id, first_name, last_name, email, password FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Giriş başarılı
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['success'] = 'Hoş geldiniz, ' . $user['first_name'] . '!';
            
            header('Location: ../dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'E-posta veya şifre hatalı.';
            header('Location: ../index.php');
            exit;
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Bir hata oluştu. Lütfen tekrar deneyiniz.';
        header('Location: ../index.php');
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}
?> 