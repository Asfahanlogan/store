<?php
require_once 'config.php';
$page_title = 'Shopping Cart';

// Initialize cart in session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' && isset($_POST['tool_id'])) {
            $tool_id = intval($_POST['tool_id']);
            $quantity = intval($_POST['quantity'] ?? 1);
            
            // Get product details
            $products = JsonDB::read('tools.json');
            $product = null;
            foreach ($products as $p) {
                if ($p['id'] == $tool_id) {
                    $product = $p;
                    break;
                }
            }
            
            if ($product) {
                // Check if product already in cart
                $found = false;
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $tool_id) {
                        $item['quantity'] += $quantity;
                        $item['subtotal'] = $item['quantity'] * $item['price'];
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'category' => $product['category'],
                        'type' => $product['type'],
                        'quantity' => $quantity,
                        'subtotal' => $product['price'] * $quantity
                    ];
                }
                
                $message = 'Product added to cart!';
                $message_type = 'success';
            }
        } elseif ($action === 'update' && isset($_POST['tool_id'])) {
            $tool_id = intval($_POST['tool_id']);
            $quantity = intval($_POST['quantity']);
            
            if ($quantity > 0) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $tool_id) {
                        $item['quantity'] = $quantity;
                        $item['subtotal'] = $item['quantity'] * $item['price'];
                        break;
                    }
                }
            } else {
                // Remove item if quantity is 0
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($tool_id) {
                    return $item['id'] != $tool_id;
                });
            }
            
            $message = 'Cart updated!';
            $message_type = 'success';
        } elseif ($action === 'remove' && isset($_POST['tool_id'])) {
            $tool_id = intval($_POST['tool_id']);
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($tool_id) {
                return $item['id'] != $tool_id;
            });
            
            $message = 'Product removed from cart!';
            $message_type = 'success';
        } elseif ($action === 'clear') {
            $_SESSION['cart'] = [];
            $message = 'Cart cleared!';
            $message_type = 'success';
        }
    }
}

// Calculate cart totals
$cart_total = array_sum(array_column($_SESSION['cart'], 'subtotal'));
$cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 fw-bold text-gradient mb-4">
                <i class="fas fa-shopping-cart me-3"></i>Shopping Cart
            </h1>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <!-- Empty Cart -->
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h3 class="text-light mb-3">Your cart is empty</h3>
                        <p class="text-light-emphasis mb-4">Add some products to get started!</p>
                        <a href="products.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-tools me-2"></i>Browse Products
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Cart Items -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-box me-2"></i>Cart Items (<?php echo $cart_count; ?>)
                                </h5>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to clear your cart?')">
                                        <i class="fas fa-trash me-1"></i>Clear Cart
                                    </button>
                                </form>
                            </div>
                            <div class="card-body">
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <div class="row align-items-center mb-4 pb-4 border-bottom border-secondary">
                                        <div class="col-md-2">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                 class="img-fluid rounded" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-light mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <p class="text-light-emphasis small mb-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($item['category']); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="text-light"><?php echo formatPrice($item['price']); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <form method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="tool_id" value="<?php echo $item['id']; ?>">
                                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="10" class="form-control form-control-sm me-2" style="width: 60px;">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="text-success fw-bold"><?php echo formatPrice($item['subtotal']); ?></span>
                                        </div>
                                        <div class="col-md-1">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="tool_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cart Summary -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 100px;">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>Order Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Items (<?php echo $cart_count; ?>):</span>
                                    <span><?php echo formatPrice($cart_total); ?></span>
                                </div>
                                <hr class="border-secondary">
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-success h5 mb-0"><?php echo formatPrice($cart_total); ?></strong>
                                </div>
                                
                                <a href="checkout-cart.php" class="btn btn-primary w-100 btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                </a>
                                
                                <div class="text-center mt-3">
                                    <a href="products.php" class="btn btn-outline-light">
                                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>