</main>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold mb-3"><i class="fas fa-book-open me-2 text-primary"></i><span class="brand-text">Book Store</span></h5>
                <p>Your ultimate destination for compelling story books, graphic novels, and best-selling literature. Read more, live more.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?php echo APP_URL; ?>/shop.php" class="text-white text-decoration-none">Shop</a></li>
                    <li><a href="<?php echo APP_URL; ?>/about.php" class="text-white text-decoration-none">About Us</a></li>
                    <li><a href="<?php echo APP_URL; ?>/contact.php" class="text-white text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">Categories</h6>
                <ul class="list-unstyled">
                    <?php 
                    if(isset($productModel)) {
                        $footerCats = $productModel->getCategories();
                        $footerCats = array_slice($footerCats, 0, 4); // Show top 4
                        foreach($footerCats as $fc) {
                            echo '<li><a href="'.APP_URL.'/shop.php?category='.$fc['id'].'" class="text-white text-decoration-none">'.$fc['name'].'</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">Customer Support</h6>
                <ul class="list-unstyled">
                    <li><a href="<?php echo APP_URL; ?>/orders.php" class="text-white text-decoration-none">My Orders</a></li>
                    <li><a href="<?php echo APP_URL; ?>/about.php" class="text-white text-decoration-none">Shipping Info</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">Contact Us</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Adajan, Surat, Gujarat, India</li>
                    <li class="mb-2"><i class="fas fa-phone me-2 text-success"></i> +91 98765 43210</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@bookstore.in</li>
                </ul>
            </div>
        </div>
        <hr class="mt-4 border-secondary">
        <div class="text-center py-3">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> Book Store. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
