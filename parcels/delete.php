<?php
session_start();
require_once '../config/database.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Geçersiz parsel ID\'si.';
    header('Location: list.php');
    exit;
}

$parcel_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Önce parselin kullanıcıya ait olup olmadığını kontrol et
    $check_query = "SELECT ada_no, parsel_no FROM parcels WHERE id = ? AND user_id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$parcel_id, $user_id]);
    $parcel = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcel) {
        $_SESSION['error'] = 'Parsel bulunamadı veya bu işlem için yetkiniz yok.';
        header('Location: list.php');
        exit;
    }
    
    // Parseli sil
    $delete_query = "DELETE FROM parcels WHERE id = ? AND user_id = ?";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->execute([$parcel_id, $user_id]);
    
    if ($delete_stmt->rowCount() > 0) {
        $_SESSION['success'] = 'Parsel "' . $parcel['ada_no'] . '/' . $parcel['parsel_no'] . '" başarıyla silindi.';
    } else {
        $_SESSION['error'] = 'Parsel silinemedi.';
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Bir hata oluştu. Lütfen tekrar deneyiniz.';
}

header('Location: list.php');
exit;
?> 