<?php
class SkanowanieBiletow {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Zapisuje skanowanie biletu przez pracownika.
     * @param int $id_biletu
     * @param int $id_pracownika
     * @return bool - true jeśli zapisano poprawnie, false w przeciwnym wypadku
     */
    public function zapiszSkanowanie(int $id_biletu, int $id_pracownika): bool {
        $query = "INSERT INTO skanowania_biletow (id_biletu, id_pracownika, data_skanowania) 
                  VALUES (:id_biletu, :id_pracownika, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
        $stmt->bindParam(":id_pracownika", $id_pracownika, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getStatystykiPracownikow(string $data): array {
    $query = "SELECT CONCAT(p.nazwisko, ' ', p.imie) AS pracownik, 
                     COUNT(sb.id_biletu) AS ilosc_zeskanowanych
              FROM pracownicy p
              LEFT JOIN skanowania_biletow sb ON sb.id_pracownika = p.id_pracownika
                  AND DATE(sb.data_skanowania) = :data
              WHERE p.stanowisko = 'Pracownik'
              GROUP BY pracownik
              ORDER BY ilosc_zeskanowanych DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':data', $data);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>
