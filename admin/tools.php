<?php
require_once '../config.php';
requireAdmin();

$page_title = 'Manage Products';

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add') {
            // Add new product
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $type = trim($_POST['type'] ?? '');
            $image = trim($_POST['image'] ?? '');
            
            if (!empty($name) && !empty($description) && $price > 0 && !empty($category) && !empty($type)) {
                $products = JsonDB::read('../data/tools.json');
                $new_id = JsonDB::getNextId('../data/tools.json');
                
                $new_product = [
                    'id' => $new_id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category' => $category,
                    'type' => $type,
                    'image' => $image ?: 'https://via.placeholder.com/300x200/6366f1/ffffff?text=' . urlencode($name)
                ];
                
                $products[] = $new_product;
                JsonDB::write('../data/tools.json', $products);
                
                $message = 'Product added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Please fill in all required fields.';
                $message_type = 'danger';
            }
        } elseif ($action === 'edit') {
            // Edit existing product
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $type = trim($_POST['type'] ?? '');
            $image = trim($_POST['image'] ?? '');
            
            if ($id > 0 && !empty($name) && !empty($description) && $price > 0 && !empty($category) && !empty($type)) {
                $products = JsonDB::read('../data/tools.json');
                $product_found = false;
                
                foreach ($products as &$product) {
                    if ($product['id'] == $id) {
                        $product['name'] = $name;
                        $product['description'] = $description;
                        $product['price'] = $price;
                        $product['category'] = $category;
                        $product['type'] = $type;
                        $product['image'] = $image ?: 'https://via.placeholder.com/300x200/6366f1/ffffff?text=' . urlencode($name);
                        $product_found = true;
                        break;
                    }
                }
                
                if ($product_found) {
                    JsonDB::write('../data/tools.json', $products);
                    $message = 'Product updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Product not found.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Please fill in all required fields.';
                $message_type = 'danger';
            }
        } elseif ($action === 'delete') {
            // Delete product
            $id = intval($_POST['id'] ?? 0);
            
            if ($id > 0) {
                $products = JsonDB::read('../data/tools.json');
                $new_products = array_filter($products, function($product) use ($id) {
                    return $product['id'] != $id;
                });
                
                if (count($new_products) < count($products)) {
                    JsonDB::write('../data/tools.json', array_values($new_products));
                    $message = 'Product deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Product not found.';
                    $message_type = 'danger';
                }
            }
        } elseif ($action === 'bulk_delete') {
            // Bulk delete products
            $ids = $_POST['bulk_ids'] ?? [];
            
            if (!empty($ids) && is_array($ids)) {
                $products = JsonDB::read('../data/tools.json');
                $ids = array_map('intval', $ids);
                $new_products = array_filter($products, function($product) use ($ids) {
                    return !in_array($product['id'], $ids);
                });
                
                $deleted_count = count($products) - count($new_products);
                if ($deleted_count > 0) {
                    JsonDB::write('../data/tools.json', array_values($new_products));
                    $message = "Successfully deleted {$deleted_count} product(s)!";
                    $message_type = 'success';
                } else {
                    $message = 'No products were deleted.';
                    $message_type = 'warning';
                }
            } else {
                $message = 'Please select products to delete.';
                $message_type = 'warning';
            }
        } elseif ($action === 'bulk_category') {
            // Bulk update category
            $ids = $_POST['bulk_ids'] ?? [];
            $new_category = trim($_POST['new_bulk_category'] ?? '');
            
            if (!empty($ids) && is_array($ids) && !empty($new_category)) {
                $products = JsonDB::read('../data/tools.json');
                $ids = array_map('intval', $ids);
                $updated_count = 0;
                
                foreach ($products as &$product) {
                    if (in_array($product['id'], $ids)) {
                        $product['category'] = $new_category;
                        $updated_count++;
                    }
                }
                
                if ($updated_count > 0) {
                    JsonDB::write('../data/tools.json', $products);
                    $message = "Successfully updated category for {$updated_count} product(s)!";
                    $message_type = 'success';
                } else {
                    $message = 'No products were updated.';
                    $message_type = 'warning';
                }
            } else {
                $message = 'Please select products and specify a category.';
                $message_type = 'warning';
            }
        }
    }
}

// Get products data
$products = JsonDB::read('../data/tools.json');

// Get unique categories
$categories = array_unique(array_column($products, 'category'));
sort($categories);

