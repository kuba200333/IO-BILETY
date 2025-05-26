<?php
require_once "config.php";

class Bilet {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obliczOdleglosc($numer_pociagu, $stacja_start, $stacja_koniec) {
        $query = "SELECT SUM(o.odleglosc_km) AS laczna_odleglosc
                  FROM odleglosci_miedzy_stacjami o
                  JOIN stacje s1 ON o.id_stacji_poczatek = s1.id_stacji
                  JOIN stacje s2 ON o.id_stacji_koniec = s2.id_stacji
                  WHERE o.id_pociagu = (SELECT id_pociagu FROM pociagi WHERE numer_pociagu = :numer_pociagu)
                  AND s1.id_stacji >= (SELECT id_stacji FROM stacje WHERE nazwa = :stacja_start)
                  AND s2.id_stacji <= (SELECT id_stacji FROM stacje WHERE nazwa = :stacja_koniec)";


        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":numer_pociagu", $numer_pociagu, PDO::PARAM_STR);
        $stmt->bindParam(":stacja_start", $stacja_start, PDO::PARAM_STR);
        $stmt->bindParam(":stacja_koniec", $stacja_koniec, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row["laczna_odleglosc"] : 0;
    }

    public function obliczCene($odleglosc, $klasa, $znizka) {
        $query = "SELECT cena FROM ceny_odleglosci WHERE :odleglosc BETWEEN odleglosc_min AND odleglosc_max AND klasa = :klasa";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":odleglosc", $odleglosc, PDO::PARAM_INT);
        $stmt->bindParam(":klasa", $klasa, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return 0;
    
        $cena_podstawowa = $row["cena"];
    
        // Dopłata do klasy sypialnej (79 zł, bez zniżek)
        if ($klasa === "sypialny") {
            return $cena_podstawowa + 79.00;
        }
    
        return round($cena_podstawowa * (1 - ($znizka / 100)), 2);
    }
    

    public function zapiszBilet($id_pasazera, $numer_pociagu, $stacja_start, $stacja_koniec, $klasa, $znizka, $wagon, $miejsce) {
        $odleglosc = $this->obliczOdleglosc($numer_pociagu, $stacja_start, $stacja_koniec);
        $cena = $this->obliczCene($odleglosc, $znizka);
        $data_podrozy = date("Y-m-d");
        $kod_qr = md5(uniqid(rand(), true));

        $query = "INSERT INTO bilety (id_pasazera, id_pociagu, id_stacji_start, id_stacji_koniec, miejsce, cena, data_podrozy, kod_qr, id_wagonu) 
                  VALUES (:id_pasazera, :id_pociagu, :id_stacji_start, :id_stacji_koniec, :miejsce, :cena, :data_podrozy, :kod_qr, :id_wagonu)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":id_pasazera" => $id_pasazera,
            ":id_pociagu" => $numer_pociagu,
            ":id_stacji_start" => $stacja_start,
            ":id_stacji_koniec" => $stacja_koniec,
            ":miejsce" => $miejsce,
            ":cena" => $cena,
            ":data_podrozy" => $data_podrozy,
            ":kod_qr" => $kod_qr,
            ":id_wagonu" => $wagon
        ]);

        return $this->conn->lastInsertId();
    }

    public function zapiszTransakcje($id_biletu, $id_pasazera, $kwota, $metoda_platnosci) {
        $query = "INSERT INTO transakcje (id_biletu, id_pasazera, kwota, metoda_platnosci, status, data_transakcji) 
                  VALUES (:id_biletu, :id_pasazera, :kwota, :metoda_platnosci, 'Zrealizowana', NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":id_biletu" => $id_biletu,
            ":id_pasazera" => $id_pasazera,
            ":kwota" => $kwota,
            ":metoda_platnosci" => $metoda_platnosci
        ]);
    }
}
?>
