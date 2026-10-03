<?php
class Cart {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addToCart($user_id, $product_id, $qty = 1) {
        $stmt = $this->db->query("SELECT * FROM carts WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $new_qty = $item['quantity'] + $qty;
            $stmt = $this->db->query("UPDATE carts SET quantity = :new_qty WHERE id = :id");
            $stmt->bindParam(':new_qty', $new_qty);
            $stmt->bindParam(':id', $item['id']);
            return $stmt->execute();
        } else {
            $stmt = $this->db->query("INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :qty)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':qty', $qty);
            return $stmt->execute();
        }
    }

    public function getCartItems($user_id) {
        $stmt = $this->db->query("SELECT c.*, p.name, p.price, p.discount, p.image, p.stock FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeFromCart($id) {
        $stmt = $this->db->query("DELETE FROM carts WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function clearCart($user_id) {
        $stmt = $this->db->query("DELETE FROM carts WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function updateQuantity($id, $qty_change) {
        $stmt = $this->db->query("SELECT quantity FROM carts WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $new_qty = $item['quantity'] + $qty_change;
            if ($new_qty <= 0) {
                return $this->removeFromCart($id);
            } else {
                $stmt = $this->db->query("UPDATE carts SET quantity = :new_qty WHERE id = :id");
                $stmt->bindParam(':new_qty', $new_qty);
                $stmt->bindParam(':id', $id);
                return $stmt->execute();
            }
        }
        return false;
    }
}
?>
