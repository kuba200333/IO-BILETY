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

    public function getIdStationByName($nazwa) {
        $query = "SELECT * FROM stacje WHERE nazwa = :nazwa"; 
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nazwa', $nazwa, PDO::PARAM_STR); 
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id_stacji'] : null; 
    }
}
?>  
