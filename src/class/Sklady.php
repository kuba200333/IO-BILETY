<?php
class Sklady {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajSklad($id_skladu, $id_pociagu, $nazwa_skladu) {
        $query = "INSERT INTO sklady_pociagow (id_skladu, id_pociagu, nazwa_skladu) 
                  VALUES (:id_skladu, :id_pociagu, :nazwa_skladu)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_skladu", $id_skladu);
        $stmt->bindParam(":id_pociagu", $id_pociagu);
        $stmt->bindParam(":nazwa_skladu", $nazwa_skladu);
        return $stmt->execute();
    }
}
?>
