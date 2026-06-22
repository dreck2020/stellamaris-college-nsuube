<?php
// config/database.php
class Database {
    private $host = "sql202.infinityfree.com";
    private $db_name = "if0_41586402_stella_maris_db";
    private $username = "if0_41586402";
    private $password = "YqLsW6xicwr";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Silent fail - no error message shown to users
            $this->conn = null;
        }
        return $this->conn;
    }
}
?>