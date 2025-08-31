<?php
require_once '../config.php';

// If already logged in, redirect to dashboard
if (isAdmin()) {
    redirect('dashboard.php');
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $admin = JsonDB::read('admin.json');
        
        // Use proper password hash verification
        if (isset($admin['username']) && isset($admin['password'])) {
            if ($username === $admin['username'] && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['login_time'] = time();
                redirect('dashboard.php');
            } else {
                $error_message = 'Invalid username or password.';
            }
        } else {
            $error_message = 'Admin configuration error.';
        }
    } else {
        $error_message = 'Please fill in all fields.';
    }
}

$page_title = 'Admin Login';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary: #6366f1;
            --bs-secondary: #8b5cf6;
            --bs-success: #10b981;
            --bs-info: #06b6d4;
            --bs-warning: #f59e0b;
            --bs-danger: #ef4444;
            --bs-dark: #0f172a;
            --bs-body-bg: #0f172a;
            --bs-body-color: #e2e8f0;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(139, 92, 246, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        
        .login-header {
            background: var(--gradient-primary);
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }
        
        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: #6366f1;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
            color: #e2e8f0;
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-shield-halved fa-3x text-white mb-3"></i>
                        <h2 class="text-white mb-0">Admin Portal</h2>
                        <p class="text-white-50 mb-0">Store</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                       required autocomplete="username">
                                <div class="invalid-feedback">
                                    Please provide a username.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a password.
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <a href="../index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Website
                            </a>
                        </div>
                        
                        <hr class="border-secondary">
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Demo Credentials:</strong><br>
                                Username: admin<br>
                                Password: SecureAdmin123!
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>
</html>