<?php
class Order {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrder($user_id, $total_amount, $address, $phone, $payment_method, $items) {
        try {
            $stmt = $this->db->query("INSERT INTO orders (user_id, total_amount, address, phone, payment_method) VALUES (:user_id, :total, :address, :phone, :payment)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':total', $total_amount);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':payment', $payment_method);
            $stmt->execute();
            
            $order_id = $this->db->lastInsertId();

            foreach ($items as $item) {
                $stmt = $this->db->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :qty, :price)");
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':product_id', $item['product_id']);
                $stmt->bindParam(':qty', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();

                // Reduce stock
                $stmt = $this->db->query("UPDATE products SET stock = stock - :qty WHERE id = :product_id");
                $stmt->bindParam(':qty', $item['quantity']);
                $stmt->bindParam(':product_id', $item['product_id']);
                $stmt->execute();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAllOrders() {
        $stmt = $this->db->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserOrders($user_id) {
        $stmt = $this->db->query("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($order_id) {
        $stmt = $this->db->query("SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = :oid");
        $stmt->bindParam(':oid', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($order_id) {
        $stmt = $this->db->query("SELECT oi.*, p.name as product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :oid");
        $stmt->bindParam(':oid', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($order_id, $status) {
        $stmt = $this->db->query("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $order_id]);
    }

    public function getRevenueBetween($start, $end) {
        $stmt = $this->db->query("SELECT COUNT(*) as order_count, COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE DATE(created_at) BETWEEN :start AND :end AND status != 'cancelled'");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts($start, $end, $limit = 10) {
        $limit = (int)$limit;
        $stmt = $this->db->query("SELECT p.id, p.name, p.image, SUM(oi.quantity) as total_qty, SUM(oi.price * oi.quantity) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE DATE(o.created_at) BETWEEN :start AND :end AND o.status != 'cancelled'
            GROUP BY p.id, p.name, p.image
            ORDER BY total_qty DESC
            LIMIT $limit");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
