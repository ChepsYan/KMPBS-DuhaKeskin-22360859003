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
    // Parseli getir
    $query = "SELECT * FROM parcels WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$parcel_id, $user_id]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcel) {
        $_SESSION['error'] = 'Parsel bulunamadı veya bu işlem için yetkiniz yok.';
        header('Location: list.php');
        exit;
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Bir hata oluştu.';
    header('Location: list.php');
    exit;
}

// Form gönderilmişse güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    
    // Basit validasyon
    if (empty($ada_no) || empty($parsel_no) || empty($il) || empty($ilce) || empty($mahalle) || 
        empty($alan) || empty($tapu_tipi) || empty($malik_adi) || empty($malik_soyadi) || empty($malik_tc)) {
        $_SESSION['error'] = 'Zorunlu alanları doldurun.';
    } else {
        try {
            $update_query = "UPDATE parcels SET 
                ada_no = ?, parsel_no = ?, il = ?, ilce = ?, mahalle = ?, alan = ?, tapu_tipi = ?,
                malik_adi = ?, malik_soyadi = ?, malik_tc = ?, adres = ?, 
                koordinat_x = ?, koordinat_y = ?, aciklama = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND user_id = ?";
            
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([
                $ada_no, $parsel_no, $il, $ilce, $mahalle, $alan, $tapu_tipi,
                $malik_adi, $malik_soyadi, $malik_tc, $adres, $koordinat_x, $koordinat_y, $aciklama,
                $parcel_id, $user_id
            ]);
            
            $_SESSION['success'] = 'Parsel başarıyla güncellendi.';
            header('Location: list.php');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Güncellenirken bir hata oluştu.';
        }
    }
}

