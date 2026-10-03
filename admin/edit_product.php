<?php
require_once '../includes/header.php';

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: products.php");
    exit();
}

$product = $productModel->getProductById($id);

if (!$product) {
    header("Location: products.php");
    exit();
}

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
                <h2 class="fw-bold mb-0">Edit Product: #<?php echo $product['id']; ?></h2>
                <a href="products.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="admin_actions.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_product">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Author</label>
                                    <select name="author_id" class="form-select rounded-3">
                                        <option value="">-- None --</option>
                                        <?php foreach($authors as $auth): ?>
                                            <option value="<?php echo $auth['id']; ?>" <?php echo ($auth['id'] == $product['author_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($auth['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Category</label>
                                    <select name="category_id" class="form-select rounded-3" required>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">ISBN</label>
                                    <input type="text" name="isbn" class="form-control rounded-3" value="<?php echo htmlspecialchars($product['isbn'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Publisher</label>
                                    <input type="text" name="publisher" class="form-control rounded-3" value="<?php echo htmlspecialchars($product['publisher'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Publication Date</label>
                                    <input type="date" name="publication_date" class="form-control rounded-3" value="<?php echo htmlspecialchars($product['publication_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Price (<?php echo CURRENCY; ?>)</label>
                                    <input type="number" name="price" step="0.01" class="form-control rounded-3" value="<?php echo $product['price']; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Discount (<?php echo CURRENCY; ?>)</label>
                                    <input type="number" name="discount" step="0.01" class="form-control rounded-3" value="<?php echo $product['discount']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Stock Quantity</label>
                                    <input type="number" name="stock" class="form-control rounded-3" value="<?php echo $product['stock']; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="description" class="form-control rounded-3" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Update Product Image</label>
                            <div class="mb-3">
                                <img src="../assets/images/<?php echo $product['image']; ?>" class="rounded-4 w-100 shadow-sm mb-3" onerror="this.src='https://via.placeholder.com/300x400?text=Book'">
                                <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                                <small class="text-muted d-block mt-2">Select a new photo to update the product image.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow">Update Product <i class="fas fa-save ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
