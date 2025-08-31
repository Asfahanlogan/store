<?php
require_once 'config.php';
$page_title = 'Home';
$products = JsonDB::read('tools.json');
$featured_products = array_slice($products, 0, 6); // Show first 6 products
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section py-5 my-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="animate-fade-in">
                    <h1 class="display-4 fw-bold mb-4">
                        Digital <span class="text-gradient">Marketplace</span> Pro
                    </h1>
                    <p class="lead text-light-emphasis mb-4">
                        Discover premium digital products including courses, books, tools, games, guides, and professional services. 
                        Everything you need to learn, grow, and succeed in the digital world.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="products.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Browse Products
                        </a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope me-2"></i>Get Support
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <div class="position-relative">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-circle p-5 d-inline-block">
                            <i class="fas fa-store text-white" style="font-size: 8rem;"></i>
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100">
                            <div class="bg-primary rounded-circle position-absolute" style="width: 20px; height: 20px; top: 20%; left: 20%; animation: pulse 2s infinite;"></div>
                            <div class="bg-success rounded-circle position-absolute" style="width: 15px; height: 15px; top: 60%; left: 80%; animation: pulse 2s infinite 0.5s;"></div>
                            <div class="bg-warning rounded-circle position-absolute" style="width: 25px; height: 25px; top: 80%; left: 30%; animation: pulse 2s infinite 1s;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-box fa-2x mb-3"></i>
                    <h3 class="fw-bold"><?php echo count($products); ?>+</h3>
                    <p class="mb-0">Digital Products</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-users fa-2x mb-3"></i>
                    <h3 class="fw-bold">5,000+</h3>
                    <p class="mb-0">Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-shield-check fa-2x mb-3"></i>
                    <h3 class="fw-bold">99.9%</h3>
                    <p class="mb-0">Uptime</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-clock fa-2x mb-3"></i>
                    <h3 class="fw-bold">24/7</h3>
                    <p class="mb-0">Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-gradient mb-3">Featured Products</h2>
            <p class="lead text-light-emphasis">Explore our most popular digital products across all categories</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-gradient">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($product['category']); ?></span>
                            <span class="h5 text-success mb-0"><?php echo formatPrice($product['price']); ?></span>
                        </div>
                        <h5 class="card-title text-light"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-light-emphasis flex-grow-1">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="checkout.php?tool_id=<?php echo $product['id']; ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-bolt me-1"></i>Buy Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-right me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-gradient mb-3">Why Choose Digital Marketplace Pro?</h2>
            <p class="lead text-light-emphasis">Trusted by professionals and learners worldwide</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <h4 class="text-light mb-3">100% Quality & Authentic</h4>
                    <p class="text-light-emphasis">
                        All our products are carefully curated and verified for quality. 
                        Get premium digital content from trusted creators and professionals.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="text-light mb-3">Cutting-Edge Content</h4>
                    <p class="text-light-emphasis">
                        Stay ahead with the latest courses, books, tools, and resources 
                        created by industry experts and leading professionals.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="text-light mb-3">Expert Support</h4>
                    <p class="text-light-emphasis">
                        Get help from our dedicated support team with 24/7 
                        assistance and comprehensive product documentation.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fab fa-bitcoin"></i>
                    </div>
                    <h4 class="text-light mb-3">Crypto Payments</h4>
                    <p class="text-light-emphasis">
                        Secure and anonymous payments using Bitcoin, Ethereum, 
                        and Litecoin for maximum privacy protection.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-download"></i>
                    </div>
                    <h4 class="text-light mb-3">Instant Download</h4>
                    <p class="text-light-emphasis">
                        Get immediate access to your purchased products with instant 
                        download links and access credentials after payment confirmation.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h4 class="text-light mb-3">Regular Updates</h4>
                    <p class="text-light-emphasis">
                        Stay current with regular content updates, new features, 
                        and additional resources at no additional cost.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-gradient mb-3">Product Categories</h2>
            <p class="lead text-light-emphasis">Explore our diverse range of digital products</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $categories = array_unique(array_column($products, 'category'));
            $category_icons = [
                'Courses' => 'fas fa-graduation-cap',
                'Books' => 'fas fa-book',
                'Services' => 'fas fa-handshake',
                'Games' => 'fas fa-gamepad',
                'Tools' => 'fas fa-tools',
                'Guides' => 'fas fa-map'
            ];
            
            foreach ($categories as $category):
                $count = count(array_filter($products, function($product) use ($category) {
                    return $product['category'] === $category;
                }));
                $icon = $category_icons[$category] ?? 'fas fa-box';
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="<?php echo $icon; ?> fa-3x text-primary mb-3"></i>
                        <h5 class="card-title text-light"><?php echo htmlspecialchars($category); ?></h5>
                        <p class="text-light-emphasis"><?php echo $count; ?> products available</p>
                        <a href="products.php?category=<?php echo urlencode($category); ?>" 
                           class="btn btn-outline-primary">
                            Explore Category
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>