// Türkiye illeri
$provinces = [
    'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Amasya', 'Ankara', 'Antalya', 
    'Artvin', 'Aydın', 'Balıkesir', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 
    'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 
    'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 
    'Gümüşhane', 'Hakkari', 'Hatay', 'Isparta', 'Mersin', 'İstanbul', 'İzmir', 
    'Kars', 'Kastamonu', 'Kayseri', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 
    'Kütahya', 'Malatya', 'Manisa', 'Kahramanmaraş', 'Mardin', 'Muğla', 'Muş', 
    'Nevşehir', 'Niğde', 'Ordu', 'Rize', 'Sakarya', 'Samsun', 'Siirt', 'Sinop', 
    'Sivas', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Şanlıurfa', 'Uşak', 
    'Van', 'Yozgat', 'Zonguldak', 'Aksaray', 'Bayburt', 'Karaman', 'Kırıkkale', 
    'Batman', 'Şırnak', 'Bartın', 'Ardahan', 'Iğdır', 'Yalova', 'Karabük', 'Kilis', 
    'Osmaniye', 'Düzce'
];
sort($provinces);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parsel Düzenle - KMPBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-map-marked-alt me-2"></i>KMPBS
            </a>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-home me-1"></i>Anasayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="list.php">
                            <i class="fas fa-list me-1"></i>Parsel Listesi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add.php">
                            <i class="fas fa-plus me-1"></i>Yeni Parsel
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Başlık -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="fw-bold text-primary mb-1">
                                    <i class="fas fa-edit me-2"></i>Parsel Düzenle
                                </h2>
                                <p class="text-muted mb-0">
                                    <?php echo htmlspecialchars($parcel['ada_no'] . '/' . $parcel['parsel_no']); ?> 
                                    - <?php echo htmlspecialchars($parcel['il'] . ', ' . $parcel['ilce']); ?>
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="list.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Geri Dön
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hata/Başarı Mesajları -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" class="needs-validation" novalidate>
                            <!-- Parsel Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-map me-2"></i>Parsel Bilgileri
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ada_no" class="form-label">Ada No</label>
                                    <input type="text" class="form-control" id="ada_no" name="ada_no" 
                                           value="<?php echo htmlspecialchars($parcel['ada_no']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="parsel_no" class="form-label">Parsel No</label>
                                    <input type="text" class="form-control" id="parsel_no" name="parsel_no" 
                                           value="<?php echo htmlspecialchars($parcel['parsel_no']); ?>" required>
                                </div>
                            </div>
                            
                            <!-- Konum Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>Konum Bilgileri
                                    </h5>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="il" class="form-label">İl</label>
                                    <select class="form-select" id="il" name="il" required>
                                        <option value="">İl Seçiniz</option>
                                        <?php foreach ($provinces as $province): ?>
                                            <option value="<?php echo $province; ?>" 
                                                    <?php echo $parcel['il'] === $province ? 'selected' : ''; ?>>
                                                <?php echo $province; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="ilce" class="form-label">İlçe</label>
                                    <input type="text" class="form-control" id="ilce" name="ilce" 
                                           value="<?php echo htmlspecialchars($parcel['ilce']); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="mahalle" class="form-label">Mahalle</label>
                                    <input type="text" class="form-control" id="mahalle" name="mahalle" 
                                           value="<?php echo htmlspecialchars($parcel['mahalle']); ?>" required>
                                </div>
                            </div>
                            
                            <!-- Teknik Bilgiler -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-ruler-combined me-2"></i>Teknik Bilgiler
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="alan" class="form-label">Alan (m²)</label>
                                    <input type="number" class="form-control" id="alan" name="alan" step="0.01" 
                                           value="<?php echo $parcel['alan']; ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tapu_tipi" class="form-label">Tapu Tipi</label>
                                    <select class="form-select" id="tapu_tipi" name="tapu_tipi" required>
                                        <option value="">Tapu Tipi Seçiniz</option>
                                        <option value="mülk" <?php echo $parcel['tapu_tipi'] === 'mülk' ? 'selected' : ''; ?>>Mülk</option>
                                        <option value="kira" <?php echo $parcel['tapu_tipi'] === 'kira' ? 'selected' : ''; ?>>Kira</option>
                                        <option value="intifa" <?php echo $parcel['tapu_tipi'] === 'intifa' ? 'selected' : ''; ?>>İntifa</option>
                                        <option value="irtifak" <?php echo $parcel['tapu_tipi'] === 'irtifak' ? 'selected' : ''; ?>>İrtifak</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Malik Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-user me-2"></i>Malik Bilgileri
                                    </h5>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="malik_adi" class="form-label">Malik Adı</label>
                                    <input type="text" class="form-control" id="malik_adi" name="malik_adi" 
                                           value="<?php echo htmlspecialchars($parcel['malik_adi']); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="malik_soyadi" class="form-label">Malik Soyadı</label>
                                    <input type="text" class="form-control" id="malik_soyadi" name="malik_soyadi" 
                                           value="<?php echo htmlspecialchars($parcel['malik_soyadi']); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="malik_tc" class="form-label">TC Kimlik No</label>
                                    <input type="text" class="form-control" id="malik_tc" name="malik_tc" 
                                           value="<?php echo htmlspecialchars($parcel['malik_tc']); ?>" maxlength="11" required>
                                </div>
                            </div>
                            
                            <!-- Koordinat Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-crosshairs me-2"></i>Koordinat Bilgileri
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="koordinat_x" class="form-label">Enlem (X)</label>
                                    <input type="number" class="form-control" id="koordinat_x" name="koordinat_x" step="0.000001" 
                                           value="<?php echo $parcel['koordinat_x']; ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="koordinat_y" class="form-label">Boylam (Y)</label>
                                    <input type="number" class="form-control" id="koordinat_y" name="koordinat_y" step="0.000001" 
                                           value="<?php echo $parcel['koordinat_y']; ?>">
                                </div>
                            </div>
                            
                            <!-- Adres ve Açıklama -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Ek Bilgiler
                                    </h5>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="adres" class="form-label">Adres</label>
                                    <textarea class="form-control" id="adres" name="adres" rows="3"><?php echo htmlspecialchars($parcel['adres']); ?></textarea>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="aciklama" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="aciklama" name="aciklama" rows="3"><?php echo htmlspecialchars($parcel['aciklama']); ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Form Butonları -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="list.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>İptal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 