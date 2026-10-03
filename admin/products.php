<?php 
require_once '../includes/header.php'; 

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

$allProducts = $productModel->getAllProducts();
$categories = $productModel->getCategories();
$authors = $productModel->getAuthors();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0">Product Management</h2>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-1"></i> Add New Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
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
                                <td><?php echo htmlspecialchars($p['category_name'] ?: 'General'); ?></td>
                                <td class="fw-bold"><?php echo CURRENCY; ?><?php echo number_format($p['price'], 2); ?></td>
                                <td>
                                    <?php if($p['stock'] > 10): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2"><?php echo $p['stock']; ?> In Stock</span>
                                    <?php elseif($p['stock'] > 0): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2"><?php echo $p['stock']; ?> Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="admin_actions.php" method="POST" class="m-0">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Click to toggle">
                                            <?php if($p['status'] === 'active'): ?>
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">Inactive</span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                        <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border-0 py-2 px-3"><i class="fas fa-edit text-primary"></i></a>
                                        <form action="admin_actions.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display:inline;">
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

<!-- Simple Add Product Modal (UI Only for now) -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin_actions.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_product">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product Name</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Enter name" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">Author</label>
                            <select name="author_id" class="form-select rounded-3">
                                <option value="">-- None --</option>
                                <?php foreach($authors as $auth): ?>
                                    <option value="<?php echo $auth['id']; ?>"><?php echo htmlspecialchars($auth['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category_id" class="form-select rounded-3" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">ISBN</label>
                            <input type="text" name="isbn" class="form-control rounded-3" placeholder="978-...">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Publisher</label>
                            <input type="text" name="publisher" class="form-control rounded-3" placeholder="Publisher name">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">Publication Date</label>
                            <input type="date" name="publication_date" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">Price (<?php echo CURRENCY; ?>)</label>
                            <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Discount (<?php echo CURRENCY; ?>)</label>
                            <input type="number" step="0.01" name="discount" class="form-control rounded-3" placeholder="0.00" value="0">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Stock</label>
                            <input type="number" name="stock" class="form-control rounded-3" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product Photo</label>
                        <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
