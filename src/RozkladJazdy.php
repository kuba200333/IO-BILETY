<?php
class RozkladJazdy {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getGodzinaOdjazdu(int $id_biletu): string {
        $query = "SELECT godzina_odjazdu FROM rozklad_jazdy 
                  WHERE id_pociagu = (SELECT id_pociagu FROM bilety WHERE id_biletu = :id_biletu)
                    AND id_stacji = (SELECT id_stacji_start FROM bilety WHERE id_biletu = :id_biletu)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['godzina_odjazdu'] : "Brak danych";
    }
}
?>
