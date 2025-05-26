<?php
require_once "config.php";

class Wyszukiwarka {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function znajdzPolaczenia($stacja_start, $stacja_koniec, $data, $godzina, $typy_pociagow) {
        // Jeśli użytkownik nie zaznaczył żadnego typu pociągu, zwracamy pustą tablicę
        if (empty($typy_pociagow)) {
            return [];
        }
    
        // Tworzymy placeholdery dla typów pociągów (np. ?, ?, ?)
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
                    AND ? BETWEEN p.od AND p.do  -- Sprawdzanie, czy data jest w zakresie
                    AND p.typ IN ($placeholders)  
                  ORDER BY r1.godzina_odjazdu ASC";
    
        $stmt = $this->conn->prepare($query);
    
        // Bindowanie podstawowych parametrów
        $stmt->bindValue(1, $stacja_start, PDO::PARAM_INT);
        $stmt->bindValue(2, $stacja_koniec, PDO::PARAM_INT);
        $stmt->bindValue(3, $godzina, PDO::PARAM_STR);
        $stmt->bindValue(4, $data, PDO::PARAM_STR);
    
        // Bindowanie dynamicznych typów pociągów
        foreach ($typy_pociagow as $index => $typ) {
            $stmt->bindValue($index + 5, $typ, PDO::PARAM_STR);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}
?>
