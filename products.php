<?php
require_once 'config.php';
$page_title = 'Products';
$products = JsonDB::read('tools.json');

// Get filter parameters
$category_filter = $_GET['category'] ?? '';
$search_filter = $_GET['search'] ?? '';
$sort_by = $_GET['sort'] ?? 'name';
$price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
$price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;
$type_filter = $_GET['type'] ?? '';
$price_sort = $_GET['price_sort'] ?? '';

// Get price range from all products
$all_prices = array_column($products, 'price');
$min_price = !empty($all_prices) ? floor(min($all_prices)) : 0;
$max_price = !empty($all_prices) ? ceil(max($all_prices)) : 1000;

// Set default price range if not specified
if ($price_min <= 0) $price_min = $min_price;
if ($price_max <= 0) $price_max = $max_price;

// Filter products
$filtered_products = $products;

if (!empty($category_filter)) {
    $filtered_products = array_filter($filtered_products, function($product) use ($category_filter) {
        return $product['category'] === $category_filter;
    });
}

if (!empty($type_filter)) {
    $filtered_products = array_filter($filtered_products, function($product) use ($type_filter) {
        return ($product['type'] ?? 'product') === $type_filter;
    });
}

if (!empty($search_filter)) {
    $filtered_products = array_filter($filtered_products, function($product) use ($search_filter) {
        return stripos($product['name'], $search_filter) !== false || 
               stripos($product['description'], $search_filter) !== false ||
               stripos($product['category'], $search_filter) !== false;
    });
}

// Filter by price range
if ($price_min > 0 || $price_max < $max_price) {
    $filtered_products = array_filter($filtered_products, function($product) use ($price_min, $price_max) {
        return $product['price'] >= $price_min && $product['price'] <= $price_max;
    });
}

// Sort products
usort($filtered_products, function($a, $b) use ($sort_by) {
    switch ($sort_by) {
        case 'price_low':
            return $a['price'] <=> $b['price'];
        case 'price_high':
            return $b['price'] <=> $a['price'];
        case 'category':
            return strcmp($a['category'], $b['category']);
        case 'name_desc':
            return strcmp($b['name'], $a['name']);
        case 'newest':
            return $b['id'] <=> $a['id']; // Assuming higher ID means newer
        default:
            return strcmp($a['name'], $b['name']);
    }
});

// Get unique categories and types
$categories = array_unique(array_column($products, 'category'));
sort($categories);

