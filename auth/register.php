<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validasyon
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Ad, soyad, e-posta ve şifre alanları zorunludur.';
        header('Location: ../register.php');
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Geçerli bir e-posta adresi girin.';
        header('Location: ../register.php');
        exit;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Şifre en az 6 karakter olmalıdır.';
        header('Location: ../register.php');
        exit;
    }
    
    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Şifreler eşleşmiyor.';
        header('Location: ../register.php');
        exit;
    }
    
    // Ad ve soyad sadece harf içermeli
    if (!preg_match('/^[a-zA-ZçğıöşüÇĞIİÖŞÜ\s]+$/', $first_name) || !preg_match('/^[a-zA-ZçğıöşüÇĞIİÖŞÜ\s]+$/', $last_name)) {
        $_SESSION['error'] = 'Ad ve soyad sadece harf içermelidir.';
        header('Location: ../register.php');
        exit;
    }
    
    try {
        // E-posta adresinin daha önce kullanılıp kullanılmadığını kontrol et
        $check_query = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$email]);
        
        if ($check_stmt->fetch()) {
            $_SESSION['error'] = 'Bu e-posta adresi zaten kullanılıyor.';
            header('Location: ../register.php');
            exit;
        }
        
        // Şifreyi hash'le
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Kullanıcıyı veritabanına kaydet
        $insert_query = "INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->execute([$first_name, $last_name, $email, $phone, $hashed_password]);
        
        $_SESSION['success'] = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
        header('Location: ../index.php');
        exit;
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = 'Bu e-posta adresi zaten kullanılıyor.';
        } else {
            $_SESSION['error'] = 'Bir hata oluştu. Lütfen tekrar deneyiniz.';
        }
        header('Location: ../register.php');
        exit;
    }
} else {
    header('Location: ../register.php');
    exit;
}
?> 