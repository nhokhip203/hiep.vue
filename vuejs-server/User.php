<?php
class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả người dùng
    public function getUsers()
    {
        $query = "SELECT id, name, email, role, note FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy người dùng theo ID
    public function getUserById($id)
    {
        $query = "SELECT id, name, email, role, note FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm người dùng mới
    public function createUser($name, $email, $password, $role, $note)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (name, email, password, role, note) VALUES (:name, :email, :password, :role, :note)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword); // Mã hóa mật khẩu
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':note', $note);
        return $stmt->execute();
    }

    // Cập nhật người dùng
    public function updateUser($id, $name, $email, $password, $role, $note)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email, password = :password, role = :role, note = :note WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword); // Mã hóa mật khẩu
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':note', $note);
        return $stmt->execute();
    }

    // Xóa người dùng
    public function deleteUser($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }


    public function checkLogin($email, $password)
    {
        $sql = "SELECT * FROM `users` WHERE `email` = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $check = $stmt->execute();
        if (!$check) {
            return false;
        }
        $userLogin = $stmt->fetch();
        if (!$userLogin) {
            return false;
        }

        if (password_verify($password, $userLogin['password'])) {
            return $userLogin;
        } else {
            return false;
        }
    }
}
