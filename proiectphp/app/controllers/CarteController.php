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
        
       // var_dump($titlu, $autor, $gen, $an, $disponibilitate);
        
        $query = "INSERT INTO Carte (titlu, autor, gen, an, disponibilitate) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$titlu, $autor, $gen, $an, $disponibilitate]);
    }

    //test
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

    //vechi
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

    public function getBookDisponibilitate($idCarte)
    {
        $query = "SELECT disponibilitate FROM carti WHERE id_carte = :idCarte";
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([
                ':idCarte' => $idCarte,
            ]);
        } catch (PDOException $e) {
            die("Eroare SQL: " . $e->getMessage());
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['disponibilitate'];
        }
        return null;
    }

    public function imprumutaCarte($idCarte, $idUtilizator)
    {
        try {
            //tranzactie pentru a evita 2 requesturi simultane
            $this->conn->beginTransaction();

            $queryCheck = "SELECT disponibilitate FROM carte WHERE id_carte = :idCarte FOR UPDATE";
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->execute([':idCarte' => $idCarte]);
            
            $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $this->conn->rollBack();
                return "Cartea nu exista.";
            }

            if (!$result['disponibilitate']) {
                $this->conn->rollBack();
                return "Cartea nu este disponibila.";
            }

            $queryUpdate = "UPDATE carte SET disponibilitate = 0 WHERE id_carte = :idCarte";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([':idCarte' => $idCarte]);

            $queryInsert = "INSERT INTO imprumut (id_carte, id_utilizator, data_imprumut) 
                            VALUES (:idCarte, :idUtilizator, NOW())";
            $stmtInsert = $this->conn->prepare($queryInsert);
            $stmtInsert->execute([
                ':idCarte' => $idCarte,
                ':idUtilizator' => $idUtilizator,
            ]);

            $this->conn->commit();
            return "Cartea a fost imprumutata cu succes.";

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return "Eroare: " . $e->getMessage();
        }
    }

    public function getCarteById($idCarte) {
        $query = "SELECT titlu, autor FROM carte WHERE id_carte = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idCarte]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function importaCartiDinJSON($numeFisier) {
        $json = file_get_contents($numeFisier);
        if ($json === false) {
            echo "Eroare la citirea fisierului JSON.";
            return;
        }

        $carti = json_decode($json, true);
        if ($carti === null) {
            echo "Eroare la parsarea fisierului JSON.";
            return;
        }

        $query = "INSERT INTO Carte (titlu, autor, gen, an, disponibilitate) 
                  VALUES (:titlu, :autor, :gen, :an, 1)";
        $stmt = $this->conn->prepare($query);

        // Insereaza fiecare carte in baza de date
        foreach ($carti as $carte) {
            try {
                $stmt->execute([
                    ':titlu' => $carte['titlu'],
                    ':autor' => $carte['autor'],
                    ':gen' => $carte['gen'],
                    ':an' => $carte['an']
                ]);
            } catch (PDOException $e) {
                echo "Eroare la inserarea cartii: " . $e->getMessage();
            }
        }

        echo "Importul cartilor a fost realizat cu succes!";
    }

    public function getCartiDupaGen() {
        $query = "SELECT gen, COUNT(*) AS numar FROM carte GROUP BY gen";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exportToCSV() {
        $fileName = "carti.csv";
        $file = fopen($fileName, 'w');
    
        fputcsv($file, ['ID Carte', 'Titlu', 'Autor', 'Gen', 'An']);
    
        $query = "SELECT id_carte, titlu, autor, gen, an FROM Carte";
        $stmt = $this->conn->query($query);
        $carti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($carti as $carte) {
            fputcsv($file, $carte);
        }
    
        fclose($file);
        echo "Fisierul $fileName a fost generat cu succes!";
    }

    public function importFromCSV($filePath) {
        if (($file = fopen($filePath, 'r')) !== false) {
            fgetcsv($file);
    
            while (($data = fgetcsv($file)) !== false) {
                $query = "INSERT INTO Carte (titlu, autor, gen, an) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$data[1], $data[2], $data[3], $data[4]]);
            }
    
            fclose($file);
            echo "Importul din CSV a fost realizat cu succes!";
        } else {
            echo "Eroare la deschiderea fisierului CSV!";
        }
    }

}
?>
