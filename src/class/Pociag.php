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
}
?>
