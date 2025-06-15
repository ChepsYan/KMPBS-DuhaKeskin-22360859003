<?php
session_start();
require_once '../config/database.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Parsel ID kontrolü
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Geçersiz parsel ID";
    header('Location: list.php');
    exit;
}

$parcel_id = (int)$_GET['id'];

try {
    // Parsel bilgilerini getir (kullanıcı kontrolü ile)
    $query = "SELECT * FROM parcels WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$parcel_id, $_SESSION['user_id']]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcel) {
        $_SESSION['error'] = "Parsel bulunamadı veya erişim yetkiniz yok";
        header('Location: list.php');
        exit;
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Veritabanı hatası oluştu";
    header('Location: list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parsel Detayı - KMPBS</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Theme CSS -->
    <link href="../assets/css/theme.css" rel="stylesheet">
    
    <style>
        .info-item {
            margin-bottom: 1.5rem;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .info-value {
            color: var(--gray-800);
            font-size: 1rem;
            padding: 0.75rem 1rem;
            background: var(--gray-100);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }
        
        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
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
                        <a class="nav-link" href="add.php">
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

    <div class="container py-5">
        <!-- Başlık -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; border-radius: var(--border-radius-lg);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="fw-bold mb-2">
                                    <i class="fas fa-eye me-2"></i>Parsel Detayı
                                </h2>
                                <p class="opacity-75 mb-0">
                                    Ada: <?php echo htmlspecialchars($parcel['ada_no']); ?> - Parsel: <?php echo htmlspecialchars($parcel['parsel_no']); ?>
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $parcel['id']; ?>" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Düzenle
                                    </a>
                                    <a href="list.php" class="btn btn-outline-light">
                                        <i class="fas fa-arrow-left me-2"></i>Listeye Dön
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Parsel Bilgileri -->
            <div class="col-lg-8">
                <div class="card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--gray-100), white); border-bottom: 2px solid var(--primary-color);">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-map me-2"></i>Parsel Bilgileri
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Ada No</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['ada_no']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Parsel No</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['parsel_no']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="info-label">İl</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['il']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="info-label">İlçe</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['ilce']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="info-label">Mahalle</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['mahalle']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Alan (m²)</label>
                                    <div class="info-value">
                                        <span class="badge bg-success fs-6"><?php echo number_format($parcel['alan'], 2); ?> m²</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Tapu Tipi</label>
                                    <div class="info-value">
                                        <span class="badge bg-primary fs-6"><?php echo ucfirst($parcel['tapu_tipi']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($parcel['koordinat_x']) && !empty($parcel['koordinat_y'])): ?>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Koordinat X</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['koordinat_x']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Koordinat Y</label>
                                    <div class="info-value"><?php echo htmlspecialchars($parcel['koordinat_y']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($parcel['adres'])): ?>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="info-label">Adres</label>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($parcel['adres'])); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($parcel['aciklama'])): ?>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="info-label">Açıklama</label>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($parcel['aciklama'])); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Malik Bilgileri -->
            <div class="col-lg-4">
                <div class="card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--gray-100), white); border-bottom: 2px solid var(--secondary-color);">
                        <h5 class="card-title mb-0 text-secondary">
                            <i class="fas fa-user me-2"></i>Malik Bilgileri
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="avatar-circle bg-secondary text-white mb-3">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5 class="fw-bold"><?php echo htmlspecialchars($parcel['malik_adi'] . ' ' . $parcel['malik_soyadi']); ?></h5>
                            <p class="text-muted mb-0">TC: <?php echo htmlspecialchars($parcel['malik_tc']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Tarih Bilgileri -->
                <div class="card fade-in mt-4">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--gray-100), white); border-bottom: 2px solid var(--info-color);">
                        <h5 class="card-title mb-0 text-info">
                            <i class="fas fa-clock me-2"></i>Tarih Bilgileri
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item mb-3">
                            <label class="info-label">Oluşturulma Tarihi</label>
                            <div class="info-value">
                                <i class="fas fa-calendar text-success me-2"></i>
                                <?php echo date('d.m.Y H:i', strtotime($parcel['created_at'])); ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Son Güncelleme</label>
                            <div class="info-value">
                                <i class="fas fa-edit text-warning me-2"></i>
                                <?php echo date('d.m.Y H:i', strtotime($parcel['updated_at'])); ?>
                            </div>
                        </div>
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
</body>
</html> 