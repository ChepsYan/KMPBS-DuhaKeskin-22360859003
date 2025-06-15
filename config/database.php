<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'system';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Check if PDO and MySQL driver are available
            if (!extension_loaded('pdo')) {
                throw new Exception("PDO extension is not loaded");
            }
            
            if (!extension_loaded('pdo_mysql')) {
                throw new Exception("PDO MySQL driver is not loaded");
            }
            
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", 
                                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Bağlantı hatası: " . $exception->getMessage();
            return null;
        } catch(Exception $exception) {
            echo "Sistem hatası: " . $exception->getMessage();
            return null;
        }
        
        return $this->conn;
    }
    
    public function createTables() {
        if (!$this->conn) {
            return false;
        }
        
        try {
            // Kullanıcılar tablosu
            $users_table = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            // Parseller tablosu
            $parcels_table = "CREATE TABLE IF NOT EXISTS parcels (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                ada_no VARCHAR(20) NOT NULL,
                parsel_no VARCHAR(20) NOT NULL,
                il VARCHAR(50) NOT NULL,
                ilce VARCHAR(50) NOT NULL,
                mahalle VARCHAR(100) NOT NULL,
                alan DECIMAL(12,2) NOT NULL,
                tapu_tipi ENUM('mülk', 'kira', 'intifa', 'irtifak') NOT NULL,
                malik_adi VARCHAR(100) NOT NULL,
                malik_soyadi VARCHAR(100) NOT NULL,
                malik_tc VARCHAR(11) NOT NULL,
                adres TEXT,
                koordinat_x DECIMAL(10,8),
                koordinat_y DECIMAL(11,8),
                aciklama TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_parcel (ada_no, parsel_no, il, ilce)
            )";
            
            $this->conn->exec($users_table);
            $this->conn->exec($parcels_table);
            
            return true;
        } catch(PDOException $exception) {
            echo "Tablo oluşturma hatası: " . $exception->getMessage();
            return false;
        }
    }
}

// Global veritabanı bağlantısı
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        // Tabloları oluştur
        $database->createTables();
    } else {
        // Fallback: Mock database object for testing
        $db = null;
        
        // Create error message for users
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['db_error_shown'])) {
            $_SESSION['error'] = "Veritabanı bağlantısı kurulamadı. Lütfen XAMPP MySQL servisinin çalıştığından emin olun.";
            $_SESSION['db_error_shown'] = true;
        }
    }
} catch(Exception $e) {
    $db = null;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['error'] = "Veritabanı hatası: " . $e->getMessage();
}

// Compatibility layer for old $pdo variable name
$pdo = $db;
?> 