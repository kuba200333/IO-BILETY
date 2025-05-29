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

    public function znajdzPolaczenia($stacja_start, $stacja_koniec, $data, $godzina, $typy_pociagow) {
        if (empty($typy_pociagow)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($typy_pociagow), '?'));

        $query = "SELECT 
                    p.numer_pociagu, 
                    p.typ, 
                    s1.nazwa AS stacja_pocz, 
                    s2.nazwa AS stacja_konc,
                    r1.godzina_odjazdu AS godzina_wyjazdu,
                    p.nazwa
                  FROM pociagi p
                  JOIN rozklad_jazdy r1 ON p.id_pociagu = r1.id_pociagu
                  JOIN rozklad_jazdy r2 ON p.id_pociagu = r2.id_pociagu
                  JOIN stacje s1 ON r1.id_stacji = s1.id_stacji
                  JOIN stacje s2 ON r2.id_stacji = s2.id_stacji
                  WHERE r1.id_stacji = ? 
                    AND r2.id_stacji = ? 
                    AND r1.godzina_odjazdu >= ? 
                    AND ? BETWEEN p.od AND p.do
                    AND p.typ IN ($placeholders)
                  ORDER BY r1.godzina_odjazdu ASC";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $stacja_start, PDO::PARAM_INT);
        $stmt->bindValue(2, $stacja_koniec, PDO::PARAM_INT);
        $stmt->bindValue(3, $godzina, PDO::PARAM_STR);
        $stmt->bindValue(4, $data, PDO::PARAM_STR);

        foreach ($typy_pociagow as $index => $typ) {
            $stmt->bindValue($index + 5, $typ, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
