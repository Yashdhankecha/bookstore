<?php 
require_once '../includes/header.php'; 

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

$order_id = isset($_GET['id']) ? $_GET['id'] : 0;
$order = $orderModel->getOrderById($order_id);

if (!$order) {
    header("Location: orders.php");
    exit();
}

$items = $orderModel->getOrderItems($order_id);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0">Order Details #ORD-<?php echo $order['id']; ?></h2>
                <a href="orders.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to Orders</a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 px-4">
                            <h5 class="fw-bold mb-0">Items Purchased</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end pe-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $i): ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/images/<?php echo $i['image']; ?>" width="40" height="40" class="rounded object-fit-cover me-3" alt="">
                                                <span class="fw-bold"><?php echo htmlspecialchars($i['product_name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo CURRENCY . $i['price']; ?></td>
                                        <td>x<?php echo $i['quantity']; ?></td>
                                        <td class="text-end pe-4 fw-bold"><?php echo CURRENCY . number_format($i['price'] * $i['quantity'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 px-4">
                            <h5 class="fw-bold mb-0">Customer Info</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><i class="fas fa-user text-muted me-2"></i> <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                            <p class="mb-1"><i class="fas fa-envelope text-muted me-2"></i> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                            <?php if(!empty($order['phone'])): ?>
                            <p class="mb-1"><i class="fas fa-phone text-muted me-2"></i> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <?php endif; ?>
                            <hr>
                            <p class="mb-1 fw-bold">Shipping Address:</p>
                            <p class="text-muted small mb-0"><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                        <div class="card-body p-4">
                            <p class="d-flex justify-content-between mb-2 opacity-75">
                                <span>Status</span>
                                <span class="badge bg-light text-primary"><?php echo ucfirst($order['status']); ?></span>
                            </p>
                            <p class="d-flex justify-content-between mb-2 opacity-75">
                                <span>Payment Method</span>
                                <span><?php echo $order['payment_method']; ?></span>
                            </p>
                            <hr class="border-white opacity-25">
                            <div class="mt-4">
                                <form action="admin_actions.php" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="redirect" value="view_order.php?id=<?php echo $order['id']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-select rounded-3">
                                        <?php foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo $order['status'] == $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-light text-primary fw-bold rounded-3 px-3">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </div>
                            <p class="d-flex justify-content-between mb-0 fs-5 fw-bold">
                                <span>Total Paid</span>
                                <span><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
