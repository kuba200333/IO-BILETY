<?php
class Znizka {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function pobierzZnizki() {
        $query = "SELECT id_znizki, nazwa_znizki, wymiar_znizki FROM znizki ORDER BY wymiar_znizki ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
