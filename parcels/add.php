<?php
session_start();
require_once '../config/database.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
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
    <title>Yeni Parsel Ekle - KMPBS</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Theme CSS -->
    <link href="../assets/css/theme.css" rel="stylesheet">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-modern">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-map-marked-alt me-2"></i>KMPBS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
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
                        <a class="nav-link active" href="add.php">
                            <i class="fas fa-plus me-1"></i>Yeni Parsel
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../profile.php"><i class="fas fa-user-edit me-2"></i>Profili Düzenle</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
        <!-- Başlık -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; border-radius: var(--border-radius-lg);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="fw-bold mb-2">
                                    <i class="fas fa-plus-circle me-2"></i>Yeni Parsel Ekle
                                </h2>
                                <p class="opacity-75 mb-0 fs-5">Sisteme yeni parsel bilgisi ekleyin</p>
                            </div>
                            <div class="col-auto">
                                <a href="list.php" class="btn btn-light text-primary fw-semibold">
                                    <i class="fas fa-list me-2"></i>Parsel Listesi
                                </a>
                            </div>
                        </div>
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
        <div class="row">
            <div class="col-12">
                <div class="card fade-in">
                    <div class="card-body p-4">
                        <form action="process_add.php" method="POST" class="needs-validation" novalidate>
                            <!-- Parsel Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-map me-2"></i>Parsel Bilgileri
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ada_no" class="form-label">Ada No</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-hashtag"></i></span>
                                        <input type="text" class="form-control" id="ada_no" name="ada_no" required>
                                        <div class="invalid-feedback">
                                            Ada numarası gereklidir.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="parsel_no" class="form-label">Parsel No</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-hashtag"></i></span>
                                        <input type="text" class="form-control" id="parsel_no" name="parsel_no" required>
                                        <div class="invalid-feedback">
                                            Parsel numarası gereklidir.
                                        </div>
                                    </div>
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
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-map-pin"></i></span>
                                        <select class="form-select" id="il" name="il" required>
                                            <option value="">İl Seçiniz</option>
                                            <?php foreach ($provinces as $province): ?>
                                                <option value="<?php echo $province; ?>"><?php echo $province; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            İl seçimi gereklidir.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="ilce" class="form-label">İlçe</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-map-pin"></i></span>
                                        <input type="text" class="form-control" id="ilce" name="ilce" required>
                                        <div class="invalid-feedback">
                                            İlçe bilgisi gereklidir.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="mahalle" class="form-label">Mahalle</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-map-pin"></i></span>
                                        <input type="text" class="form-control" id="mahalle" name="mahalle" required>
                                        <div class="invalid-feedback">
                                            Mahalle bilgisi gereklidir.
                                        </div>
                                    </div>
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
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-ruler-combined"></i></span>
                                        <input type="number" class="form-control" id="alan" name="alan" step="0.01" min="0" required>
                                        <span class="input-group-text bg-primary text-white">m²</span>
                                        <div class="invalid-feedback">
                                            Geçerli bir alan değeri girin.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tapu_tipi" class="form-label">Tapu Tipi</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-file-alt"></i></span>
                                        <select class="form-select" id="tapu_tipi" name="tapu_tipi" required>
                                            <option value="">Tapu Tipi Seçiniz</option>
                                            <option value="mülk">Mülk</option>
                                            <option value="kira">Kira</option>
                                            <option value="intifa">İntifa</option>
                                            <option value="irtifak">İrtifak</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Tapu tipi seçimi gereklidir.
                                        </div>
                                    </div>
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
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="malik_adi" name="malik_adi" required>
                                        <div class="invalid-feedback">
                                            Malik adı gereklidir.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="malik_soyadi" class="form-label">Malik Soyadı</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="malik_soyadi" name="malik_soyadi" required>
                                        <div class="invalid-feedback">
                                            Malik soyadı gereklidir.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="malik_tc" class="form-label">TC Kimlik No</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="malik_tc" name="malik_tc" maxlength="11" required>
                                        <div class="invalid-feedback">
                                            Geçerli bir TC Kimlik Numarası girin.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Koordinat Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="fw-bold text-secondary mb-3">
                                        <i class="fas fa-crosshairs me-2"></i>Koordinat Bilgileri
                                        <small class="text-muted">(Opsiyonel)</small>
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="koordinat_x" class="form-label">Enlem (X)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-crosshairs"></i></span>
                                        <input type="number" class="form-control coordinate-input" id="koordinat_x" name="koordinat_x" step="0.000001" placeholder="41.0082">
                                        <div class="invalid-feedback">
                                            Geçerli bir enlem değeri girin.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="koordinat_y" class="form-label">Boylam (Y)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-crosshairs"></i></span>
                                        <input type="number" class="form-control coordinate-input" id="koordinat_y" name="koordinat_y" step="0.000001" placeholder="28.9784">
                                        <div class="invalid-feedback">
                                            Geçerli bir boylam değeri girin.
                                        </div>
                                    </div>
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
                                    <textarea class="form-control" id="adres" name="adres" rows="3" placeholder="Detaylı adres bilgisi..."></textarea>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="aciklama" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="aciklama" name="aciklama" rows="3" placeholder="Parsel hakkında ek bilgiler..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Form Butonları -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="../dashboard.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>İptal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Parsel Kaydet
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

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success/error messages as toasts
            <?php if (isset($_SESSION['success'])): ?>
                KMPBSTheme.showToast('success', '<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>');
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                KMPBSTheme.showToast('error', '<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>');
            <?php endif; ?>
        });
    </script>
</body>
</html> 