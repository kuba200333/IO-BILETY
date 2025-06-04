<?php
class Stacje {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function pobierzWszystkie(): array {
        $query = "SELECT id_stacji, nazwa FROM stacje ORDER BY nazwa ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>  
