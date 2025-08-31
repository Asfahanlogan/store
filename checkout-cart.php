<?php
require_once 'config.php';
$page_title = 'Cart Checkout';

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
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
        
        // Calculate total
        $cart_total = array_sum(array_column($_SESSION['cart'], 'subtotal'));
        
        // Create new payment record
        $payments = JsonDB::read('payments.json');
        $payment_id = JsonDB::getNextId('payments.json');
        
        $new_payment = [
            'id' => $payment_id,
            'items' => $_SESSION['cart'],
            'customer_email' => $customer_email,
            'crypto_currency' => $crypto_currency,
            'wallet_address' => $crypto_addresses[$crypto_currency],
            'amount' => $cart_total,
            'tx_hash' => '',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 'cart_order'
        ];
        
        $payments[] = $new_payment;
        JsonDB::write('payments.json', $payments);
        
        $order_created = true;
        $order_details = $new_payment;
        
        // Clear cart after successful order
        $_SESSION['cart'] = [];
    }
}

// Calculate cart totals
$cart_total = array_sum(array_column($_SESSION['cart'], 'subtotal'));
$cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
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
                        <i class="fas fa-shopping-cart me-2"></i>Cart Checkout
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Cart Summary -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-box me-2"></i>Order Summary
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-dark table-borderless">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     class="rounded me-3" style="width: 50px; height: 35px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <small class="text-light-emphasis"><?php echo htmlspecialchars($item['category']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo formatPrice($item['price']); ?></td>
                                        <td class="text-success"><?php echo formatPrice($item['subtotal']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="border-top border-secondary">
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td class="text-success h5 mb-0"><?php echo formatPrice($cart_total); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
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
                                       placeholder="Enter your email address">
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-coins me-2"></i>Payment Method
                            </h5>
                            <div class="mb-3">
                                <label class="form-label">Select Cryptocurrency *</label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="crypto_currency" 
                                               id="btc" value="BTC" required>
                                        <label class="btn btn-outline-warning w-100 py-3" for="btc">
                                            <i class="fab fa-bitcoin fa-2x d-block mb-2"></i>
                                            <strong>Bitcoin (BTC)</strong>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="crypto_currency" 
                                               id="eth" value="ETH" required>
                                        <label class="btn btn-outline-info w-100 py-3" for="eth">
                                            <i class="fab fa-ethereum fa-2x d-block mb-2"></i>
                                            <strong>Ethereum (ETH)</strong>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="crypto_currency" 
                                               id="ltc" value="LTC" required>
                                        <label class="btn btn-outline-secondary w-100 py-3" for="ltc">
                                            <i class="fas fa-coins fa-2x d-block mb-2"></i>
                                            <strong>Litecoin (LTC)</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="invalid-feedback d-block" style="display: none;" id="crypto-error">
                                    Please select a cryptocurrency payment method.
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i>Complete Order
                            </button>
                            <a href="cart.php" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Order Confirmation -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Order Created Successfully!
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-shopping-bag fa-4x text-success mb-3"></i>
                        <h5><i class="fas fa-shopping-cart me-2"></i>Order Created Successfully!</h5>
                        <p class="text-light-emphasis">Your order has been created and is awaiting payment confirmation.</p>
                    </div>
                    
                    <!-- Order Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">Order Information:</h6>
                            <table class="table table-dark table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>#<?php echo $order_details['id']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="text-success"><?php echo formatPrice($order_details['amount']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Items:</strong></td>
                                    <td><?php echo count($order_details['items']); ?> products</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($order_details['customer_email']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order_details['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">Payment Details:</h6>
                            <div class="card bg-dark border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-<?php echo strtolower($order_details['crypto_currency']); ?> me-2"></i>
                                        <?php echo $order_details['crypto_currency']; ?> Payment Required
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-warning mb-2">
                                        <strong>Amount:</strong> <?php echo formatPrice($order_details['amount']); ?>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Wallet Address:</strong>
                                    </p>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" 
                                               value="<?php echo htmlspecialchars($order_details['wallet_address']); ?>" 
                                               readonly id="walletAddress">
                                        <button class="btn btn-outline-light" type="button" 
                                                onclick="copyToClipboard(document.getElementById('walletAddress').value, this)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <p class="text-warning mb-0">
                                        <small>
                                            <i class="fas fa-info-circle me-1"></i>
                                            Send exactly <?php echo formatPrice($order_details['amount']); ?> to this address
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="alert alert-info">
                        <h6 class="text-primary mb-2">What's Next?</h6>
                        <ol class="mb-0">
                            <li>Send <?php echo formatPrice($order_details['amount']); ?> <?php echo $order_details['crypto_currency']; ?> to the wallet address above</li>
                            <li>Include sufficient network fees for faster confirmation</li>
                            <li>Wait for payment confirmation (usually 1-3 confirmations)</li>
                            <li>You'll receive access to your purchased products via email</li>
                        </ol>
                    </div>
                    
                    <div class="text-center">
                        <a href="payment-status.php?order_id=<?php echo $order_details['id']; ?>" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Check Payment Status
                        </a>
                        <a href="products.php" class="btn btn-outline-light">
                            <i class="fas fa-shopping-cart me-1"></i>Buy More Products
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
        var cryptoError = document.getElementById('crypto-error');
        
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                var cryptoSelected = document.querySelector('input[name="crypto_currency"]:checked');
                
                if (form.checkValidity() === false || !cryptoSelected) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    if (!cryptoSelected) {
                        cryptoError.style.display = 'block';
                    } else {
                        cryptoError.style.display = 'none';
                    }
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Hide crypto error when option is selected
document.querySelectorAll('input[name="crypto_currency"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('crypto-error').style.display = 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>