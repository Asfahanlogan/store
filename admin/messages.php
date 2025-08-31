<?php
require_once '../config.php';
requireAdmin();

$page_title = 'Contact Messages';

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'update_status') {
            // Update message status
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            
            if ($id > 0 && in_array($status, ['unread', 'read', 'replied'])) {
                $messages = JsonDB::read('../data/contact_messages.json');
                $message_found = false;
                
                foreach ($messages as &$msg) {
                    if ($msg['id'] == $id) {
                        $msg['status'] = $status;
                        $message_found = true;
                        break;
                    }
                }
                
                if ($message_found) {
                    JsonDB::write('../data/contact_messages.json', $messages);
                    $message = 'Message status updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Message not found.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Invalid status value.';
                $message_type = 'danger';
            }
        } elseif ($action === 'delete') {
            // Delete message
            $id = intval($_POST['id'] ?? 0);
            
            if ($id > 0) {
                $messages = JsonDB::read('../data/contact_messages.json');
                $new_messages = array_filter($messages, function($msg) use ($id) {
                    return $msg['id'] != $id;
                });
                
                if (count($new_messages) < count($messages)) {
                    JsonDB::write('../data/contact_messages.json', array_values($new_messages));
                    $message = 'Message deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Message not found.';
                    $message_type = 'danger';
                }
            }
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search_filter = $_GET['search'] ?? '';

// Get messages data
$messages = JsonDB::read('../data/contact_messages.json');

// Filter messages
$filtered_messages = $messages;

if (!empty($status_filter)) {
    $filtered_messages = array_filter($filtered_messages, function($msg) use ($status_filter) {
        return $msg['status'] === $status_filter;
    });
}

if (!empty($search_filter)) {
    $filtered_messages = array_filter($filtered_messages, function($msg) use ($search_filter) {
        // Search in name, email, subject, and message
        return stripos($msg['name'], $search_filter) !== false ||
               stripos($msg['email'], $search_filter) !== false ||
               stripos($msg['subject'], $search_filter) !== false ||
               stripos($msg['message'], $search_filter) !== false;
    });
}

// Sort by date (newest first)
usort($filtered_messages, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$total_messages = count($filtered_messages);
$total_pages = ceil($total_messages / $per_page);
$offset = ($page - 1) * $per_page;
$paginated_messages = array_slice($filtered_messages, $offset, $per_page);

// Calculate statistics
$total_messages_count = count($messages);
$unread_messages = count(array_filter($messages, function($msg) { return $msg['status'] === 'unread'; }));
$read_messages = count(array_filter($messages, function($msg) { return $msg['status'] === 'read'; }));
$replied_messages = count(array_filter($messages, function($msg) { return $msg['status'] === 'replied'; }));
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-gradient mb-0">Contact Messages</h2>
        <p class="text-light-emphasis">Manage customer inquiries and support requests</p>
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
                        <h3 class="text-light mb-1"><?php echo $total_messages_count; ?></h3>
                        <p class="text-light-emphasis mb-0">Total Messages</p>
                    </div>
                    <i class="fas fa-envelope text-primary fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning mb-1"><?php echo $unread_messages; ?></h3>
                        <p class="text-light-emphasis mb-0">Unread</p>
                    </div>
                    <i class="fas fa-envelope-open-text text-warning fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-info mb-1"><?php echo $read_messages; ?></h3>
                        <p class="text-light-emphasis mb-0">Read</p>
                    </div>
                    <i class="fas fa-book-open text-info fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-1"><?php echo $replied_messages; ?></h3>
                        <p class="text-light-emphasis mb-0">Replied</p>
                    </div>
                    <i class="fas fa-reply-all text-success fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search_filter); ?>" 
                       placeholder="Search by name, email, subject, or message...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <?php if (!empty($status_filter) || !empty($search_filter)): ?>
                    <a href="messages.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Messages Table -->
<div class="card">
    <div class="card-header">
        <h5 class="text-primary mb-0">
            <i class="fas fa-inbox me-2"></i>Contact Messages
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($paginated_messages)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-light">No messages found</h5>
                <p class="text-light-emphasis">Messages will appear here when customers contact you.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginated_messages as $msg): ?>
                        <tr>
                            <td>#<?php echo $msg['id']; ?></td>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                            <td>
                                <?php if ($msg['status'] === 'unread'): ?>
                                    <span class="badge bg-warning">Unread</span>
                                <?php elseif ($msg['status'] === 'read'): ?>
                                    <span class="badge bg-info">Read</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Replied</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="#" class="btn btn-outline-primary view-message" 
                                       data-id="<?php echo $msg['id']; ?>"
                                       data-name="<?php echo htmlspecialchars($msg['name']); ?>"
                                       data-email="<?php echo htmlspecialchars($msg['email']); ?>"
                                       data-subject="<?php echo htmlspecialchars($msg['subject']); ?>"
                                       data-message="<?php echo htmlspecialchars($msg['message']); ?>"
                                       data-date="<?php echo $msg['created_at']; ?>"
                                       data-status="<?php echo $msg['status']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-warning update-status" 
                                            data-id="<?php echo $msg['id']; ?>"
                                            data-status="<?php echo $msg['status']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-message" 
                                            data-id="<?php echo $msg['id']; ?>">
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
            <nav aria-label="Messages pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_filter); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_filter); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_filter); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMessageModalLabel">Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <span id="modal-name"></span></p>
                        <p><strong>Email:</strong> <span id="modal-email"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Subject:</strong> <span id="modal-subject"></span></p>
                        <p><strong>Date:</strong> <span id="modal-date"></span></p>
                    </div>
                </div>
                <div class="mb-3">
                    <p><strong>Message:</strong></p>
                    <div class="bg-secondary p-3 rounded" id="modal-message"></div>
                </div>
                <div class="mb-3">
                    <p><strong>Status:</strong> 
                        <span id="modal-status" class="badge"></span>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Message Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" id="update-id">
                    <div class="mb-3">
                        <label for="status-select" class="form-label">Status</label>
                        <select class="form-select" id="status-select" name="status" required>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                            <option value="replied">Replied</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete-id">
                    <p>Are you sure you want to delete this message? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// View message modal
document.querySelectorAll('.view-message').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        document.getElementById('modal-name').textContent = this.dataset.name;
        document.getElementById('modal-email').textContent = this.dataset.email;
        document.getElementById('modal-subject').textContent = this.dataset.subject;
        document.getElementById('modal-message').textContent = this.dataset.message;
        document.getElementById('modal-date').textContent = this.dataset.date;
        
        // Set status badge
        const statusBadge = document.getElementById('modal-status');
        statusBadge.textContent = this.dataset.status.charAt(0).toUpperCase() + this.dataset.status.slice(1);
        statusBadge.className = 'badge';
        
        if (this.dataset.status === 'unread') {
            statusBadge.classList.add('bg-warning');
        } else if (this.dataset.status === 'read') {
            statusBadge.classList.add('bg-info');
        } else {
            statusBadge.classList.add('bg-success');
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
        modal.show();
    });
});

// Update status modal
document.querySelectorAll('.update-status').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('update-id').value = this.dataset.id;
        document.getElementById('status-select').value = this.dataset.status;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        modal.show();
    });
});

// Delete confirmation modal
document.querySelectorAll('.delete-message').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('delete-id').value = this.dataset.id;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });
});
</script>

<?php include 'includes/footer.php'; ?>