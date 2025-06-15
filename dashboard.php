<?php
session_start();
require_once 'config/database.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Kullanıcının parsellerini getir
try {
    // Check if database connection is available
    if (!$db) {
        throw new Exception("Database connection not available");
    }
    
    $query = "SELECT * FROM parcels WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // İstatistikler
    $total_parcels = count($parcels);
    $total_area = array_sum(array_column($parcels, 'alan'));
    
    // En çok kullanılan il
    $provinces = array_count_values(array_column($parcels, 'il'));
    $most_common_province = $provinces ? array_key_first($provinces) : 'Henüz veri yok';
    
} catch (Exception $e) {
    $parcels = [];
    $total_parcels = 0;
    $total_area = 0;
    $most_common_province = 'Veritabanı Hatası';
    
    // Set error message if not already set
    if (!isset($_SESSION['error'])) {
        $_SESSION['error'] = "Veritabanı bağlantı sorunu. Lütfen XAMPP MySQL servisinin çalıştığından emin olun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Paneli - KMPBS</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Theme CSS -->
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-modern">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-map-marked-alt me-2"></i>KMPBS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Anasayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parcels/list.php">
                            <i class="fas fa-list me-1"></i>Parsel Listesi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parcels/add.php">
                            <i class="fas fa-plus me-1"></i>Yeni Parsel
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit me-2"></i>Profili Düzenle</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
        <!-- Hoş Geldin Kartı -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; border-radius: var(--border-radius-lg);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="card-title mb-2 fw-bold">Hoş geldiniz, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</h2>
                                <p class="card-text opacity-75 mb-0 fs-5">Konumsal Mülkiyet ve Parsel Bilgi Sistemi'ne hoş geldiniz. Parsellerinizi buradan yönetebilirsiniz.</p>
                            </div>
                            <div class="col-auto">
                                <div class="text-white-50">
                                    <i class="fas fa-map-marked-alt" style="font-size: 4rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card fade-in">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--info-color));">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_parcels; ?></div>
                    <div class="stat-label">Toplam Parsel</div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="stat-card fade-in">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), var(--secondary-color));">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($total_area, 0); ?> m²</div>
                    <div class="stat-label">Toplam Alan</div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="stat-card fade-in">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--info-color), var(--primary-dark));">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-number text-truncate" style="font-size: 1.8rem;"><?php echo $most_common_province; ?></div>
                    <div class="stat-label">En Çok Parsel Olan İl</div>
                </div>
            </div>
        </div>
        
        <!-- Hızlı İşlemler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--gray-100), white); border-bottom: 2px solid var(--primary-color);">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-bolt me-2"></i>Hızlı İşlemler
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <a href="parcels/add.php" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none py-4">
                                    <i class="fas fa-plus fa-2x mb-3"></i>
                                    <span class="fw-semibold">Yeni Parsel Ekle</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="parcels/list.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none py-4">
                                    <i class="fas fa-list fa-2x mb-3"></i>
                                    <span class="fw-semibold">Parsel Listesi</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="parcels/list.php" class="btn btn-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none py-4">
                                    <i class="fas fa-search fa-2x mb-3"></i>
                                    <span class="fw-semibold">Parsel Ara</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="parcels/list.php" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none py-4">
                                    <i class="fas fa-download fa-2x mb-3"></i>
                                    <span class="fw-semibold">Rapor Al</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Son Eklenen Parseller -->
        <div class="row">
            <div class="col-12">
                <div class="card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--gray-100), white); border-bottom: 2px solid var(--secondary-color);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-secondary">
                                <i class="fas fa-clock me-2"></i>Son Eklenen Parseller
                            </h5>
                            <a href="parcels/list.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i>Tümünü Gör
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($parcels) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ada/Parsel</th>
                                            <th>İl/İlçe</th>
                                            <th>Mahalle</th>
                                            <th>Alan (m²)</th>
                                            <th>Tapu Tipi</th>
                                            <th>Eklenme Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $recent_parcels = array_slice($parcels, 0, 5);
                                        foreach ($recent_parcels as $parcel): 
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold text-primary"><?php echo htmlspecialchars($parcel['ada_no']); ?></span> / 
                                                    <span class="text-muted"><?php echo htmlspecialchars($parcel['parsel_no']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($parcel['il'] . ' / ' . $parcel['ilce']); ?></td>
                                                <td><?php echo htmlspecialchars($parcel['mahalle']); ?></td>
                                                <td>
                                                    <span class="badge bg-success-subtle text-success fw-semibold">
                                                        <?php echo number_format($parcel['alan'], 0); ?> m²
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        <?php echo ucfirst($parcel['tapu_tipi']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted">
                                                    <?php echo date('d.m.Y', strtotime($parcel['created_at'])); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="parcels/view.php?id=<?php echo $parcel['id']; ?>" 
                                                           class="btn btn-outline-primary btn-sm" title="Görüntüle">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="parcels/edit.php?id=<?php echo $parcel['id']; ?>" 
                                                           class="btn btn-outline-warning btn-sm" title="Düzenle">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-map fa-3x mb-3 opacity-50"></i>
                                    <h5>Henüz parsel eklenmemiş</h5>
                                    <p>İlk parselinizi eklemek için aşağıdaki butona tıklayın</p>
                                    <a href="parcels/add.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>İlk Parseli Ekle
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    
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