// Product types
$product_types = ['book', 'course', 'service', 'game', 'tool', 'guide'];
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="text-gradient mb-0">Manage Products</h2>
            <p class="text-light-emphasis">Add, edit, or remove digital products</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#categoriesModal">
                <i class="fas fa-tags me-2"></i>Manage Categories
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus me-2"></i>Add New Product
            </button>
        </div>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Products Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="text-primary mb-0">
            <i class="fas fa-box me-2"></i>All Products (<?php echo count($products); ?>)
        </h5>
        <div class="d-flex gap-2">
            <div class="bulk-actions" style="display: none;">
                <select class="form-select form-select-sm" id="bulkAction">
                    <option value="">Bulk Actions</option>
                    <option value="delete">Delete Selected</option>
                    <option value="category">Change Category</option>
                </select>
                <button type="button" class="btn btn-sm btn-warning" onclick="executeBulkAction()">
                    <i class="fas fa-play me-1"></i>Apply
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="searchProducts" placeholder="Search products...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-light">No products available</h5>
                <p class="text-light-emphasis">Add your first product using the button above.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-1"></i>Add Product
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): 
                            // Calculate basic product status
                            $status = 'active';
                            $status_class = 'success';
                            $status_icon = 'check-circle';
                        ?>
                        <tr data-product-id="<?php echo $product['id']; ?>">
                            <td>
                                <input type="checkbox" class="form-check-input product-checkbox" 
                                       value="<?php echo $product['id']; ?>">
                            </td>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         class="rounded me-3" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         style="width: 50px; height: 35px; object-fit: cover;">
                                    <div>
                                        <strong class="text-light"><?php echo htmlspecialchars($product['name']); ?></strong>
                                        <br>
                                        <small class="text-light-emphasis"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars(ucfirst($product['type'] ?? 'product')); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($product['category']); ?></span>
                            </td>
                            <td class="text-success"><?php echo formatPrice($product['price']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $status_class; ?>">
                                    <i class="fas fa-<?php echo $status_icon; ?> me-1"></i><?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm"
                                            onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                            title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-info btn-sm"
                                            onclick="duplicateProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                            title="Duplicate Product">
                                        <i class="fas fa-clone"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')"
                                            title="Delete Product">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Categories Management Modal -->
<div class="modal fade" id="categoriesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tags me-2"></i>Manage Categories
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Existing Categories</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ($categories as $category): 
                            $count = count(array_filter($products, function($p) use ($category) {
                                return $p['category'] === $category;
                            }));
                        ?>
                        <span class="badge bg-primary fs-6">
                            <?php echo htmlspecialchars($category); ?>
                            <span class="badge bg-light text-dark ms-1"><?php echo $count; ?></span>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label for="newCategoryName" class="form-label">Add New Category</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newCategoryName" 
                                   placeholder="Enter category name" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    <small>Categories are automatically created when you add products. 
                    Use this to pre-create categories for organization.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Category Change Modal -->
<div class="modal fade" id="bulkCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="bulk_category">
                <input type="hidden" id="bulkCategoryIds" name="bulk_ids">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tags me-2"></i>Change Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Change category for <span id="selectedCount">0</span> selected product(s):</p>
                    <div class="mb-3">
                        <label for="newBulkCategory" class="form-label">New Category</label>
                        <select class="form-select" id="newBulkCategory" name="new_bulk_category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add New Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addName" class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="addDescription" class="form-label">Description *</label>
                        <textarea class="form-control" id="addDescription" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="addPrice" class="form-label">Price ($) *</label>
                                <input type="number" class="form-control" id="addPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="addType" class="form-label">Product Type *</label>
                                <select class="form-select" id="addType" name="type" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($product_types as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo htmlspecialchars(ucfirst($type)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="addCategory" class="form-label">Category *</label>
                                <select class="form-select" id="addCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <option value="New">+ Add New Category</option>
                                </select>
                                <div id="newCategoryField" class="mt-2" style="display: none;">
                                    <input type="text" class="form-control" name="new_category" placeholder="Enter new category">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="addImage" class="form-label">Image URL</label>
                        <input type="url" class="form-control" id="addImage" name="image" 
                               placeholder="https://example.com/image.jpg">
                        <div class="form-text">Leave blank to use default placeholder image</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="editId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description *</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editPrice" class="form-label">Price ($) *</label>
                                <input type="number" class="form-control" id="editPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editType" class="form-label">Product Type *</label>
                                <select class="form-select" id="editType" name="type" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($product_types as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo htmlspecialchars(ucfirst($type)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editCategory" class="form-label">Category *</label>
                                <select class="form-select" id="editCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <option value="New">+ Add New Category</option>
                                </select>
                                <div id="editNewCategoryField" class="mt-2" style="display: none;">
                                    <input type="text" class="form-control" name="new_category" placeholder="Enter new category">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Image URL</label>
                        <input type="url" class="form-control" id="editImage" name="image" 
                               placeholder="https://example.com/image.jpg">
                        <div class="form-text">Leave blank to use default placeholder image</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Bulk operations functionality
let selectedProducts = new Set();

// Handle select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const isChecked = this.checked;
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
        if (isChecked) {
            selectedProducts.add(parseInt(checkbox.value));
        } else {
            selectedProducts.delete(parseInt(checkbox.value));
        }
    });
    
    updateBulkActions();
});

// Handle individual checkbox changes
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            selectedProducts.add(parseInt(this.value));
        } else {
            selectedProducts.delete(parseInt(this.value));
        }
        
        updateBulkActions();
        updateSelectAll();
    });
});

