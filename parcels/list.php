<?php
session_start();
require_once '../config/database.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Sayfalama ayarları
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Arama ve filtreleme
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_il = isset($_GET['filter_il']) ? trim($_GET['filter_il']) : '';
$filter_tapu = isset($_GET['filter_tapu']) ? trim($_GET['filter_tapu']) : '';

try {
    // Check if database connection is available
    if (!$db) {
        throw new Exception("Database connection not available");
    }
    
    // Base query
    $where_conditions = ["user_id = ?"];
    $params = [$_SESSION['user_id']];
    
    // Arama koşulları
    if (!empty($search)) {
        $where_conditions[] = "(ada_no LIKE ? OR parsel_no LIKE ? OR malik_adi LIKE ? OR malik_soyadi LIKE ? OR mahalle LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    }
    
    if (!empty($filter_il)) {
        $where_conditions[] = "il = ?";
        $params[] = $filter_il;
    }
    
    if (!empty($filter_tapu)) {
        $where_conditions[] = "tapu_tipi = ?";
        $params[] = $filter_tapu;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Toplam kayıt sayısı
    $count_query = "SELECT COUNT(*) FROM parcels WHERE $where_clause";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);
    
    // Parsel listesi
    $query = "SELECT * FROM parcels WHERE $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // İl listesi (filtre için)
    $il_query = "SELECT DISTINCT il FROM parcels WHERE user_id = ? ORDER BY il";
    $il_stmt = $db->prepare($il_query);
    $il_stmt->execute([$_SESSION['user_id']]);
    $provinces = $il_stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    $parcels = [];
    $total_records = 0;
    $total_pages = 0;
    $provinces = [];
    
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
    <title>Parsel Listesi - KMPBS</title>
    
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
                        <a class="nav-link active" href="list.php">
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
    
    <div class="container-fluid mt-4">
        <!-- Başlık ve İstatistikler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; border-radius: var(--border-radius-lg);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="fw-bold mb-2">
                                    <i class="fas fa-list me-2"></i>Parsel Listesi
                                </h2>
                                <p class="opacity-75 mb-0 fs-5">
                                    Toplam <?php echo $total_records; ?> parsel bulundu
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group">
                                    <a href="add.php" class="btn btn-light text-primary fw-semibold">
                                        <i class="fas fa-plus me-2"></i>Yeni Parsel
                                    </a>
                                    <button type="button" class="btn btn-outline-light" onclick="printTable()">
                                        <i class="fas fa-print me-2"></i>Yazdır
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtreler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--gray-100), white); border-bottom: 2px solid var(--secondary-color);">
                        <h5 class="card-title mb-0 text-secondary">
                            <i class="fas fa-filter me-2"></i>Filtreler ve Arama
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Arama</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control table-search" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Ada, parsel, malik adı, mahalle...">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="filter_il" class="form-label">İl Filtresi</label>
                                <select class="form-select" id="filter_il" name="filter_il">
                                    <option value="">Tüm İller</option>
                                    <?php foreach ($provinces as $province): ?>
                                        <option value="<?php echo htmlspecialchars($province); ?>" 
                                                <?php echo $filter_il === $province ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($province); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="filter_tapu" class="form-label">Tapu Tipi</label>
                                <select class="form-select" id="filter_tapu" name="filter_tapu">
                                    <option value="">Tüm Tipler</option>
                                    <option value="mülk" <?php echo $filter_tapu === 'mülk' ? 'selected' : ''; ?>>Mülk</option>
                                    <option value="kira" <?php echo $filter_tapu === 'kira' ? 'selected' : ''; ?>>Kira</option>
                                    <option value="intifa" <?php echo $filter_tapu === 'intifa' ? 'selected' : ''; ?>>İntifa</option>
                                    <option value="irtifak" <?php echo $filter_tapu === 'irtifak' ? 'selected' : ''; ?>>İrtifak</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Filtrele
                                    </button>
                                    <a href="list.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Temizle
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Parsel Tablosu -->
        <div class="row">
            <div class="col-12">
                <div class="card fade-in">
                    <div class="card-body p-0">
                        <?php if (count($parcels) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-modern mb-0" id="parcelTable">
                                    <thead>
                                        <tr>
                                            <th>Ada/Parsel</th>
                                            <th>Konum</th>
                                            <th>Mahalle</th>
                                            <th>Alan (m²)</th>
                                            <th>Tapu Tipi</th>
                                            <th>Malik</th>
                                            <th>Tarih</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($parcels as $parcel): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-primary">
                                                        <?php echo htmlspecialchars($parcel['ada_no']); ?> / <?php echo htmlspecialchars($parcel['parsel_no']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                        <div>
                                                            <div class="fw-semibold"><?php echo htmlspecialchars($parcel['il']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($parcel['ilce']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($parcel['mahalle']); ?></td>
                                                <td>
                                                    <span class="badge bg-success-subtle text-success fw-semibold fs-6">
                                                        <?php echo number_format($parcel['alan'], 0); ?> m²
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        <?php echo ucfirst($parcel['tapu_tipi']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($parcel['malik_adi'] . ' ' . $parcel['malik_soyadi']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($parcel['malik_tc']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('d.m.Y', strtotime($parcel['created_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="view.php?id=<?php echo $parcel['id']; ?>" 
                                                           class="btn btn-outline-primary btn-sm" title="Görüntüle">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit.php?id=<?php echo $parcel['id']; ?>" 
                                                           class="btn btn-outline-warning btn-sm" title="Düzenle">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="delete.php?id=<?php echo $parcel['id']; ?>" 
                                                           class="btn btn-outline-danger btn-sm btn-delete" 
                                                           data-item="<?php echo htmlspecialchars($parcel['ada_no'] . '/' . $parcel['parsel_no'] . ' parselini'); ?>" 
                                                           title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Sayfalama -->
                            <?php if ($total_pages > 1): ?>
                                <div class="card-footer bg-light">
                                    <nav aria-label="Sayfa navigasyonu">
                                        <ul class="pagination justify-content-center mb-0">
                                            <!-- İlk sayfa -->
                                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($filter_il) ? '&filter_il=' . urlencode($filter_il) : ''; ?><?php echo !empty($filter_tapu) ? '&filter_tapu=' . urlencode($filter_tapu) : ''; ?>">
                                                    <i class="fas fa-angle-double-left"></i>
                                                </a>
                                            </li>
                                            
                                            <!-- Önceki sayfa -->
                                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($filter_il) ? '&filter_il=' . urlencode($filter_il) : ''; ?><?php echo !empty($filter_tapu) ? '&filter_tapu=' . urlencode($filter_tapu) : ''; ?>">
                                                    <i class="fas fa-angle-left"></i>
                                                </a>
                                            </li>
                                            
                                            <!-- Sayfa numaraları -->
                                            <?php
                                            $start_page = max(1, $page - 2);
                                            $end_page = min($total_pages, $page + 2);
                                            
                                            for ($i = $start_page; $i <= $end_page; $i++):
                                            ?>
                                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($filter_il) ? '&filter_il=' . urlencode($filter_il) : ''; ?><?php echo !empty($filter_tapu) ? '&filter_tapu=' . urlencode($filter_tapu) : ''; ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <!-- Sonraki sayfa -->
                                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($filter_il) ? '&filter_il=' . urlencode($filter_il) : ''; ?><?php echo !empty($filter_tapu) ? '&filter_tapu=' . urlencode($filter_tapu) : ''; ?>">
                                                    <i class="fas fa-angle-right"></i>
                                                </a>
                                            </li>
                                            
                                            <!-- Son sayfa -->
                                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($filter_il) ? '&filter_il=' . urlencode($filter_il) : ''; ?><?php echo !empty($filter_tapu) ? '&filter_tapu=' . urlencode($filter_tapu) : ''; ?>">
                                                    <i class="fas fa-angle-double-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                    
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            Sayfa <?php echo $page; ?> / <?php echo $total_pages; ?> 
                                            (Toplam <?php echo $total_records; ?> kayıt)
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                    <h5>Parsel bulunamadı</h5>
                                    <p>Arama kriterlerinize uygun parsel bulunmadı</p>
                                    <a href="add.php" class="btn btn-primary">
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
        
        // Print function
        function printTable() {
            const table = document.getElementById('parcelTable').outerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Parsel Listesi</title>');
            printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="container mt-4">');
            printWindow.document.write('<h2 class="text-center mb-4">KMPBS - Parsel Listesi</h2>');
            printWindow.document.write(table);
            printWindow.document.write('</div></body></html>');
            printWindow.document.close();
            printWindow.print();
        }
        
  
    </script>
</body>
</html> 