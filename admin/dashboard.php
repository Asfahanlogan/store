<?php
require_once '../config.php';
requireAdmin();

$page_title = 'Dashboard';

// Get data
$tools = JsonDB::read('../data/tools.json');
$payments = JsonDB::read('../data/payments.json');
$messages = JsonDB::read('../data/contact_messages.json');

// Calculate statistics
$total_tools = count($tools);
$total_sales = count($payments);
$total_revenue = array_sum(array_column($payments, 'amount'));

// Count payments by status
$pending_payments = count(array_filter($payments, function($p) { return $p['status'] === 'pending'; }));
$confirmed_payments = count(array_filter($payments, function($p) { return $p['status'] === 'confirmed'; }));

// Count messages by status
$total_messages = count($messages);
$unread_messages = count(array_filter($messages, function($m) { return $m['status'] === 'unread'; }));

// Count payments by cryptocurrency
$crypto_stats = [];
foreach ($payments as $payment) {
    $currency = $payment['crypto_currency'];
    if (!isset($crypto_stats[$currency])) {
        $crypto_stats[$currency] = 0;
    }
    $crypto_stats[$currency]++;
}

// Get recent payments
usort($payments, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$recent_payments = array_slice($payments, 0, 5);

// Get tools by category
$category_stats = [];
foreach ($tools as $tool) {
    $category = $tool['category'];
    if (!isset($category_stats[$category])) {
        $category_stats[$category] = 0;
    }
    $category_stats[$category]++;
}
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-gradient mb-0">Dashboard Overview</h2>
        <p class="text-light-emphasis">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><?php echo $total_tools; ?></h3>
                    <p class="mb-0">Total Tools</p>
                </div>
                <i class="fas fa-tools fa-2x"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><?php echo $total_sales; ?></h3>
                    <p class="mb-0">Total Sales</p>
                </div>
                <i class="fas fa-shopping-cart fa-2x"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><?php echo formatPrice($total_revenue); ?></h3>
                    <p class="mb-0">Total Revenue</p>
                </div>
                <i class="fas fa-dollar-sign fa-2x"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card bg-dark border-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-1"><?php echo $confirmed_payments; ?></h3>
                        <p class="text-light-emphasis mb-0">Confirmed Payments</p>
                    </div>
                    <i class="fas fa-check-circle text-success fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card bg-dark border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning mb-1"><?php echo $unread_messages; ?></h3>
                        <p class="text-light-emphasis mb-0">Unread Messages</p>
                    </div>
                    <i class="fas fa-envelope text-warning fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Payments -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-history me-2"></i>Recent Payments
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_payments)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h5 class="text-light">No payments yet</h5>
                        <p class="text-light-emphasis">Payments will appear here once customers make purchases.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Tool</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_payments as $payment): 
                                    // Get tool name - handle both individual purchases and cart orders
                                    $tool_name = 'Unknown Tool';
                                    
                                    if (isset($payment['tool_id'])) {
                                        // Individual purchase - find tool by tool_id
                                        foreach ($tools as $tool) {
                                            if ($tool['id'] == $payment['tool_id']) {
                                                $tool_name = $tool['name'];
                                                break;
                                            }
                                        }
                                    } elseif (isset($payment['items']) && is_array($payment['items']) && !empty($payment['items'])) {
                                        // Cart order - get first item name, or show count if multiple
                                        if (count($payment['items']) == 1) {
                                            $tool_name = $payment['items'][0]['name'];
                                        } else {
                                            $tool_name = count($payment['items']) . ' items';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td>#<?php echo $payment['id']; ?></td>
                                    <td><?php echo htmlspecialchars(substr($payment['customer_email'], 0, 20)); ?></td>
                                    <td><?php echo htmlspecialchars($tool_name); ?></td>
                                    <td><?php echo formatPrice($payment['amount']); ?></td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo $payment['crypto_currency']; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($payment['status'] === 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="payments.php" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>View All Payments
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Statistics -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Payment Methods
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($crypto_stats)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-coins fa-2x text-muted mb-2"></i>
                        <p class="text-light-emphasis mb-0">No payments yet</p>
                    </div>
                <?php else: ?>
                    <div class="mt-3">
                        <?php foreach ($crypto_stats as $currency => $count): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fab fa-<?php echo strtolower($currency); ?> me-2 text-warning"></i>
                                    <?php echo $currency; ?>
                                </span>
                                <span class="badge bg-secondary"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pending Payments -->
        <div class="card">
            <div class="card-header">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-clock me-2"></i>Pending Payments
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-light">Payments awaiting confirmation</span>
                    <span class="h3 text-warning"><?php echo $pending_payments; ?></span>
                </div>
                <div class="progress mt-3">
                    <div class="progress-bar bg-warning" 
                         role="progressbar" 
                         style="width: <?php echo $total_sales > 0 ? ($pending_payments / $total_sales * 100) : 0; ?>%" 
                         aria-valuenow="<?php echo $pending_payments; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="<?php echo $total_sales; ?>">
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="payments.php?status=pending" class="btn btn-outline-warning">
                        <i class="fas fa-check-circle me-1"></i>Review Pending
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tools by Category -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-tags me-2"></i>Tools by Category
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($category_stats)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-tools fa-2x text-muted mb-2"></i>
                        <p class="text-light-emphasis mb-0">No tools available</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($category_stats as $category => $count): ?>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="text-center">
                                <div class="feature-icon mx-auto mb-2" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <h6 class="text-light mb-1"><?php echo htmlspecialchars($category); ?></h6>
                                <p class="text-light-emphasis mb-0"><?php echo $count; ?> tools</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="tools.php" class="btn btn-outline-primary">
                        <i class="fas fa-tools me-1"></i>Manage Tools
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>