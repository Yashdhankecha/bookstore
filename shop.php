<?php
require_once 'includes/header.php';

$category_id = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$author_id = isset($_GET['author']) && $_GET['author'] !== '' ? $_GET['author'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$products = $productModel->getFilteredProducts($category_id, $author_id, $search);

$categories = $productModel->getCategories();
$authors = $productModel->getAuthors();
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 sticky-lg-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Filters</h5>

                <!-- Search -->
                <form action="shop.php" method="GET" class="mb-4">
                    <?php if($category_id): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category_id); ?>"><?php endif; ?>
                    <?php if($author_id): ?><input type="hidden" name="author" value="<?php echo htmlspecialchars($author_id); ?>"><?php endif; ?>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>

                <!-- Categories -->
                <h6 class="fw-bold mb-3 small text-uppercase ls-1">Categories</h6>
                <div class="list-group list-group-flush mb-4">
                    <a href="shop.php?<?php echo http_build_query(array_filter(['author' => $author_id, 'search' => $search])); ?>" class="list-group-item list-group-item-action border-0 px-0 mb-1 <?php echo !$category_id ? 'fw-bold text-primary border-bottom' : ''; ?>">
                        All Categories
                    </a>
                    <?php foreach($categories as $cat): ?>
                    <a href="shop.php?<?php echo http_build_query(array_filter(['category' => $cat['id'], 'author' => $author_id, 'search' => $search])); ?>" class="list-group-item list-group-item-action border-0 px-0 mb-1 <?php echo $category_id == $cat['id'] ? 'fw-bold text-primary border-bottom' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Authors -->
                <h6 class="fw-bold mb-3 small text-uppercase ls-1">Authors</h6>
                <div class="list-group list-group-flush mb-2">
                    <a href="shop.php?<?php echo http_build_query(array_filter(['category' => $category_id, 'search' => $search])); ?>" class="list-group-item list-group-item-action border-0 px-0 mb-1 <?php echo !$author_id ? 'fw-bold text-primary border-bottom' : ''; ?>">
                        All Authors
                    </a>
                    <?php foreach($authors as $a): ?>
                    <a href="shop.php?<?php echo http_build_query(array_filter(['category' => $category_id, 'author' => $a['id'], 'search' => $search])); ?>" class="list-group-item list-group-item-action border-0 px-0 mb-1 <?php echo $author_id == $a['id'] ? 'fw-bold text-primary border-bottom' : ''; ?>">
                        <?php echo htmlspecialchars($a['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    <?php
                    $title_parts = [];
                    if($category_id) {
                        $cat_info = array_filter($categories, function($c) use ($category_id) { return $c['id'] == $category_id; });
                        $cat_info = reset($cat_info);
                        if($cat_info) $title_parts[] = $cat_info['name'];
                    }
                    if($author_id) {
                        $auth_info = array_filter($authors, function($a) use ($author_id) { return $a['id'] == $author_id; });
                        $auth_info = reset($auth_info);
                        if($auth_info) $title_parts[] = 'by ' . $auth_info['name'];
                    }
                    if(!empty($search)) {
                        $title_parts[] = 'Search: ' . htmlspecialchars($search);
                    }
                    echo !empty($title_parts) ? implode(' &middot; ', $title_parts) : 'All Products';
                    ?>
                </h4>
                <div class="d-flex align-items-center">
                    <small class="text-muted me-2"><?php echo count($products); ?> Items Found</small>
                </div>
            </div>

            <div class="row g-4">
                <?php if(empty($products)): ?>
                    <div class="col-12 py-5 text-center">
                        <i class="fas fa-search-minus display-1 opacity-25 mb-3"></i>
                        <h4 class="fw-light">No products found matching your criteria.</h4>
                    </div>
                <?php else: ?>
                    <?php foreach($products as $p):
                        $p_rating = $productModel->getAverageRating($p['id']);
                        $p_avg = $p_rating['avg_rating'] ? round($p_rating['avg_rating'], 1) : 0;
                        $p_count = (int)$p_rating['review_count'];
                        $p_final = $p['price'] - $p['discount'];
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card product-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <img src="assets/images/<?php echo $p['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='https://via.placeholder.com/300x300?text=<?php echo urlencode($p['name']); ?>'">
                            <div class="card-body d-flex flex-column">
                                <h6 class="fw-bold mb-1">
                                    <a href="product.php?id=<?php echo $p['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($p['name']); ?></a>
                                </h6>
                                <?php if(!empty($p['author_name'])): ?>
                                    <small class="text-muted mb-2">by <?php echo htmlspecialchars($p['author_name']); ?></small>
                                <?php endif; ?>
                                <?php if($p_count > 0): ?>
                                    <div class="mb-2 small">
                                        <span class="text-warning"><i class="fas fa-star"></i> <?php echo $p_avg; ?></span>
                                        <span class="text-muted">(<?php echo $p_count; ?>)</span>
                                    </div>
                                <?php endif; ?>
                                <p class="text-muted small flex-grow-1 opacity-75 mb-3"><?php echo htmlspecialchars(substr($p['description'], 0, 80)); ?>...</p>
                                    <div class="mt-auto d-flex align-items-center justify-content-between">
                                        <span>
                                            <span class="fw-bold text-primary fs-5"><?php echo CURRENCY; ?><?php echo number_format($p_final, 2); ?></span>
                                            <?php if($p['discount'] > 0): ?>
                                                <small class="text-muted text-decoration-line-through ms-1"><?php echo CURRENCY; ?><?php echo number_format($p['price'], 2); ?></small>
                                            <?php endif; ?>
                                        </span>
                                        <div class="d-flex gap-2">
                                            <?php if(isset($_SESSION['user_id'])): ?>
                                                <form action="wishlist_action.php" method="POST" class="m-0">
                                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                    <input type="hidden" name="action" value="add">
                                                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3 shadow-sm" title="Add to Favorite">
                                                        <i class="fas fa-heart"></i>
                                                    </button>
                                                </form>
                                                <form action="cart_action.php" method="POST" class="m-0">
                                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                    <input type="hidden" name="action" value="add">
                                                    <button type="submit" class="btn btn-primary rounded-pill px-3 shadow-sm" title="Add to Cart">
                                                        <i class="fas fa-cart-plus"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a href="login.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" title="Login to buy">
                                                    Login <i class="fas fa-sign-in-alt ms-1"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
