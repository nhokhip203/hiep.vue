<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'vuejs';
    private $username = 'root';
    private $password = '';
    private $port = '3306';
    private $charset = 'utf8';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=$this->host;dbname=$this->db_name;port=$this->port;charset=$this->charset";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Kết nối thất bại: " . $e->getMessage();
        }

        return $this->conn;
    }
}