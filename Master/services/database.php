<?php
require_once '../configs/secret.php';
require_once "../models/consultation.php";
require_once "../models/users.php";
// Database Connection
class Database {
    private $host = "yourDBhost";
    private $db_name = "yourDBname";
    private $username = "yourDBusername";
    private $password = DB_PASSWORD;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>