$types = array_unique(array_column($products, 'type'));
$types = array_filter($types); // Remove empty values
sort($types);
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-gradient mb-3">Digital Products Collection</h1>
        <p class="lead text-light-emphasis">Premium courses, books, tools, games, guides, and services for everyone</p>
    </div>

    <!-- Advanced Filters and Search -->
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Search & Filters
                <button class="btn btn-outline-light btn-sm float-end" type="button" 
                        data-bs-toggle="collapse" data-bs-target="#advancedFilters" 
                        aria-expanded="false">
                    <i class="fas fa-cog me-1"></i>Advanced
                </button>
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <!-- Basic Search Row -->
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Search Products</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Search by name, description, or category..."
                                   value="<?php echo htmlspecialchars($search_filter); ?>">
                            <?php if (!empty($search_filter)): ?>
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>" 
                                    <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name (A-Z)</option>
                            <option value="name_desc" <?php echo $sort_by === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                            <option value="price_low" <?php echo $sort_by === 'price_low' ? 'selected' : ''; ?>>Price (Low-High)</option>
                            <option value="price_high" <?php echo $sort_by === 'price_high' ? 'selected' : ''; ?>>Price (High-Low)</option>
                            <option value="category" <?php echo $sort_by === 'category' ? 'selected' : ''; ?>>Category</option>
                            <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Filters (Collapsible) -->
                <div class="collapse" id="advancedFilters">
                    <hr class="border-secondary">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label">Product Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <?php foreach ($types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" 
                                        <?php echo $type_filter === $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst($type)); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Price Range: $<span id="priceRangeDisplay"><?php echo $price_min; ?> - $<?php echo $price_max; ?></span></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" 
                                           name="price_min" id="priceMin" 
                                           placeholder="Min" min="<?php echo $min_price; ?>" 
                                           max="<?php echo $max_price; ?>" 
                                           value="<?php echo $price_min; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" 
                                           name="price_max" id="priceMax" 
                                           placeholder="Max" min="<?php echo $min_price; ?>" 
                                           max="<?php echo $max_price; ?>" 
                                           value="<?php echo $price_max; ?>">
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="range" class="form-range" id="priceRangeSlider" 
                                       min="<?php echo $min_price; ?>" max="<?php echo $max_price; ?>" 
                                       value="<?php echo $price_max; ?>" step="5">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Quick Price Filters</label>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-outline-success btn-sm" 
                                        onclick="setPrice(0, 25)">Under $25</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                        onclick="setPrice(25, 100)">$25-$100</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="setPrice(100, 9999)">$100+</button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-filter me-1"></i>Apply Filters
                                </button>
                                <a href="products.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="text-light">
            <?php if (!empty($category_filter) || !empty($search_filter)): ?>
                Showing <?php echo count($filtered_products); ?> results
                <?php if (!empty($search_filter)): ?>
                    for "<?php echo htmlspecialchars($search_filter); ?>"
                <?php endif; ?>
                <?php if (!empty($category_filter)): ?>
                    in <?php echo htmlspecialchars($category_filter); ?>
                <?php endif; ?>
            <?php else: ?>
                All <?php echo count($filtered_products); ?> Products
            <?php endif; ?>
        </h5>
        
        <?php if (!empty($category_filter) || !empty($search_filter)): ?>
        <a href="products.php" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i>Clear Filters
        </a>
        <?php endif; ?>
    </div>

    <!-- Products Grid -->
    <?php if (empty($filtered_products)): ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-light">No products found</h4>
            <p class="text-light-emphasis">Try adjusting your search criteria or browse all categories.</p>
            <a href="products.php" class="btn btn-primary">View All Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($filtered_products as $product): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-gradient product-card">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             style="height: 250px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($product['category']); ?></span>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <h5 class="card-title text-light mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-light-emphasis">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 text-success mb-0"><?php echo formatPrice($product['price']); ?></span>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <small class="text-light-emphasis ms-1">(5.0)</small>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php?tool_id=<?php echo $product['id']; ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-bolt me-1"></i>Buy Now
                                </a>
                                <button class="btn btn-outline-light btn-sm" 
                                        onclick="showProductDetails(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                    <i class="fas fa-info-circle me-1"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="productModalImage" src="" alt="" class="img-fluid rounded mb-3">
                    </div>
                    <div class="col-md-6">
                        <p><strong>Category:</strong> <span id="productModalCategory" class="badge bg-primary"></span></p>
                        <p><strong>Price:</strong> <span id="productModalPrice" class="text-success h5"></span></p>
                        <p><strong>Description:</strong></p>
                        <p id="productModalDescription" class="text-light-emphasis"></p>
                        
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-primary mb-2"><i class="fas fa-check-circle me-1"></i>What's Included:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-download text-success me-2"></i>Instant access/download</li>
                                <li><i class="fas fa-book text-info me-2"></i>Comprehensive documentation</li>
                                <li><i class="fas fa-headset text-warning me-2"></i>24/7 technical support</li>
                                <li><i class="fas fa-sync-alt text-primary me-2"></i>Free updates for 1 year</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Note:</strong> All digital products come with our satisfaction guarantee.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="productModalBuyBtn" href="#" class="btn btn-success">
                    <i class="fas fa-shopping-cart me-2"></i>Buy Now
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showProductDetails(product) {
    document.getElementById('productModalTitle').textContent = product.name;
    document.getElementById('productModalImage').src = product.image;
    document.getElementById('productModalImage').alt = product.name;
    document.getElementById('productModalCategory').textContent = product.category;
    document.getElementById('productModalPrice').textContent = '$' + product.price.toFixed(2);
    document.getElementById('productModalDescription').textContent = product.description;
    document.getElementById('productModalBuyBtn').href = 'checkout.php?tool_id=' + product.id;
    
    new bootstrap.Modal(document.getElementById('productDetailsModal')).show();
}

// Add hover effects
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px)';
        this.style.transition = 'all 0.3s ease';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>

<?php include 'includes/footer.php'; ?>