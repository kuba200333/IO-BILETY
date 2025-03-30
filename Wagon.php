<?php
require_once "config.php";

class Wagon {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajWagon($id_skladu, $numer_wagonu, $typ, $klasa, $liczba_miejsc, $miejsce_od, $miejsce_do) {
        $query = "INSERT INTO wagony (id_skladu, numer_wagonu, typ, klasa, liczba_miejsc, miejsce_od, miejsce_do) 
                  VALUES (:id_skladu, :numer_wagonu, :typ, :klasa, :liczba_miejsc, :miejsce_od, :miejsce_do)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":id_skladu" => $id_skladu,
            ":numer_wagonu" => $numer_wagonu,
            ":typ" => $typ,
            ":klasa" => $klasa,
            ":liczba_miejsc" => $liczba_miejsc,
            ":miejsce_od" => $miejsce_od,
            ":miejsce_do" => $miejsce_do
        ]);
    }

    public function pobierzSklady() {
        $query = "SELECT id_skladu, nazwa_skladu FROM sklady_pociagow";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
