<?php 
require_once 'includes/header.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$_SESSION['wishlist_notified'] = false; // Mark as seen
$wishlistItems = $productModel->getWishlistItems($user_id);
?>

<div class="container py-5">
    <div class="row min-vh-100">
        <div class="col-lg-12">
            <h2 class="fw-bold mb-4">My Wishlist</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <?php if($_GET['success'] == 'wishlist_removed'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Item removed from your wishlist.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(empty($wishlistItems)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-heart display-1 text-light mb-4"></i>
                    <h3 class="fw-bold">Your wishlist is empty</h3>
                    <p class="text-muted mb-4">Save your favorite books here to buy them later.</p>
                    <a href="shop.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold text-uppercase">Start Browsing</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($wishlistItems as $item): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="card product-card h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                            <form action="wishlist_action.php" method="POST" class="position-absolute top-0 end-0 m-2 z-index-2">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="btn btn-light rounded-circle shadow-sm text-danger" title="Remove from Wishlist">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            
                            <img src="assets/images/<?php echo $item['image']; ?>" class="card-img-top" alt="<?php echo $item['name']; ?>" onerror="this.src='https://via.placeholder.com/300x300?text=<?php echo urlencode($item['name']); ?>'">
                            <div class="card-body d-flex flex-column">
                                <h6 class="fw-bold mb-2">
                                    <a href="product.php?id=<?php echo $item['id']; ?>" class="text-dark text-decoration-none"><?php echo $item['name']; ?></a>
                                </h6>
                                <div class="mt-auto d-flex align-items-center justify-content-between">
                                    <span class="fw-bold text-primary fs-5"><?php echo CURRENCY; ?><?php echo $item['price']; ?></span>
                                    <form action="cart_action.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-primary rounded-pill px-3 shadow-sm" title="Add to Cart">
                                            <i class="fas fa-cart-plus"></i> <span class="small ms-1 d-none d-xl-inline">Add</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
