<?php
require_once 'config.php';
$page_title = 'Checkout';

// Get product ID from URL
$product_id = $_GET['tool_id'] ?? null;
if (!$product_id) {
    redirect('products.php');
}

// Get product details
$products = JsonDB::read('tools.json');
$product = null;
foreach ($products as $p) {
    if ($p['id'] == $product_id) {
        $product = $p;
        break;
    }
}

if (!$product) {
    redirect('products.php');
}

// Handle form submission
$order_created = false;
$order_details = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $crypto_currency = $_POST['crypto_currency'] ?? '';
    
    if ($customer_email && in_array($crypto_currency, ['BTC', 'ETH', 'LTC'])) {
        // Get crypto addresses
        $crypto_addresses = getCryptoAddresses();
        
        // Create new payment record
        $payments = JsonDB::read('payments.json');
        $payment_id = JsonDB::getNextId('payments.json');
        
        $new_payment = [
            'id' => $payment_id,
            'tool_id' => $product['id'],
            'customer_email' => $customer_email,
            'crypto_currency' => $crypto_currency,
            'wallet_address' => $crypto_addresses[$crypto_currency],
            'amount' => $product['price'],
            'tx_hash' => '',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $payments[] = $new_payment;
        JsonDB::write('payments.json', $payments);
        
        $order_created = true;
        $order_details = $new_payment;
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <?php if (!$order_created): ?>
    <!-- Checkout Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-gradient mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Checkout
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Product Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="img-fluid rounded" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="col-md-8">
                            <h4 class="text-light"><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p class="text-light-emphasis"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><span class="badge bg-primary"><?php echo htmlspecialchars($product['category']); ?></span></p>
                            <h3 class="text-success"><?php echo formatPrice($product['price']); ?></h3>
                        </div>
                    </div>
                    
                    <hr class="border-secondary">
                    
                    <!-- Payment Form -->
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Customer Information
                            </h5>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       placeholder="your@email.com">
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                                <div class="form-text">
                                    You'll receive access instructions at this email after payment confirmation.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fab fa-bitcoin me-2"></i>Payment Method
                            </h5>
                            <p class="text-light-emphasis mb-3">
                                We accept cryptocurrency payments for secure and anonymous transactions.
                            </p>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="crypto-option">
                                        <input type="radio" class="btn-check" name="crypto_currency" 
                                               id="btc" value="BTC" required>
                                        <label class="btn btn-outline-warning w-100 py-3" for="btc">
                                            <i class="fab fa-bitcoin fa-2x d-block mb-2"></i>
                                            <strong>Bitcoin</strong><br>
                                            <small>BTC</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="crypto-option">
                                        <input type="radio" class="btn-check" name="crypto_currency" 
                                               id="eth" value="ETH" required>
                                        <label class="btn btn-outline-info w-100 py-3" for="eth">
                                            <i class="fab fa-ethereum fa-2x d-block mb-2"></i>
                                            <strong>Ethereum</strong><br>
                                            <small>ETH</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="crypto-option">
                                        <input type="radio" class="btn-check" name="crypto_currency" 
                                               id="ltc" value="LTC" required>
                                        <label class="btn btn-outline-secondary w-100 py-3" for="ltc">
                                            <i class="fas fa-coins fa-2x d-block mb-2"></i>
                                            <strong>Litecoin</strong><br>
                                            <small>LTC</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback d-block" style="display: none;" id="crypto-error">
                                Please select a cryptocurrency payment method.
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Payment Process:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Select your preferred cryptocurrency</li>
                                <li>Copy the wallet address provided</li>
                                <li>Send the exact amount to the address</li>
                                <li>Submit the transaction hash for verification</li>
                                <li>Download link will be sent after confirmation</li>
                            </ol>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Products
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Payment Instructions -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Payment Instructions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Order Created Successfully!</h5>
                        <p class="mb-0">Your order has been created. Please follow the payment instructions below.</p>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">Order Details</h6>
                            <p><strong>Order ID:</strong> #<?php echo $order_details['id']; ?></p>
                            <p><strong>Product:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
                            <p><strong>Amount:</strong> <?php echo formatPrice($order_details['amount']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['customer_email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Payment Information</h6>
                            <p><strong>Currency:</strong> 
                                <span class="badge bg-warning"><?php echo $order_details['crypto_currency']; ?></span>
                            </p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-warning">Pending Payment</span>
                            </p>
                            <p><strong>Created:</strong> <?php echo $order_details['created_at']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Payment Instructions -->
                    <div class="card bg-dark border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-wallet me-2"></i>Send Payment to This Address
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-secondary rounded">
                                <code class="text-warning fs-6" id="walletAddress">
                                    <?php echo $order_details['wallet_address']; ?>
                                </code>
                                <button class="btn btn-outline-light btn-sm" 
                                        onclick="copyToClipboard('<?php echo $order_details['wallet_address']; ?>', this)">
                                    <i class="fas fa-copy me-1"></i>Copy
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <p class="text-warning mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Important:</strong>
                                </p>
                                <ul class="text-light-emphasis small">
                                    <li>Send exactly <strong><?php echo formatPrice($order_details['amount']); ?></strong> worth of <?php echo $order_details['crypto_currency']; ?></li>
                                    <li>Double-check the wallet address before sending</li>
                                    <li>Include sufficient network fees for faster confirmation</li>
                                    <li>Save your transaction hash for tracking</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Code Placeholder -->
                    <div class="text-center my-4">
                        <div class="bg-light p-4 rounded d-inline-block">
                            <i class="fas fa-qrcode fa-5x text-dark"></i>
                            <p class="text-dark mt-2 mb-0">QR Code</p>
                            <small class="text-muted">Scan to pay with mobile wallet</small>
                        </div>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-list-ol me-2"></i>What happens next?</h6>
                        <ol class="mb-0">
                            <li>Send the payment to the wallet address above</li>
                            <li>Wait for network confirmation (usually 10-60 minutes)</li>
                            <li>Our system will automatically detect your payment</li>
                            <li>You'll receive access instructions via email</li>
                            <li>Access your purchased product immediately</li>
                        </ol>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="products.php" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-cart me-1"></i>Buy More Products
                        </a>
                        <a href="payment-status.php?order_id=<?php echo $order_details['id']; ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-check me-1"></i>I Made the Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                // Check if crypto currency is selected
                var cryptoSelected = document.querySelector('input[name="crypto_currency"]:checked');
                var cryptoError = document.getElementById('crypto-error');
                
                if (!cryptoSelected) {
                    cryptoError.style.display = 'block';
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    cryptoError.style.display = 'none';
                }
                
                if (form.checkValidity() === false || !cryptoSelected) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Hide crypto error when option is selected
document.querySelectorAll('input[name="crypto_currency"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('crypto-error').style.display = 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>