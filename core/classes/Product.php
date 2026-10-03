<?php
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllProducts() {
        $stmt = $this->db->query("SELECT p.*, c.name as category_name, a.name as author_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN authors a ON p.author_id = a.id ORDER BY p.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->db->query("SELECT p.*, c.name as category_name, a.name as author_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN authors a ON p.author_id = a.id WHERE p.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->db->query("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name, $description, $image = null) {
        $stmt = $this->db->query("INSERT INTO categories (name, description, image) VALUES (:name, :description, :image)");
        return $stmt->execute([':name' => $name, ':description' => $description, ':image' => $image]);
    }

    public function updateCategory($id, $name, $description) {
        $stmt = $this->db->query("UPDATE categories SET name = :name, description = :description WHERE id = :id");
        return $stmt->execute([':name' => $name, ':description' => $description, ':id' => $id]);
    }

    public function deleteCategory($id) {
        $stmt = $this->db->query("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getProductsByCategory($cid) {
        $stmt = $this->db->query("SELECT p.*, c.name as category_name, a.name as author_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN authors a ON p.author_id = a.id WHERE p.category_id = :cid AND p.status = 'active'");
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Combined filter: category + author + search, all optional, all active only
    public function getFilteredProducts($category_id = null, $author_id = null, $search = '') {
        $sql = "SELECT p.*, c.name as category_name, a.name as author_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN authors a ON p.author_id = a.id WHERE p.status = 'active'";
        $params = [];

        if (!empty($category_id)) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        if (!empty($author_id)) {
            $sql .= " AND p.author_id = :author_id";
            $params[':author_id'] = $author_id;
        }
        if (!empty($search)) {
            $sql .= " AND p.name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Author Methods
    public function getAuthors() {
        $stmt = $this->db->query("SELECT * FROM authors ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAuthorById($id) {
        $stmt = $this->db->query("SELECT * FROM authors WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addAuthor($name, $bio) {
        $stmt = $this->db->query("INSERT INTO authors (name, bio) VALUES (:name, :bio)");
        return $stmt->execute([':name' => $name, ':bio' => $bio]);
    }

    public function updateAuthor($id, $name, $bio) {
        $stmt = $this->db->query("UPDATE authors SET name = :name, bio = :bio WHERE id = :id");
        return $stmt->execute([':name' => $name, ':bio' => $bio, ':id' => $id]);
    }

    public function deleteAuthor($id) {
        $stmt = $this->db->query("DELETE FROM authors WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Wishlist Methods
    public function addToWishlist($user_id, $product_id) {
        if(!$this->isInWishlist($user_id, $product_id)) {
            $stmt = $this->db->query("INSERT INTO wishlist (user_id, product_id) VALUES (:uid, :pid)");
            $stmt->bindParam(':uid', $user_id);
            $stmt->bindParam(':pid', $product_id);
            return $stmt->execute();
        }
        return false;
    }

    public function removeFromWishlist($user_id, $product_id) {
        $stmt = $this->db->query("DELETE FROM wishlist WHERE user_id = :uid AND product_id = :pid");
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':pid', $product_id);
        return $stmt->execute();
    }

    public function getWishlistItems($user_id) {
        $stmt = $this->db->query("SELECT p.*, w.id as wishlist_id FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = :uid ORDER BY w.id DESC");
        $stmt->bindParam(':uid', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isInWishlist($user_id, $product_id) {
        $stmt = $this->db->query("SELECT id FROM wishlist WHERE user_id = :uid AND product_id = :pid");
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':pid', $product_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Review Methods
    public function getAllReviews() {
        $stmt = $this->db->query("SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReviewsForProduct($product_id) {
        $stmt = $this->db->query("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :pid ORDER BY r.created_at DESC");
        $stmt->bindParam(':pid', $product_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($product_id) {
        $stmt = $this->db->query("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE product_id = :pid");
        $stmt->bindParam(':pid', $product_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userHasReviewed($user_id, $product_id) {
        $stmt = $this->db->query("SELECT id FROM reviews WHERE user_id = :uid AND product_id = :pid");
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':pid', $product_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function addReview($user_id, $product_id, $rating, $comment) {
        $stmt = $this->db->query("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (:uid, :pid, :rating, :comment)");
        return $stmt->execute([':uid' => $user_id, ':pid' => $product_id, ':rating' => $rating, ':comment' => $comment]);
    }

    public function deleteReview($review_id) {
        $stmt = $this->db->query("DELETE FROM reviews WHERE id = :id");
        return $stmt->execute([':id' => $review_id]);
    }
}
?>
