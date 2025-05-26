<?php
require_once "config.php";

class Sklad {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajSklad($id_pociagu, $nazwa_skladu) {
        $query = "INSERT INTO sklady_pociagow (id_pociagu, nazwa_skladu) VALUES (:id_pociagu, :nazwa_skladu)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":id_pociagu" => $id_pociagu,
            ":nazwa_skladu" => $nazwa_skladu
        ]);
    }

    public function pobierzPociagi() {
        $query = "SELECT id_pociagu, numer_pociagu FROM pociagi";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pobierzSklady() {
        $query = "SELECT id_skladu, nazwa_skladu FROM sklady_pociagow";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
