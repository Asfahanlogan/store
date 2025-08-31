<?php
require_once 'config.php';
$page_title = 'Payment Status';

$order_id = $_GET['order_id'] ?? null;
$order = null;
$tool = null;

if ($order_id) {
    // Get payment record
    $payments = JsonDB::read('payments.json');
    foreach ($payments as $payment) {
        if ($payment['id'] == $order_id) {
            $order = $payment;
            break;
        }
    }
    
    if ($order) {
        // Get tool details
        $tools = JsonDB::read('tools.json');
        foreach ($tools as $t) {
            if ($t['id'] == $order['tool_id']) {
                $tool = $t;
                break;
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!$order): ?>
            <!-- Order Not Found -->
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Order Not Found
                    </h3>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-light">No order found with this ID</h4>
                    <p class="text-light-emphasis">Please check your order ID and try again.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-1"></i>Browse Products
                        </a>
                        <a href="contact.php" class="btn btn-outline-secondary">
                            <i class="fas fa-envelope me-1"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <!-- Order Status -->
            <div class="card <?php echo $order['status'] === 'confirmed' ? 'border-success' : 'border-warning'; ?>">
                <div class="card-header <?php echo $order['status'] === 'confirmed' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <h3 class="mb-0">
                        <i class="fas fa-<?php echo $order['status'] === 'confirmed' ? 'check-circle' : 'clock'; ?> me-2"></i>
                        Order Status: <?php echo ucfirst($order['status']); ?>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Order Details</h5>
                            <table class="table table-dark table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>#<?php echo $order['id']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Product:</strong></td>
                                    <td><?php echo htmlspecialchars($tool['name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td><?php echo formatPrice($order['amount']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Payment Details</h5>
                            <table class="table table-dark table-borderless">
                                <tr>
                                    <td><strong>Currency:</strong></td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo $order['crypto_currency']; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Wallet Address:</strong></td>
                                    <td>
                                        <code class="text-info small"><?php echo substr($order['wallet_address'], 0, 20) . '...'; ?></code>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>TX Hash:</strong></td>
                                    <td>
                                        <?php if (!empty($order['tx_hash'])): ?>
                                            <code class="text-success small"><?php echo substr($order['tx_hash'], 0, 20) . '...'; ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">Pending...</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php if ($order['status'] === 'confirmed'): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Confirmed
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if ($order['status'] === 'confirmed'): ?>
                    <!-- Payment Confirmed -->
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle me-2"></i>Payment Confirmed!</h5>
                        <p class="mb-0">Your payment has been confirmed and processed successfully.</p>
                    </div>
                    
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-download fa-3x mb-3"></i>
                            <h4>Download Your Tool</h4>
                            <p class="mb-3">Your tool is ready for download. Click the button below to get your files.</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <button class="btn btn-light btn-lg" onclick="downloadTool()">
                                    <i class="fas fa-download me-2"></i>Download <?php echo htmlspecialchars($tool['name']); ?>
                                </button>
                                <button class="btn btn-outline-light" onclick="downloadDocumentation()">
                                    <i class="fas fa-book me-2"></i>Download Documentation
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php else: ?>
                    <!-- Payment Pending -->
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-clock me-2"></i>Payment Pending</h5>
                        <p class="mb-0">We're waiting for your payment to be confirmed on the blockchain.</p>
                    </div>
                    
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5><i class="fas fa-info-circle me-2"></i>What to do next?</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>If you haven't sent payment yet:</h6>
                                    <ul>
                                        <li>Send exactly <?php echo formatPrice($order['amount']); ?> worth of <?php echo $order['crypto_currency']; ?></li>
                                        <li>To address: <code><?php echo $order['wallet_address']; ?></code></li>
                                        <li>Include sufficient network fees</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>If you already sent payment:</h6>
                                    <ul>
                                        <li>Wait for blockchain confirmation</li>
                                        <li>This usually takes 10-60 minutes</li>
                                        <li>Check back in a few minutes</li>
                                        <li>Contact support if delayed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Product Information -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Product Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="<?php echo htmlspecialchars($tool['image']); ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?php echo htmlspecialchars($tool['name']); ?>">
                                </div>
                                <div class="col-md-8">
                                    <h4 class="text-light"><?php echo htmlspecialchars($tool['name']); ?></h4>
                                    <p class="text-light-emphasis"><?php echo htmlspecialchars($tool['description']); ?></p>
                                    <p><span class="badge bg-primary"><?php echo htmlspecialchars($tool['category']); ?></span></p>
                                    
                                    <div class="border rounded p-3 mt-3">
                                        <h6 class="text-primary mb-2">What's Included:</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-download text-success me-2"></i>Main tool package</li>
                                            <li><i class="fas fa-book text-info me-2"></i>User documentation</li>
                                            <li><i class="fas fa-code text-warning me-2"></i>Example scripts</li>
                                            <li><i class="fas fa-headset text-primary me-2"></i>Technical support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                        <a href="products.php" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-cart me-1"></i>Buy More Tools
                        </a>
                        <div>
                            <button class="btn btn-primary me-2" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh Status
                            </button>
                            <a href="contact.php" class="btn btn-outline-secondary">
                                <i class="fas fa-envelope me-1"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function downloadTool() {
    // Simulate download
    alert('Download started! Your tool package is being prepared.');
    // In a real implementation, this would generate a secure download link
}

function downloadDocumentation() {
    // Simulate documentation download
    alert('Documentation download started!');
}

// Auto-refresh for pending payments
<?php if ($order && $order['status'] === 'pending'): ?>
setTimeout(function() {
    location.reload();
}, 60000); // Refresh every minute for pending payments
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>