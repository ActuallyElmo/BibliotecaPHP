<?php
class Database {
    private $host = "localhost";
    private $username = "root";  // sau alt nume de utilizator (exemplu: "384904")
    private $password = "";      // sau parola corespunzătoare
    private $db_name = "biblioteca"; // numele bazei de date
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
