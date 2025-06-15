<?php
session_start();

// Oturumu temizle
session_unset();
session_destroy();

// Cookie'leri temizle
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Ana sayfaya yönlendir
header('Location: ../index.php');
exit;
?> 