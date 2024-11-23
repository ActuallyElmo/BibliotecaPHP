<?php
require_once __DIR__ . '/../../config/db.php';

class UtilizatorController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllUsers() {
        $query = "SELECT * FROM Utilizator";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUser($nume, $prenume, $email, $status_membru, $parola) {
        $query = "INSERT INTO Utilizator (nume, prenume, email, status_membru, parola) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($parola, PASSWORD_BCRYPT);
        return $stmt->execute([$nume, $prenume, $email, $status_membru, $hashedPassword]);
    }

    public function updateUser($id_utilizator, $nume, $prenume, $email, $status_membru) {
        $query = "UPDATE Utilizator SET nume = ?, prenume = ?, email = ?, status_membru = ? WHERE id_utilizator = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nume, $prenume, $email, $status_membru, $id_utilizator]);
    }

    public function deleteUser($id_utilizator) {
        $query = "DELETE FROM Utilizator WHERE id_utilizator = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_utilizator]);
    }

    public function getUserByEmail($email)
    {
        try {
            $sql = "SELECT * FROM utilizator WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC); // returnam datele
            } else {
                return null; // Daca nu exista
            }
        } catch (PDOException $e) {
            die("Eroare la interogare: " . $e->getMessage());
        }
    }
}
?>
