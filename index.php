<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section text-center py-5">
    <div class="container py-5">
        <h1 class="display-3 fw-bold mb-4">Discover Your Next Great Adventure.</h1>
        <p class="lead mb-5">From thrilling stories to epic comics and classic novels, we have the perfect book for every reader.</p>
        <a href="shop.php" class="btn btn-primary btn-lg shadow-sm px-5 py-3 rounded-pill text-uppercase fw-bold">Explore Books</a>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container py-3">
        <div class="row align-items-center mb-4">
            <div class="col-6">
                <h2 class="fw-bold mb-0">Shop by Category</h2>
            </div>
            <div class="col-6 text-end">
                <a href="shop.php" class="text-secondary text-decoration-none fw-bold">View All <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row g-4">
            <?php 
            $categories = $productModel->getCategories();
            foreach($categories as $cat): ?>
            <div class="col-md-4">
                <a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 category-card position-relative">
                        <img src="assets/images/<?php echo $cat['image']; ?>" class="card-img-top" alt="<?php echo $cat['name']; ?>" onerror="this.src='https://via.placeholder.com/400x250?text=<?php echo urlencode($cat['name']); ?>'">
                        <div class="card-body text-center bg-white">
                            <h5 class="fw-bold text-dark mb-0"><?php echo $cat['name']; ?></h5>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row align-items-center mb-4">
            <div class="col-6">
                <h2 class="fw-bold mb-0">New Arrivals</h2>
            </div>
            <div class="col-6 text-end">
                <a href="shop.php" class="text-secondary text-decoration-none fw-bold">View All <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row g-4">
            <?php 
            $products = $productModel->getAllProducts();
            // Show only first 4 for home
            $featured = array_slice($products, 0, 4);
            foreach($featured as $p): ?>
            <div class="col-md-3">
                <div class="card product-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="assets/images/<?php echo $p['image']; ?>" class="card-img-top" alt="<?php echo $p['name']; ?>" onerror="this.src='https://via.placeholder.com/300x300?text=<?php echo urlencode($p['name']); ?>'">
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-1 d-block text-uppercase small ls-1"><?php echo htmlspecialchars($p['category_name'] ?? 'Book'); ?></small>
                        <h6 class="fw-bold mb-2">
                            <a href="product.php?id=<?php echo $p['id']; ?>" class="text-dark text-decoration-none"><?php echo $p['name']; ?></a>
                        </h6>
                        <div class="mt-auto d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-primary"><?php echo CURRENCY; ?><?php echo $p['price']; ?></span>
                            <form action="cart_action.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="fas fa-cart-plus me-1"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Promo Section -->
<section class="py-5">
    <div class="container py-3">
        <div class="row g-0 rounded-5 overflow-hidden shadow-sm bg-white border border-light">
            <div class="col-md-6 bg-dark text-white p-5 d-flex flex-column justify-content-center">
                <h4 class="text-secondary fw-bold mb-3">Best-Seller Offer</h4>
                <h2 class="display-5 fw-bold mb-4">Save up to 30% on Comic Series</h2>
                <p class="mb-5 lead opacity-75">Dive into the world of action and adventure with our exclusive comic collection. Limited time offer.</p>
                <div>
                    <a href="shop.php" class="btn btn-primary px-4 py-2 fw-bold text-uppercase rounded-pill">Grab the Deal</a>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-block">
                <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&q=80&w=800" class="w-100 h-100 object-fit-cover" alt="Art Supplies">
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
