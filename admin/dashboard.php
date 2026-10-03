<?php 
require_once '../includes/header.php'; 

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

$allProducts = $productModel->getAllProducts();
$categories = $productModel->getCategories();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0">Dashboard Overview</h2>
                <div>
                    <span class="text-muted small"><?php echo date("l, F j, Y"); ?></span>
                </div>
            </div>

            <!-- Stats -->
            <div class="row row-cols-2 row-cols-lg-5 g-4 mb-5">
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="rounded-circle bg-primary-subtle p-3 d-inline-block mb-3 mx-auto">
                            <i class="fas fa-box text-primary fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo count($allProducts); ?></h3>
                        <p class="text-muted small mb-0">Total Products</p>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="rounded-circle bg-success-subtle p-3 d-inline-block mb-3 mx-auto">
                            <i class="fas fa-list text-success fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo count($categories); ?></h3>
                        <p class="text-muted small mb-0">Categories</p>
                    </div>
                </div>
                <?php
                $orders = $orderModel->getAllOrders();
                $total_orders = count($orders);
                $total_revenue = 0;
                foreach($orders as $o) {
                    $total_revenue += $o['total_amount'];
                }
                $total_users = count($userModel->getAllUsers());
                ?>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="rounded-circle bg-warning-subtle p-3 d-inline-block mb-3 mx-auto">
                            <i class="fas fa-shopping-bag text-warning fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $total_orders; ?></h3>
                        <p class="text-muted small mb-0">Total Orders</p>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="rounded-circle bg-danger-subtle p-3 d-inline-block mb-3 mx-auto">
                            <i class="fas fa-rupee-sign text-danger fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo CURRENCY; ?><?php echo number_format($total_revenue, 2); ?></h3>
                        <p class="text-muted small mb-0">Total Revenue</p>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="rounded-circle bg-info-subtle p-3 d-inline-block mb-3 mx-auto">
                            <i class="fas fa-users text-info fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $total_users; ?></h3>
                        <p class="text-muted small mb-0">Total Users</p>
                    </div>
                </div>
            </div>

            <!-- Recent Products Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Manage Products</h5>
                    <a href="products.php?action=add" class="btn btn-primary btn-sm rounded-pill px-3">Add Product <i class="fas fa-plus ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($allProducts as $p): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <img src="../assets/images/<?php echo $p['image']; ?>" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/50x50?text=P'">
                                </td>
                                <td class="py-3">
                                    <h6 class="fw-bold mb-0"><?php echo $p['name']; ?></h6>
                                    <small class="text-muted">ID: #<?php echo $p['id']; ?></small>
                                </td>
                                <td><?php echo $p['category_name'] ?: 'General'; ?></td>
                                <td class="fw-bold"><?php echo CURRENCY; ?><?php echo $p['price']; ?></td>
                                <td>
                                    <?php if($p['stock'] > 10): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2"><?php echo $p['stock']; ?> In Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2"><?php echo $p['stock']; ?> Low Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                        <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border-0 py-2 px-3"><i class="fas fa-edit text-primary"></i></a>
                                        <form action="admin_actions.php" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-light border-0 py-2 px-3 border-start"><i class="fas fa-trash text-danger"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
