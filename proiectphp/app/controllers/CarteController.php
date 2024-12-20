<?php
require_once __DIR__ . '/../../config/db.php';

class CarteController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getCarti($gen = null, $an = null, $disponibilitate = null)
    {
        $sql = "SELECT * FROM Carte WHERE 1=1";
        $params = [];

        if ($gen) {
            $sql .= " AND gen = :gen";
            $params[':gen'] = $gen;
        }
        if ($an) {
            $sql .= " AND an = :an";
            $params[':an'] = $an;
        }
        if ($disponibilitate !== null && $disponibilitate !== '') {
            $sql .= " AND disponibilitate = :disponibilitate";
            $params[':disponibilitate'] = $disponibilitate;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBooks() {
        $query = "SELECT * FROM Carte";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addBook($titlu, $autor, $gen, $an, $disponibilitate) {
        
        var_dump($titlu, $autor, $gen, $an, $disponibilitate);
        
        $query = "INSERT INTO Carte (titlu, autor, gen, an, disponibilitate) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$titlu, $autor, $gen, $an, $disponibilitate]);
    }

    /*public function updateBook($id_carte, $titlu, $autor, $gen, $an, $disponibilitate) {
        $query = "UPDATE Carte SET titlu = ?, autor = ?, gen = ?, an = ?, disponibilitate = ? WHERE id_carte = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$titlu, $autor, $gen, $an, $disponibilitate, $id_carte]);
    }*/

    public function deleteBook($id_carte) {
        $query = "DELETE FROM Carte WHERE id_carte = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_carte]);
    }

    public function updateBook($id, $titlu, $autor, $gen, $an, $disponibilitate) {
        $stmt = $this->conn->prepare("UPDATE Carte SET titlu = ?, autor = ?, gen = ?, an = ?, disponibilitate = ? WHERE id_carte = ?");
        return $stmt->execute([$titlu, $autor, $gen, $an, $disponibilitate, $id]);
    }

    public function updateBookDisponibilitate($idCarte, $disponibilitate)
    {
        $query = "UPDATE Carte SET disponibilitate = :disponibilitate WHERE id_carte = :id_carte";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute([
                ':disponibilitate' => $disponibilitate,
                ':id_carte' => $idCarte,
            ]);
        } catch (PDOException $e) {
            die("Eroare SQL: " . $e->getMessage());
        }
    }
}
?>
