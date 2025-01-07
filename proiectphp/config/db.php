<?php
class Database {
    private $host = "localhost";
    private $username = "root"; 
    private $password = "";     
    private $db_name = "biblioteca"; 
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Eroare la conectare: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
