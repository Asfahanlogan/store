<?php
require_once '../config.php';
requireAdmin();

$page_title = 'Payment Records';

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'update_status') {
            // Update payment status
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $tx_hash = trim($_POST['tx_hash'] ?? '');
            
            if ($id > 0 && in_array($status, ['pending', 'confirmed'])) {
                $payments = JsonDB::read('../data/payments.json');
                $payment_found = false;
                
                foreach ($payments as &$payment) {
                    if ($payment['id'] == $id) {
                        $payment['status'] = $status;
                        if (!empty($tx_hash)) {
                            $payment['tx_hash'] = $tx_hash;
                        }
                        $payment_found = true;
                        break;
                    }
                }
                
                if ($payment_found) {
                    JsonDB::write('../data/payments.json', $payments);
                    $message = 'Payment status updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Payment record not found.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Invalid status value.';
                $message_type = 'danger';
            }
        } elseif ($action === 'delete') {
            // Delete payment record
            $id = intval($_POST['id'] ?? 0);
            
            if ($id > 0) {
                $payments = JsonDB::read('../data/payments.json');
                $new_payments = array_filter($payments, function($payment) use ($id) {
                    return $payment['id'] != $id;
                });
                
                if (count($new_payments) < count($payments)) {
                    JsonDB::write('../data/payments.json', array_values($new_payments));
                    $message = 'Payment record deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Payment record not found.';
                    $message_type = 'danger';
                }
            }
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search_filter = $_GET['search'] ?? '';

// Get payments data
$payments = JsonDB::read('../data/payments.json');

// Get tools data for reference
$tools = JsonDB::read('../data/tools.json');

// Filter payments
$filtered_payments = $payments;

if (!empty($status_filter)) {
    $filtered_payments = array_filter($filtered_payments, function($payment) use ($status_filter) {
        return $payment['status'] === $status_filter;
    });
}

if (!empty($search_filter)) {
    $filtered_payments = array_filter($filtered_payments, function($payment) use ($search_filter, $tools) {
        // Search in email, tool name, and tx_hash - handle both individual purchases and cart orders
        $tool_name = '';
        
        if (isset($payment['tool_id'])) {
            // Individual purchase - find tool by tool_id
            foreach ($tools as $tool) {
                if ($tool['id'] == $payment['tool_id']) {
                    $tool_name = $tool['name'];
                    break;
                }
            }
        } elseif (isset($payment['items']) && is_array($payment['items']) && !empty($payment['items'])) {
            // Cart order - search in all item names
            $tool_names = array_column($payment['items'], 'name');
            $tool_name = implode(' ', $tool_names);
        }
        
        return stripos($payment['customer_email'], $search_filter) !== false ||
               stripos($tool_name, $search_filter) !== false ||
               stripos($payment['tx_hash'], $search_filter) !== false;
    });
}

// Sort by date (newest first)
usort($filtered_payments, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$total_payments = count($filtered_payments);
$total_pages = ceil($total_payments / $per_page);
$offset = ($page - 1) * $per_page;
$paginated_payments = array_slice($filtered_payments, $offset, $per_page);

// Calculate statistics
$total_sales = count($payments);
$pending_payments = count(array_filter($payments, function($p) { return $p['status'] === 'pending'; }));
$confirmed_payments = count(array_filter($payments, function($p) { return $p['status'] === 'confirmed'; }));
$total_revenue = array_sum(array_column($payments, 'amount'));
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-gradient mb-0">Payment Records</h2>
        <p class="text-light-emphasis">Manage cryptocurrency payment transactions</p>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-dark border-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-light mb-1"><?php echo $total_sales; ?></h3>
                        <p class="text-light-emphasis mb-0">Total Payments</p>
                    </div>
                    <i class="fas fa-credit-card text-primary fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning mb-1"><?php echo $pending_payments; ?></h3>
                        <p class="text-light-emphasis mb-0">Pending</p>
                    </div>
                    <i class="fas fa-clock text-warning fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-1"><?php echo $confirmed_payments; ?></h3>
                        <p class="text-light-emphasis mb-0">Confirmed</p>
                    </div>
                    <i class="fas fa-check-circle text-success fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-info mb-1"><?php echo formatPrice($total_revenue); ?></h3>
                        <p class="text-light-emphasis mb-0">Total Revenue</p>
                    </div>
                    <i class="fas fa-dollar-sign text-info fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Search Payments</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           placeholder="Search by email, tool, or TX hash..."
                           value="<?php echo htmlspecialchars($search_filter); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <?php if (!empty($status_filter) || !empty($search_filter)): ?>
                    <a href="payments.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-header">
        <h5 class="text-primary mb-0">
            <i class="fas fa-list me-2"></i>Payment Transactions
            <?php if (!empty($status_filter) || !empty($search_filter)): ?>
                <small class="text-light-emphasis">
                    (<?php echo count($filtered_payments); ?> results)
                </small>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($paginated_payments)): ?>
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                <h5 class="text-light">No payment records found</h5>
                <p class="text-light-emphasis">
                    <?php if (!empty($status_filter) || !empty($search_filter)): ?>
                        Try adjusting your filters or search criteria.
                    <?php else: ?>
                        Payment records will appear here once customers make purchases.
                    <?php endif; ?>
                </p>
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
                            <th>TX Hash</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginated_payments as $payment): 
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
                            <td>
                                <small><?php echo htmlspecialchars($payment['customer_email']); ?></small>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($tool_name); ?></small>
                            </td>
                            <td class="text-success"><?php echo formatPrice($payment['amount']); ?></td>
                            <td>
                                <span class="badge bg-warning"><?php echo $payment['crypto_currency']; ?></span>
                            </td>
                            <td>
                                <?php if (!empty($payment['tx_hash'])): ?>
                                    <small class="text-info" title="<?php echo htmlspecialchars($payment['tx_hash']); ?>">
                                        <?php echo substr($payment['tx_hash'], 0, 10) . '...'; ?>
                                    </small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($payment['status'] === 'confirmed'): ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm"
                                            onclick="updatePayment(<?php echo htmlspecialchars(json_encode($payment)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="deletePayment(<?php echo $payment['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Payment records pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_filter); ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_filter); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_filter); ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Update Payment Modal -->
<div class="modal fade" id="updatePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" id="updateId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Update Payment Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="updateStatus" class="form-label">Status</label>
                        <select class="form-select" id="updateStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="updateTxHash" class="form-label">Transaction Hash</label>
                        <input type="text" class="form-control" id="updateTxHash" name="tx_hash" 
                               placeholder="Enter transaction hash (optional)">
                        <div class="form-text">Optional: Add the blockchain transaction hash for verification</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update payment function
function updatePayment(payment) {
    document.getElementById('updateId').value = payment.id;
    document.getElementById('updateStatus').value = payment.status;
    document.getElementById('updateTxHash').value = payment.tx_hash || '';
    
    new bootstrap.Modal(document.getElementById('updatePaymentModal')).show();
}

// Delete payment function
function deletePayment(id) {
    if (confirm('Are you sure you want to delete this payment record?\n\nThis action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>