// Update bulk actions visibility
function updateBulkActions() {
    const bulkActions = document.querySelector('.bulk-actions');
    if (selectedProducts.size > 0) {
        bulkActions.style.display = 'flex';
        bulkActions.style.alignItems = 'center';
        bulkActions.style.gap = '10px';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Update select all checkbox state
function updateSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
    
    if (checkedBoxes.length === checkboxes.length && checkboxes.length > 0) {
        selectAll.checked = true;
        selectAll.indeterminate = false;
    } else if (checkedBoxes.length > 0) {
        selectAll.checked = false;
        selectAll.indeterminate = true;
    } else {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
}

// Execute bulk action
function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedIds = Array.from(selectedProducts);
    
    if (!action || selectedIds.length === 0) {
        alert('Please select an action and at least one product.');
        return;
    }
    
    if (action === 'delete') {
        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected product(s)?\n\nThis action cannot be undone.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="bulk_delete">
                ${selectedIds.map(id => `<input type="hidden" name="bulk_ids[]" value="${id}">`).join('')}
            `;
            document.body.appendChild(form);
            form.submit();
        }
    } else if (action === 'category') {
        document.getElementById('selectedCount').textContent = selectedIds.length;
        document.getElementById('bulkCategoryIds').value = JSON.stringify(selectedIds);
        new bootstrap.Modal(document.getElementById('bulkCategoryModal')).show();
    }
}

// Clear selection
function clearSelection() {
    selectedProducts.clear();
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
    updateBulkActions();
}

// Search functionality
document.getElementById('searchProducts').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Handle category selection for new product
document.getElementById('addCategory').addEventListener('change', function() {
    const newCategoryField = document.getElementById('newCategoryField');
    if (this.value === 'New') {
        newCategoryField.style.display = 'block';
        newCategoryField.querySelector('input').required = true;
    } else {
        newCategoryField.style.display = 'none';
        newCategoryField.querySelector('input').required = false;
    }
});

// Handle category selection for edit product
document.getElementById('editCategory').addEventListener('change', function() {
    const newCategoryField = document.getElementById('editNewCategoryField');
    if (this.value === 'New') {
        newCategoryField.style.display = 'block';
        newCategoryField.querySelector('input').required = true;
    } else {
        newCategoryField.style.display = 'none';
        newCategoryField.querySelector('input').required = false;
    }
});

// Edit product function
function editProduct(product) {
    document.getElementById('editId').value = product.id;
    document.getElementById('editName').value = product.name;
    document.getElementById('editDescription').value = product.description;
    document.getElementById('editPrice').value = product.price;
    document.getElementById('editType').value = product.type || 'tool';
    document.getElementById('editCategory').value = product.category;
    document.getElementById('editImage').value = product.image;
    
    // Hide new category field if not needed
    document.getElementById('editNewCategoryField').style.display = 'none';
    
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}

// Duplicate product function
function duplicateProduct(product) {
    document.getElementById('addName').value = product.name + ' (Copy)';
    document.getElementById('addDescription').value = product.description;
    document.getElementById('addPrice').value = product.price;
    document.getElementById('addType').value = product.type || 'tool';
    document.getElementById('addCategory').value = product.category;
    document.getElementById('addImage').value = product.image;
    
    new bootstrap.Modal(document.getElementById('addProductModal')).show();
}

// Delete product function
function deleteProduct(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
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

// Add category form handler
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const categoryName = document.getElementById('newCategoryName').value.trim();
    
    if (categoryName) {
        // Add to existing select elements
        const selects = ['addCategory', 'editCategory', 'newBulkCategory'];
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            const option = document.createElement('option');
            option.value = categoryName;
            option.textContent = categoryName;
            select.insertBefore(option, select.querySelector('option[value="New"]') || select.lastElementChild);
        });
        
        document.getElementById('newCategoryName').value = '';
        alert(`Category "${categoryName}" added successfully!`);
    }
});

// Auto-focus first field in modals
document.getElementById('addProductModal').addEventListener('shown.bs.modal', function() {
    document.getElementById('addName').focus();
});

document.getElementById('editProductModal').addEventListener('shown.bs.modal', function() {
    document.getElementById('editName').focus();
});

document.getElementById('categoriesModal').addEventListener('shown.bs.modal', function() {
    document.getElementById('newCategoryName').focus();
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateBulkActions();
});
</script>

<?php include 'includes/footer.php'; ?>