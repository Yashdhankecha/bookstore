<?php 
require_once 'includes/header.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = $orderModel->getUserOrders($user_id);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">My Orders</h2>
                <a href="shop.php" class="btn btn-outline-primary rounded-pill px-4">Continue Shopping</a>
            </div>

            <?php if(empty($orders)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-shopping-bag display-1 text-light mb-4"></i>
                    <h3 class="fw-bold">No orders yet</h3>
                    <p class="text-muted mb-4">You haven't placed any orders yet. Explore our collection!</p>
                    <a href="shop.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Shop Now</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Order ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                <tr>
                                    <td class="ps-4 py-3 fw-bold">#ORD-<?php echo $order['id']; ?></td>
                                    <td><?php echo date("d M, Y", strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'primary' : 'info')); 
                                        ?> rounded-pill px-3 py-2 fw-bold">
                                            <?php 
                                            if($order['status'] == 'delivered') echo 'Completed';
                                            elseif($order['status'] == 'processing') echo 'Confirmed';
                                            else echo ucfirst($order['status']); 
                                            ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold"><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></td>
                                    <td class="text-end pe-4">
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
