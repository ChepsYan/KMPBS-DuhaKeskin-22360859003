<?php
session_start();
require_once 'config/database.php';

// Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMPBS - Giriş</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Theme CSS -->
    <link href="assets/css/theme.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 2rem 1rem;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            overflow: hidden;
            position: relative;
            margin: auto;
        }

        .left-panel {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            height: 100%;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .left-panel > * {
            position: relative;
            z-index: 2;
        }

        .logo-section {
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .logo-icon i {
            font-size: 3.5rem;
            color: white;
        }

        .system-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .system-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .features-list {
            list-style: none;
            text-align: left;
            max-width: 300px;
        }

        .features-list li {
            padding: 0.7rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.95rem;
            opacity: 0.9;
            transition: all 0.3s ease;
        }

        .features-list li:hover {
            opacity: 1;
            transform: translateX(5px);
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list i {
            margin-right: 0.7rem;
            width: 20px;
            color: rgba(255, 255, 255, 0.8);
        }

        .right-panel {
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--gray-100);
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .custom-checkbox input[type="checkbox"] {
            opacity: 0;
            position: absolute;
        }

        .checkbox-mark {
            width: 18px;
            height: 18px;
            border: 2px solid var(--gray-400);
            border-radius: 4px;
            margin-right: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
        }

        .custom-checkbox input[type="checkbox"]:checked + .checkbox-mark {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .custom-checkbox input[type="checkbox"]:checked + .checkbox-mark i {
            color: white;
            font-size: 0.7rem;
        }

        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gray-300);
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: var(--gray-500);
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
        }

        .register-btn {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius);
            display: inline-block;
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }
            
            .login-container {
                margin: 0;
                border-radius: var(--border-radius-lg);
                min-height: auto;
                max-width: 100%;
            }

            .left-panel {
                padding: 2rem 1.5rem;
            }

            .right-panel {
                padding: 2rem 1.5rem;
            }

            .system-title {
                font-size: 2rem;
            }

            .form-title {
                font-size: 1.5rem;
            }
            
            .row.h-100 {
                min-height: auto;
            }
        }
    </style>
</head>
<body class="min-vh-100">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center ">
            <div class="col-12 col-xl-10">
                <div class="login-container slide-in-left">
                    <div class="row h-100">
                        <!-- Sol Panel -->
                        <div class="col-lg-6 p-0">
                            <div class="left-panel">
                                <div class="logo-section">
                                    <div class="logo-icon">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <h1 class="system-title">KMPBS</h1>
                                    <p class="system-subtitle">Konumsal Mülkiyet ve Parsel Bilgi Sistemi</p>
                                </div>

                                <ul class="features-list">
                                    <li><i class="fas fa-plus-circle"></i>Ada-Parsel Oluşturma</li>
                                    <li><i class="fas fa-list-ul"></i>Parsel Listeleme</li>
                                    <li><i class="fas fa-edit"></i>Parsel Düzenleme</li>
                                    <li><i class="fas fa-trash-alt"></i>Parsel Silme</li>
                                    <li><i class="fas fa-map"></i>Konumsal Bilgi Yönetimi</li>
                                    <li><i class="fas fa-shield-alt"></i>Güvenli Veri Saklama</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sağ Panel -->
                        <div class="col-lg-6 p-0">
                            <div class="right-panel">
                                <div class="form-header">
                                    <h2 class="form-title">Hoş Geldiniz</h2>
                                    <p class="form-subtitle">Hesabınıza giriş yapın</p>
                                </div>

                                <form id="loginForm" method="POST" action="auth/login.php">
                                    <div class="form-group">
                                        <label for="email" class="form-label">E-posta Adresi</label>
                                        <div class="input-wrapper">
                                            <input type="email" id="email" name="email" class="form-control" required>
                                            <i class="fas fa-envelope input-icon"></i>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="form-label">Şifre</label>
                                        <div class="input-wrapper">
                                            <input type="password" id="password" name="password" class="form-control" required>
                                            <i class="fas fa-lock input-icon"></i>
                                            <button type="button" class="password-toggle" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-options">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" id="remember" name="remember">
                                            <span class="checkbox-mark">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            Beni hatırla
                                        </label>
                                        <a href="#" class="forgot-link">Şifremi unuttum</a>
                                    </div>

                                    <button type="submit" class="login-btn" id="loginBtn">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Giriş Yap
                                    </button>
                                </form>

                                <div class="divider">
                                    <span>veya</span>
                                </div>

                                <div class="register-link">
                                    <a href="register.php" class="register-btn">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Hesap Oluştur
                                    </a>
                                </div>
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
    <script src="assets/js/theme.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show PHP messages as toasts
            <?php if (isset($_SESSION['error'])): ?>
                KMPBSTheme.showToast('error', '<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>');
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                KMPBSTheme.showToast('success', '<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>');
            <?php endif; ?>
        });
    </script>
</body>
</html> 