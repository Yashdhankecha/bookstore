<!-- Admin Sidebar -->
<div class="col-md-3 col-lg-2 px-0 admin-sidebar d-none d-md-block shadow-sm">
    <div class="px-4 py-3 border-bottom border-secondary mb-3">
        <h5 class="fw-bold mb-0 text-white"><i class="fas fa-user-shield me-2"></i>Admin Panel</h5>
    </div>
    <div class="nav flex-column">
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-chart-line me-3"></i> Dashboard
        </a>
        <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' || basename($_SERVER['PHP_SELF']) == 'edit_product.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-box me-3"></i> Products
        </a>
        <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-list me-3"></i> Categories
        </a>
        <a href="authors.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'authors.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-feather-alt me-3"></i> Authors
        </a>
        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' || basename($_SERVER['PHP_SELF']) == 'view_order.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-shopping-bag me-3"></i> Orders
        </a>
        <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-users me-3"></i> Users
        </a>
        <a href="reviews.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-star me-3"></i> Reviews
        </a>
        <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-chart-pie me-3"></i> Reports
        </a>
        <a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?> px-4 py-3 text-white text-decoration-none d-flex align-items-center">
            <i class="fas fa-envelope-open-text me-3"></i> Messages
        </a>
        <hr class="bg-secondary mx-3 my-4 opacity-25">
        <a href="../index.php" class="px-4 py-3 text-info text-decoration-none d-flex align-items-center">
            <i class="fas fa-store me-3"></i> Return to Store
        </a>
        <a href="../logout.php" class="px-4 py-3 text-danger text-decoration-none d-flex align-items-center">
            <i class="fas fa-sign-out-alt me-3"></i> Logout
        </a>
    </div>
</div>
