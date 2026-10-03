<?php
require_once '../core/config.php';
require_once '../core/classes/Database.php';
require_once '../core/classes/User.php';
require_once '../core/classes/Product.php';
require_once '../core/classes/Order.php';

$db = new Database();
$userModel = new User($db);
$productModel = new Product($db);
$orderModel = new Order($db);

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Function to handle image upload
    function uploadImage($file) {
        $target_dir = "../assets/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($file["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $file_name;
        }
        return 'book_default.jpg';
    }

    if ($action === 'add_product') {
        $name = $_POST['name'];
        $isbn = $_POST['isbn'] ?? null;
        $publisher = $_POST['publisher'] ?? null;
        $publication_date = !empty($_POST['publication_date']) ? $_POST['publication_date'] : null;
        $price = $_POST['price'];
        $discount = $_POST['discount'] !== '' ? $_POST['discount'] : 0;
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];
        $author_id = !empty($_POST['author_id']) ? $_POST['author_id'] : null;
        $description = $_POST['description'];

        $image = 'book_default.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = uploadImage($_FILES['image']);
        }

        $stmt = $db->query("INSERT INTO products (name, isbn, publisher, publication_date, price, discount, stock, category_id, author_id, description, image) VALUES (:name, :isbn, :publisher, :publication_date, :price, :discount, :stock, :category_id, :author_id, :description, :image)");
        $stmt->execute([
            ':name' => $name,
            ':isbn' => $isbn,
            ':publisher' => $publisher,
            ':publication_date' => $publication_date,
            ':price' => $price,
            ':discount' => $discount,
            ':stock' => $stock,
            ':category_id' => $category_id,
            ':author_id' => $author_id,
            ':description' => $description,
            ':image' => $image
        ]);
        header("Location: products.php?success=added");
        exit();
    }

    if ($action === 'edit_product') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $isbn = $_POST['isbn'] ?? null;
        $publisher = $_POST['publisher'] ?? null;
        $publication_date = !empty($_POST['publication_date']) ? $_POST['publication_date'] : null;
        $price = $_POST['price'];
        $discount = $_POST['discount'] !== '' ? $_POST['discount'] : 0;
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];
        $author_id = !empty($_POST['author_id']) ? $_POST['author_id'] : null;
        $description = $_POST['description'];

        $sql = "UPDATE products SET name = :name, isbn = :isbn, publisher = :publisher, publication_date = :publication_date, price = :price, discount = :discount, stock = :stock, category_id = :category_id, author_id = :author_id, description = :description";
        $params = [
            ':name' => $name,
            ':isbn' => $isbn,
            ':publisher' => $publisher,
            ':publication_date' => $publication_date,
            ':price' => $price,
            ':discount' => $discount,
            ':stock' => $stock,
            ':category_id' => $category_id,
            ':author_id' => $author_id,
            ':description' => $description,
            ':id' => $id
        ];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = uploadImage($_FILES['image']);
            $sql .= ", image = :image";
            $params[':image'] = $image;
        }

        $sql .= " WHERE id = :id";
        $stmt = $db->query($sql);
        $stmt->execute($params);
        header("Location: products.php?success=updated");
        exit();
    }

    if ($action === 'delete_product') {
        $id = $_POST['id'];
        $stmt = $db->query("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header("Location: products.php?success=deleted");
        exit();
    }

    if ($action === 'toggle_status') {
        $id = $_POST['id'];
        $stmt = $db->query("UPDATE products SET status = IF(status = 'active', 'inactive', 'active') WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header("Location: products.php?success=updated");
        exit();
    }

    if ($action === 'update_status') {
        $id = $_POST['id'];
        $status = $_POST['status'] ?? 'pending';
        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (in_array($status, $allowed)) {
            $orderModel->updateStatus($id, $status);
        }
        $redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : 'orders.php';
        $separator = strpos($redirect, '?') !== false ? '&' : '?';
        header("Location: $redirect{$separator}success=status_updated");
        exit();
    }

    // Legacy 2-step actions kept for compatibility, now just delegate to updateStatus
    if ($action === 'confirm_order') {
        $orderModel->updateStatus($_POST['id'], 'processing');
        header("Location: orders.php?success=confirmed");
        exit();
    }

    if ($action === 'complete_order') {
        $orderModel->updateStatus($_POST['id'], 'delivered');
        header("Location: orders.php?success=completed");
        exit();
    }

    // Categories
    if ($action === 'add_category') {
        $productModel->addCategory($_POST['name'], $_POST['description'] ?? '');
        header("Location: categories.php?success=added");
        exit();
    }

    if ($action === 'edit_category') {
        $productModel->updateCategory($_POST['id'], $_POST['name'], $_POST['description'] ?? '');
        header("Location: categories.php?success=updated");
        exit();
    }

    if ($action === 'delete_category') {
        $productModel->deleteCategory($_POST['id']);
        header("Location: categories.php?success=deleted");
        exit();
    }

    // Authors
    if ($action === 'add_author') {
        $productModel->addAuthor($_POST['name'], $_POST['bio'] ?? '');
        header("Location: authors.php?success=added");
        exit();
    }

    if ($action === 'edit_author') {
        $productModel->updateAuthor($_POST['id'], $_POST['name'], $_POST['bio'] ?? '');
        header("Location: authors.php?success=updated");
        exit();
    }

    if ($action === 'delete_author') {
        $productModel->deleteAuthor($_POST['id']);
        header("Location: authors.php?success=deleted");
        exit();
    }

    // Users
    if ($action === 'edit_user_role') {
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        $userModel->updateRole($_POST['id'], $role);
        header("Location: users.php?success=updated");
        exit();
    }

    if ($action === 'delete_user') {
        $userModel->deleteUser($_POST['id']);
        header("Location: users.php?success=deleted");
        exit();
    }

    // Reviews
    if ($action === 'delete_review') {
        $productModel->deleteReview($_POST['id']);
        header("Location: reviews.php?success=deleted");
        exit();
    }
}

header("Location: dashboard.php");
exit();
?>
