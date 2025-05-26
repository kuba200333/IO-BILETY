<?php
require_once "config.php";

class Rozklad {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajRozklad($id_pociagu, $id_stacji, $godzina_przyjazdu, $godzina_odjazdu) {
        $query = "INSERT INTO rozklad_jazdy (id_pociagu, id_stacji, godzina_przyjazdu, godzina_odjazdu) 
                  VALUES (:id_pociagu, :id_stacji, :godzina_przyjazdu, :godzina_odjazdu)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":id_pociagu" => $id_pociagu,
            ":id_stacji" => $id_stacji,
            ":godzina_przyjazdu" => $godzina_przyjazdu,
            ":godzina_odjazdu" => $godzina_odjazdu
        ]);
    }

    public function pobierzPociagi() {
        $query = "SELECT id_pociagu, numer_pociagu, nazwa FROM pociagi";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pobierzStacje() {
        $query = "SELECT id_stacji, nazwa FROM stacje";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
