<?php
require_once "config.php";

class Pociag {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajPociag($numer_pociagu, $typ, $nazwa, $od, $do) {
        $query = "INSERT INTO pociagi (numer_pociagu, typ, nazwa, od, do) 
                  VALUES (:numer_pociagu, :typ, :nazwa, :od, :do)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":numer_pociagu" => $numer_pociagu,
            ":typ" => $typ,
            ":nazwa" => $nazwa,
            ":od" => $od,
            ":do" => $do
        ]);
    }
}
?>
