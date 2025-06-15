<?php
session_start();
require_once '../config/database.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form verilerini al ve temizle
    $ada_no = trim($_POST['ada_no']);
    $parsel_no = trim($_POST['parsel_no']);
    $il = trim($_POST['il']);
    $ilce = trim($_POST['ilce']);
    $mahalle = trim($_POST['mahalle']);
    $alan = $_POST['alan'];
    $tapu_tipi = $_POST['tapu_tipi'];
    $malik_adi = trim($_POST['malik_adi']);
    $malik_soyadi = trim($_POST['malik_soyadi']);
    $malik_tc = trim($_POST['malik_tc']);
    $adres = trim($_POST['adres']);
    $koordinat_x = !empty($_POST['koordinat_x']) ? $_POST['koordinat_x'] : null;
    $koordinat_y = !empty($_POST['koordinat_y']) ? $_POST['koordinat_y'] : null;
    $aciklama = trim($_POST['aciklama']);
    $user_id = $_SESSION['user_id'];
    
    // Validasyon
    $errors = [];
    
    if (empty($ada_no)) {
        $errors[] = 'Ada numarası gereklidir.';
    }
    
    if (empty($parsel_no)) {
        $errors[] = 'Parsel numarası gereklidir.';
    }
    
    if (empty($il)) {
        $errors[] = 'İl seçimi gereklidir.';
    }
    
    if (empty($ilce)) {
        $errors[] = 'İlçe bilgisi gereklidir.';
    }
    
    if (empty($mahalle)) {
        $errors[] = 'Mahalle bilgisi gereklidir.';
    }
    
    if (empty($alan) || !is_numeric($alan) || $alan <= 0) {
        $errors[] = 'Geçerli bir alan değeri girin.';
    }
    
    if (empty($tapu_tipi)) {
        $errors[] = 'Tapu tipi seçimi gereklidir.';
    }
    
    if (empty($malik_adi)) {
        $errors[] = 'Malik adı gereklidir.';
    }
    
    if (empty($malik_soyadi)) {
        $errors[] = 'Malik soyadı gereklidir.';
    }
    
    if (empty($malik_tc) || !preg_match('/^\d{11}$/', $malik_tc)) {
        $errors[] = 'Geçerli bir TC Kimlik Numarası girin (11 hane).';
    }
    
    // TC Kimlik No validasyonu
    if (!empty($malik_tc) && strlen($malik_tc) == 11) {
        if (!validateTCKN($malik_tc)) {
            $errors[] = 'Geçerli bir TC Kimlik Numarası girin.';
        }
    }
    
    // Ad ve soyad sadece harf kontrolü
    if (!preg_match('/^[a-zA-ZçğıöşüÇĞIİÖŞÜ\s]+$/', $malik_adi)) {
        $errors[] = 'Malik adı sadece harf içermelidir.';
    }
    
    if (!preg_match('/^[a-zA-ZçğıöşüÇĞIİÖŞÜ\s]+$/', $malik_soyadi)) {
        $errors[] = 'Malik soyadı sadece harf içermelidir.';
    }
    
    // Koordinat validasyonu
    if (!empty($koordinat_x) && (!is_numeric($koordinat_x) || $koordinat_x < -90 || $koordinat_x > 90)) {
        $errors[] = 'Geçerli bir enlem değeri girin (-90 ile 90 arasında).';
    }
    
    if (!empty($koordinat_y) && (!is_numeric($koordinat_y) || $koordinat_y < -180 || $koordinat_y > 180)) {
        $errors[] = 'Geçerli bir boylam değeri girin (-180 ile 180 arasında).';
    }
    
    // Hata varsa geri dön
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: add.php');
        exit;
    }
    
    try {
        // Aynı ada/parsel kombinasyonunun var olup olmadığını kontrol et
        $check_query = "SELECT id FROM parcels WHERE ada_no = ? AND parsel_no = ? AND il = ? AND ilce = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$ada_no, $parsel_no, $il, $ilce]);
        
        if ($check_stmt->fetch()) {
            $_SESSION['error'] = 'Bu ada/parsel kombinasyonu zaten mevcut.';
            header('Location: add.php');
            exit;
        }
        
        // Parseli veritabanına kaydet
        $insert_query = "INSERT INTO parcels (
            user_id, ada_no, parsel_no, il, ilce, mahalle, alan, tapu_tipi, 
            malik_adi, malik_soyadi, malik_tc, adres, koordinat_x, koordinat_y, aciklama
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->execute([
            $user_id, $ada_no, $parsel_no, $il, $ilce, $mahalle, $alan, $tapu_tipi,
            $malik_adi, $malik_soyadi, $malik_tc, $adres, $koordinat_x, $koordinat_y, $aciklama
        ]);
        
        $_SESSION['success'] = 'Parsel başarıyla eklendi.';
        header('Location: ../dashboard.php');
        exit;
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = 'Bu parsel zaten kayıtlı.';
        } else {
            $_SESSION['error'] = 'Bir hata oluştu. Lütfen tekrar deneyiniz.';
        }
        header('Location: add.php');
        exit;
    }
    
} else {
    header('Location: add.php');
    exit;
}

// TC Kimlik No validasyon fonksiyonu
function validateTCKN($tcno) {
    if (strlen($tcno) !== 11) return false;
    
    // İlk hane 0 olamaz
    if ($tcno[0] === '0') return false;
    
    // Tüm haneler aynı olamaz
    if (preg_match('/^(.)\1{10}$/', $tcno)) return false;
    
    $digits = str_split($tcno);
    $digits = array_map('intval', $digits);
    
    // 10. hane kontrolü
    $oddSum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
    $evenSum = $digits[1] + $digits[3] + $digits[5] + $digits[7];
    
    if (($oddSum * 7 - $evenSum) % 10 !== $digits[9]) return false;
    
    // 11. hane kontrolü
    $totalSum = array_sum(array_slice($digits, 0, 10));
    if ($totalSum % 10 !== $digits[10]) return false;
    
    return true;
}
?> 