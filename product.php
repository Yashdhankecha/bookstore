<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$product = $productModel->getProductById($id);

if(!$product) {
    header("Location: shop.php");
    exit();
}

$rating = $productModel->getAverageRating($product['id']);
$avg_rating = $rating['avg_rating'] ? round($rating['avg_rating'], 1) : 0;
$review_count = (int)$rating['review_count'];
$reviews = $productModel->getReviewsForProduct($product['id']);

$has_discount = $product['discount'] > 0;
$final_price = $product['price'] - $product['discount'];

$is_wishlisted = false;
if(isset($_SESSION['user_id'])) {
    $is_wishlisted = $productModel->isInWishlist($_SESSION['user_id'], $product['id']);
}

$user_has_reviewed = false;
if(isset($_SESSION['user_id'])) {
    $user_has_reviewed = $productModel->userHasReviewed($_SESSION['user_id'], $product['id']);
}
?>

<div class="container py-5">
    <div class="row g-5">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                <img src="assets/images/<?php echo $product['image']; ?>" class="w-100 h-auto" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='https://via.placeholder.com/600x600?text=Product'">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="ps-lg-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item small"><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item small"><a href="shop.php" class="text-muted text-decoration-none">Shop</a></li>
                        <li class="breadcrumb-item small active" aria-current="page"><?php echo htmlspecialchars($product['category_name'] ?? 'Books'); ?></li>
                    </ol>
                </nav>

                <h1 class="fw-bold mb-2 display-5 font-serif"><?php echo htmlspecialchars($product['name']); ?></h1>
                <?php if(!empty($product['author_name'])): ?>
                    <p class="text-muted mb-3">by <span class="fw-bold text-dark"><?php echo htmlspecialchars($product['author_name']); ?></span></p>
                <?php endif; ?>

                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fa<?php echo $i <= round($avg_rating) ? 's' : 'r'; ?> fa-star small"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted small">
                        <?php if($review_count > 0): ?>
                            (<?php echo $avg_rating; ?> / 5.0) from <?php echo $review_count; ?> Review<?php echo $review_count == 1 ? '' : 's'; ?>
                        <?php else: ?>
                            No reviews yet
                        <?php endif; ?>
                    </span>
                </div>

                <?php if($has_discount): ?>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <h2 class="text-primary fw-bold mb-0"><?php echo CURRENCY; ?><?php echo number_format($final_price, 2); ?></h2>
                        <span class="text-muted text-decoration-line-through fs-5"><?php echo CURRENCY; ?><?php echo number_format($product['price'], 2); ?></span>
                        <span class="badge badge-pill-soft">Save <?php echo CURRENCY; ?><?php echo number_format($product['discount'], 2); ?></span>
                    </div>
                <?php else: ?>
                    <h2 class="text-primary fw-bold mb-4"><?php echo CURRENCY; ?><?php echo number_format($product['price'], 2); ?></h2>
                <?php endif; ?>

                <p class="text-muted mb-5 lead opacity-75"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <hr class="my-4">

                <!-- Stock Status -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="fw-bold me-2 small">Availability:</span>
                        <?php if($product['stock'] > 0): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">In Stock</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">Only <?php echo $product['stock']; ?> items remaining!</small>
                </div>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="row g-3 mb-5">
                        <form action="cart_action.php" method="POST" class="col-9 row g-3 align-items-end m-0">
                            <div class="col-4">
                                <label class="form-label small fw-bold">Qty:</label>
                                <input type="number" name="qty" class="form-control rounded-3 py-2 fw-bold" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-3 shadow-sm">
                                    <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                </button>
                            </div>
                        </form>
                        <form action="wishlist_action.php" method="POST" class="col-3">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="action" value="<?php echo $is_wishlisted ? 'remove' : 'add'; ?>">
                            <button type="submit" class="btn <?php echo $is_wishlisted ? 'btn-danger' : 'btn-outline-danger'; ?> w-100 py-3 rounded-3 shadow-sm" title="<?php echo $is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="mb-5">
                        <a href="login.php" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-3 shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i> Login to Buy Now
                        </a>
                        <p class="text-center mt-2 small text-muted">You must be logged in to place an order.</p>
                    </div>
                <?php endif; ?>

                <div class="d-flex align-items-center p-3 rounded-4 bg-light shadow-sm mb-5">
                    <div class="me-3">
                        <i class="fas fa-book-reader text-secondary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold small">FREE Shipping on this item!</h6>
                        <p class="mb-0 small text-muted">Delivery expected in 3 - 5 business days.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="col-12 mt-5">
            <ul class="nav nav-tabs border-0 bg-white shadow-sm rounded-4 p-2 mb-4" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active border-0 rounded-3 px-4 py-2 me-2" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 rounded-3 px-4 py-2 me-2" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button">Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 rounded-3 px-4 py-2" id="rev-tab" data-bs-toggle="tab" data-bs-target="#rev" type="button">Reviews (<?php echo $review_count; ?>)</button>
                </li>
            </ul>
            <div class="tab-content bg-white p-4 rounded-4 shadow-sm" id="productTabContent">
                <div class="tab-pane fade show active" id="desc" role="tabpanel">
                    <p class="mb-0 p-3"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                <div class="tab-pane fade" id="spec" role="tabpanel">
                    <div class="p-3">
                        <table class="table table-striped rounded-3 overflow-hidden">
                            <tbody>
                                <tr><th class="w-25 border-0">Author</th><td class="border-0"><?php echo htmlspecialchars($product['author_name'] ?? 'N/A'); ?></td></tr>
                                <tr><th class="w-25">Category</th><td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td></tr>
                                <tr><th class="w-25">ISBN</th><td><?php echo htmlspecialchars($product['isbn'] ?? 'N/A'); ?></td></tr>
                                <tr><th class="w-25">Publisher</th><td><?php echo htmlspecialchars($product['publisher'] ?? 'N/A'); ?></td></tr>
                                <tr><th class="w-25">Publication Date</th><td><?php echo !empty($product['publication_date']) ? date('d M, Y', strtotime($product['publication_date'])) : 'N/A'; ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="rev" role="tabpanel">
                    <div class="p-3" id="rev-anchor">
                        <h6 class="fw-bold mb-4">Customer Reviews</h6>

                        <?php if(empty($reviews)): ?>
                            <p class="text-muted small mb-4">No reviews yet. Be the first to share your thoughts!</p>
                        <?php else: ?>
                            <?php foreach($reviews as $r): ?>
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-circle fs-4 me-2 text-secondary"></i>
                                    <span class="fw-bold me-2"><?php echo htmlspecialchars($r['user_name']); ?></span>
                                    <div class="text-warning small">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa<?php echo $i <= $r['rating'] ? 's' : 'r'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-muted small ms-2"><?php echo date('d M, Y', strtotime($r['created_at'])); ?></span>
                                </div>
                                <?php if(!empty($r['comment'])): ?>
                                    <p class="text-muted small mb-0"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['user_id'])): ?>
                            <?php if($user_has_reviewed): ?>
                                <p class="small text-muted fst-italic">You've already reviewed this book. Thank you for your feedback!</p>
                            <?php else: ?>
                                <h6 class="fw-bold mt-4 mb-3">Write a Review</h6>
                                <form action="review_action.php" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Rating</label>
                                        <select name="rating" class="form-select rounded-3 w-auto" required>
                                            <option value="5">5 - Excellent</option>
                                            <option value="4">4 - Good</option>
                                            <option value="3">3 - Average</option>
                                            <option value="2">2 - Poor</option>
                                            <option value="1">1 - Terrible</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Your Review</label>
                                        <textarea name="comment" class="form-control rounded-3" rows="3" placeholder="Share your thoughts about this book..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Submit Review</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="small text-muted"><a href="login.php">Login</a> to write a review.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.location.hash === '#rev') {
        const trigger = document.getElementById('rev-tab');
        if (trigger) new bootstrap.Tab(trigger).show();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
