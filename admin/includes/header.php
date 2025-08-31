<?php
if (!isAdmin()) {
    redirect('login.php');
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin - ' . SITE_NAME : 'Admin - ' . SITE_NAME; ?></title>
    
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
            --gradient-secondary: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(15, 23, 42, 0.95) !important;
            border-bottom: 1px solid rgba(139, 92, 246, 0.2);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
        }
        
        .nav-link:hover {
            color: var(--bs-primary) !important;
            background: rgba(99, 102, 241, 0.1);
        }
        
        .nav-link.active {
            color: var(--bs-primary) !important;
            background: rgba(99, 102, 241, 0.2);
        }
        
        .sidebar {
            background: rgba(30, 41, 59, 0.9);
            border-right: 1px solid rgba(139, 92, 246, 0.2);
            backdrop-filter: blur(10px);
            min-height: calc(100vh - 76px);
        }
        
        .sidebar .nav-link {
            color: #e2e8f0;
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(99, 102, 241, 0.1);
            color: var(--bs-primary);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--gradient-primary);
            color: white;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(139, 92, 246, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.1);
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }
        
        .btn-success {
            background: var(--gradient-secondary);
            border: none;
            font-weight: 600;
        }
        
        .stats-card {
            background: var(--gradient-primary);
            border-radius: 15px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.3);
        }
        
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .table-dark {
            background-color: rgba(30, 41, 59, 0.8);
        }
        
        .admin-badge {
            background: var(--gradient-primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-halved me-2"></i>Admin Panel
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                           href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tools.php' ? 'active' : ''; ?>" 
                           href="tools.php">
                            <i class="fas fa-box me-1"></i>Manage Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" 
                           href="payments.php">
                            <i class="fas fa-credit-card me-1"></i>Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" 
                           href="messages.php">
                            <i class="fas fa-envelope me-1"></i>Messages
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="admin-badge">
                                <i class="fas fa-user-shield me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li>
                                <a class="dropdown-item" href="../index.php" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>View Website
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-cog me-2"></i>Administration
                        </h6>
                        <nav class="nav flex-column">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                               href="dashboard.php">
                                <i class="fas fa-chart-bar me-2"></i>Dashboard
                            </a>
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tools.php' ? 'active' : ''; ?>" 
                               href="tools.php">
                                <i class="fas fa-box me-2"></i>Manage Products
                            </a>
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" 
                               href="payments.php">
                                <i class="fas fa-money-bill-wave me-2"></i>Payment Records
                            </a>
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" 
                               href="messages.php">
                                <i class="fas fa-envelope me-2"></i>Contact Messages
                            </a>
                        </nav>
                        
                        <hr class="border-secondary">
                        
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-link me-2"></i>Quick Links
                        </h6>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="../index.php" target="_blank">
                                <i class="fas fa-home me-2"></i>Website Home
                            </a>
                            <a class="nav-link" href="../products.php" target="_blank">
                                <i class="fas fa-shopping-cart me-2"></i>Products Page
                            </a>
                            <a class="nav-link" href="../contact.php" target="_blank">
                                <i class="fas fa-envelope me-2"></i>Contact Page
                            </a>
                            <a class="nav-link" href="messages.php">
                                <i class="fas fa-inbox me-2"></i>Contact Messages
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
