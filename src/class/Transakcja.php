<?php

class Transakcja {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajTransakcje($id_biletu, $kwota, $metoda_platnosci, $id_pasazera = null) {
        if ($id_pasazera !== null) {
            $query_transakcja = "INSERT INTO transakcje (id_biletu, id_pasazera, kwota, metoda_platnosci, status, data_transakcji) 
                                 VALUES (:id_biletu, :id_pasazera, :kwota, :metoda_platnosci, 'Zakończona', NOW())";
            $stmt = $this->conn->prepare($query_transakcja);
            $stmt->bindParam(":id_pasazera", $id_pasazera, PDO::PARAM_INT);
        } else {
            $query_transakcja = "INSERT INTO transakcje (id_biletu, kwota, metoda_platnosci, status, data_transakcji) 
                                 VALUES (:id_biletu, :kwota, :metoda_platnosci, 'Zrealizowana', NOW())";
            $stmt = $this->conn->prepare($query_transakcja);
        }
        
        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
        $stmt->bindParam(":kwota", $kwota, PDO::PARAM_STR);
        $stmt->bindParam(":metoda_platnosci", $metoda_platnosci, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}

?>