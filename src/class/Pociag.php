<?php
class Pociag {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function pobierzPociagi() {
        $query = "SELECT id_pociagu, numer_pociagu, typ, nazwa, od, do FROM pociagi";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
