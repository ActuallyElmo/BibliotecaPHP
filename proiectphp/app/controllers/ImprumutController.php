<?php
require_once __DIR__ . '/../../config/db.php';

class ImprumutController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getImprumuturi() {
        $query = "SELECT * FROM Imprumut";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addImprumutTest($id_carte, $id_utilizator, $data_imprumut) {
        $query = "INSERT INTO Imprumut (id_carte, id_utilizator, data_imprumut) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_carte, $id_utilizator, $data_imprumut]);
    }

    public function returnBook($id_imprumut, $data_returnare) {
        $query = "UPDATE Imprumut SET data_returnare = ? WHERE id_imprumut = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data_returnare, $id_imprumut]);
    }

    public function deleteLoan($id_imprumut) {
        $query = "DELETE FROM Imprumut WHERE id_imprumut = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_imprumut]);
    }

    public function markAsReturned($idImprumut) {
        $query = "UPDATE Imprumut SET data_returnare = CURDATE() WHERE id_imprumut = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$idImprumut]);
    }

    public function addImprumut($idCarte, $idUtilizator, $dataImprumut)
    {
        $query = "INSERT INTO Imprumut (id_carte, id_utilizator, data_imprumut) VALUES (:id_carte, :id_utilizator, :data_imprumut)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id_carte' => $idCarte,
            ':id_utilizator' => $idUtilizator,
            ':data_imprumut' => $dataImprumut,
        ]);
    }

    public function getImprumuturiByUser($idUtilizator)
    {
        $query = "SELECT i.id_imprumut, c.id_carte, c.titlu, c.autor, c.gen, c.an, i.data_imprumut, i.data_returnare
                  FROM Imprumut i
                  JOIN Carte c ON i.id_carte = c.id_carte
                  WHERE i.id_utilizator = :id_utilizator";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id_utilizator' => $idUtilizator]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
