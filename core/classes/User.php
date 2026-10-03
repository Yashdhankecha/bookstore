<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($name, $email, $password) {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->query("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function login($email, $password) {
        $stmt = $this->db->query("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function isAdmin() {
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->db->query("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $name, $email, $address) {
        $stmt = $this->db->query("UPDATE users SET name = :name, email = :email, address = :address WHERE id = :id");
        return $stmt->execute([':name' => $name, ':email' => $email, ':address' => $address, ':id' => $id]);
    }

    public function changePassword($id, $current_password, $new_password) {
        $user = $this->getUserById($id);
        if (!$user || !password_verify($current_password, $user['password'])) {
            return false;
        }
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->db->query("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hashed, ':id' => $id]);
    }

    public function updateRole($id, $role) {
        $stmt = $this->db->query("UPDATE users SET role = :role WHERE id = :id");
        return $stmt->execute([':role' => $role, ':id' => $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->db->query("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
