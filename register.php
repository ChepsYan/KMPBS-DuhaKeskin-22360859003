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
    <title>KMPBS - Kayıt Ol</title>
    
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
            background: linear-gradient(135deg, var(--secondary-color) 0%, #38ef7d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 2rem 1rem;
        }

        .register-container {
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
            background: linear-gradient(135deg, var(--secondary-color) 0%, #38ef7d 100%);
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

        .welcome-section {
            margin-bottom: 2rem;
        }

        .welcome-icon {
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

        .welcome-icon i {
            font-size: 3.5rem;
            color: white;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .benefits-list {
            list-style: none;
            text-align: left;
            max-width: 300px;
            padding: 0;
            margin: 0;
        }

        .benefits-list li {
            padding: 0.7rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.95rem;
            opacity: 0.9;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .benefits-list li:hover {
            opacity: 1;
            transform: translateX(5px);
        }

        .benefits-list li:last-child {
            border-bottom: none;
        }

        .benefits-list li i {
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

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 0.8rem 0.8rem 2.6rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: var(--gray-100);
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.1);
            background: white;
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1rem;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-control:focus + .input-icon {
            color: var(--secondary-color);
        }

        .password-toggle {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            font-size: 0.95rem;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--secondary-color);
            background: rgba(17, 153, 142, 0.1);
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 0.4rem;
            transition: all 0.3s ease;
            background: var(--gray-300);
            display: none;
        }

        .password-strength.weak {
            background: linear-gradient(90deg, var(--danger-color) 30%, var(--gray-300) 30%);
        }

        .password-strength.medium {
            background: linear-gradient(90deg, var(--warning-color) 60%, var(--gray-300) 60%);
        }

        .password-strength.strong {
            background: linear-gradient(90deg, var(--success-color) 100%, var(--gray-300) 100%);
        }

        .custom-checkbox {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            user-select: none;
            margin: 1.2rem 0;
            font-size: 0.85rem;
            line-height: 1.4;
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
            margin-right: 0.8rem;
            margin-top: 0.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
            flex-shrink: 0;
        }

        .custom-checkbox input[type="checkbox"]:checked + .checkbox-mark {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .custom-checkbox input[type="checkbox"]:checked + .checkbox-mark i {
            color: white;
            font-size: 0.6rem;
        }

        .checkbox-text a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .checkbox-text a:hover {
            text-decoration: underline;
        }

        .register-btn {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #38ef7d 100%);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .register-btn:hover::before {
            left: 100%;
        }

        .divider {
            text-align: center;
            margin: 0.6rem 0;
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
            font-size: 0.8rem;
        }

        .login-link {
            text-align: center;
        }

        .login-btn {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.7rem 1.8rem;
            border: 2px solid var(--secondary-color);
            border-radius: var(--border-radius);
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .login-btn:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
        }

        /* Responsive */
        @media (min-width: 1367px) {
            .register-container {
                max-width: 1500px;
            }
        }
        
        @media (max-width: 1366px) {
            .register-container {
                max-width: 95%;
            }
            
            .right-panel {
                padding: 2rem 1.5rem;
            }
            
            .form-control {
                padding: 0.7rem 0.7rem 0.7rem 2.3rem;
                font-size: 0.88rem;
            }
            
            .input-icon {
                left: 0.7rem;
                font-size: 0.95rem;
            }
        }
        
        @media (max-width: 1200px) {
            .register-container {
                max-width: 98%;
            }
            
            .right-panel {
                padding: 1.8rem 1.3rem;
            }
        }
        
        @media (max-width: 992px) {
            body {
                padding: 1rem 0.5rem;
            }
            
            .register-container {
                max-width: 95%;
            }
            
            .left-panel, .right-panel {
                padding: 2rem 1.5rem;
                max-height: none;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-group {
                margin-bottom: 0.8rem;
            }
            
            .welcome-title {
                font-size: 1.8rem;
            }

            .form-title {
                font-size: 1.4rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }
            
            .register-container {
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

            .welcome-title {
                font-size: 2rem;
            }

            .form-title {
                font-size: 1.5rem;
            }
            
            .row.h-100 {
                min-height: auto;
            }
            
            .custom-checkbox {
                font-size: 0.75rem;
            }
            
            .checkbox-mark {
                width: 14px;
                height: 14px;
                margin-right: 0.5rem;
            }
        }
    </style>
</head>
<body class="min-vh-100">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-xl-10">
                <div class="register-container slide-in-left">
                    <div class="row h-100">
                        <!-- Sol Panel -->
                        <div class="col-lg-6 p-0">
                            <div class="left-panel">
                                <div class="welcome-section">
                                    <div class="welcome-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <h1 class="welcome-title">Aramıza Katılın</h1>
                                    <p class="welcome-subtitle">KMPBS ile parsel bilgilerinizi güvenle yönetin ve kolayca erişin</p>
                                </div>

                                <ul class="benefits-list">
                                    <li><i class="fas fa-shield-alt"></i>Güvenli veri saklama</li>
                                    <li><i class="fas fa-database"></i>Merkezi parsel yönetimi</li>
                                    <li><i class="fas fa-search"></i>Hızlı arama ve filtreleme</li>
                                    <li><i class="fas fa-mobile-alt"></i>Mobil uyumlu arayüz</li>
                                    <li><i class="fas fa-clock"></i>7/24 erişim imkanı</li>
                                    <li><i class="fas fa-users"></i>Çoklu kullanıcı desteği</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sağ Panel -->
                        <div class="col-lg-6 p-0">
                            <div class="right-panel">
                                <div class="form-header" style="text-align: center; margin-bottom: 1.2rem;">
                                    <h2 class="form-title" style="font-size: 1.2rem !important; font-weight: 700; color: #11998e; margin-bottom: 0.3rem;">Hesap Oluşturun</h2>
                                    <p class="form-subtitle" style="color: #6b7280; font-size: 0.8rem; margin-bottom: 0;">Bilgilerinizi girerek hemen başlayın</p>
                                </div>

                                <form id="registerForm" method="POST" action="auth/register.php">
                                    <div class="form-row" style="display: flex; gap: 0.8rem; margin-bottom: 0;">
                                        <div class="form-group" style="margin-bottom: 0.8rem; position: relative; flex: 1;">
                                            <label for="first_name" class="form-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151; font-size: 0.85rem;">Ad</label>
                                            <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                                                <input type="text" id="first_name" name="first_name" class="form-control" style="width: 100%; padding: 0.7rem 0.7rem 0.7rem 2.3rem; border: 2px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #f9fafb;" required>
                                                <i class="fas fa-user input-icon" style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; z-index: 2;"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group" style="margin-bottom: 0.8rem; position: relative; flex: 1;">
                                            <label for="last_name" class="form-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151; font-size: 0.85rem;">Soyad</label>
                                            <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                                                <input type="text" id="last_name" name="last_name" class="form-control" style="width: 100%; padding: 0.7rem 0.7rem 0.7rem 2.3rem; border: 2px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #f9fafb;" required>
                                                <i class="fas fa-user input-icon" style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; z-index: 2;"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 0.8rem; position: relative; flex: 1;">
                                        <label for="email" class="form-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151; font-size: 0.85rem;">E-posta Adresi</label>
                                        <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                                            <input type="email" id="email" name="email" class="form-control" style="width: 100%; padding: 0.7rem 0.7rem 0.7rem 2.3rem; border: 2px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #f9fafb;" required>
                                            <i class="fas fa-envelope input-icon" style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; z-index: 2;"></i>
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 0.8rem; position: relative; flex: 1;">
                                        <label for="phone" class="form-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151; font-size: 0.85rem;">Telefon Numarası (İsteğe bağlı)</label>
                                        <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                                            <input type="tel" id="phone" name="phone" class="form-control" style="width: 100%; padding: 0.7rem 0.7rem 0.7rem 2.3rem; border: 2px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #f9fafb;" placeholder="05XX XXX XX XX">
                                            <i class="fas fa-phone input-icon" style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; z-index: 2;"></i>
                                        </div>
                                    </div>

                                    <div class="form-row" style="display: flex; gap: 0.8rem; margin-bottom: 0;">
                                        <div class="form-group" style="margin-bottom: 0.8rem; position: relative; flex: 1;">
                                            <label for="password" class="form-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151; font-size: 0.85rem;">Şifre</label>
                                            <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                                                <input type="password" id="password" name="password" class="form-control" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 2.3rem; border: 2px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #f9fafb;" required minlength="6">
                                                <i class="fas fa-lock input-icon" style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; z-index: 2;"></i>
                                                <button type="button" class="password-toggle" id="togglePassword" style="position: absolute; right: 0.7rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 0.85rem; padding: 0.4rem; border-radius: 6px; z-index: 2;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="password-strength" id="passwordStrength"></div>
                                        </div>
                                        
                                        <div class="form-group" style="margin-bottom: 0.8rem; position: relative; flex: 1;">
                                            <label for="password_confirm" class="form-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151; font-size: 0.85rem;">Şifre Tekrar</label>
                                            <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                                                <input type="password" id="password_confirm" name="password_confirm" class="form-control" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 2.3rem; border: 2px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #f9fafb;" required>
                                                <i class="fas fa-lock input-icon" style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; z-index: 2;"></i>
                                                <button type="button" class="password-toggle" id="togglePasswordConfirm" style="position: absolute; right: 0.7rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 0.85rem; padding: 0.4rem; border-radius: 6px; z-index: 2;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <label class="custom-checkbox" style="display: flex; align-items: flex-start; cursor: pointer; user-select: none; margin: 1rem 0; font-size: 0.8rem; line-height: 1.3;">
                                        <input type="checkbox" id="terms" required style="opacity: 0; position: absolute;">
                                        <span class="checkbox-mark" style="width: 16px; height: 16px; border: 2px solid #9ca3af; border-radius: 4px; margin-right: 0.6rem; margin-top: 0.1rem; display: flex; align-items: center; justify-content: center; background: white; flex-shrink: 0;">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <span class="checkbox-text">
                                            <a href="#" target="_blank" style="color: #11998e; text-decoration: none; font-weight: 500;">Kullanım koşullarını</a> ve <a href="#" target="_blank" style="color: #11998e; text-decoration: none; font-weight: 500;">gizlilik politikasını</a> kabul ediyorum
                                        </span>
                                    </label>

                                    <button type="submit" class="register-btn" id="registerBtn" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; margin-bottom: 0.8rem;">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Hesap Oluştur
                                    </button>
                                </form>

                                <div class="divider" style="text-align: center; margin: 0.5rem 0; position: relative;">
                                    <span style="background: white; padding: 0 1rem; color: #9ca3af; font-size: 0.75rem;">veya</span>
                                </div>

                                <div class="login-link" style="text-align: center;">
                                    <a href="index.php" class="login-btn" style="color: #11998e; text-decoration: none; font-weight: 600; padding: 0.6rem 1.5rem; border: 2px solid #11998e; border-radius: 8px; display: inline-block; font-size: 0.85rem;">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Giriş Yap
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
    <script src="assets/js/theme.js?v=<?php echo time(); ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced validation for registration form
            const registerForm = document.getElementById('registerForm');
            const termsCheckbox = document.getElementById('terms');
            
            // Custom validation for names (Turkish characters)
            const nameInputs = document.querySelectorAll('#first_name, #last_name');
            nameInputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Allow only Turkish characters and spaces
                    this.value = this.value.replace(/[^a-zA-ZçğıöşüÇĞIİÖŞÜ\s]/g, '');
                });
            });
            
            // Form submission validation
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate terms acceptance
                if (!termsCheckbox.checked) {
                    KMPBSTheme.showToast('warning', 'Kullanım koşullarını kabul etmelisiniz');
                    return;
                }
                
                // All validations passed, submit form
                KMPBSTheme.setButtonLoading(document.getElementById('registerBtn'), true);
                
                setTimeout(() => {
                    this.submit();
                }, 500);
            });
            
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