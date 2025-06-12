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

    public function obliczCene($odleglosc, $klasa, $znizka, $data_podrozy) {
    $query = "SELECT cena FROM ceny_odleglosci WHERE :odleglosc BETWEEN odleglosc_min AND odleglosc_max AND klasa = :klasa";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":odleglosc", $odleglosc, PDO::PARAM_INT);
    $stmt->bindParam(":klasa", $klasa, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) return [0, 0];

    $cena_podstawowa = $row["cena"];

    if ($klasa === "sypialny") {
        return [$cena_podstawowa + 79.00, 0]; // bez promocji
    }

    $dzisiaj = new DateTime();
    $data_podrozy_dt = new DateTime($data_podrozy);
    $roznica = $dzisiaj->diff($data_podrozy_dt)->days;
    $czy_przyszlosc = $data_podrozy_dt > $dzisiaj;

    $promo = 0;
    if ($czy_przyszlosc) {
        if ($roznica >= 21) {
            $promo = 30;
        } elseif ($roznica >= 14) {
            $promo = 20;
        } elseif ($roznica >= 7) {
            $promo = 10;
        }
    }

    $laczna_znizka = $znizka + $promo;
    if ($laczna_znizka > 90) $laczna_znizka = 90;

    $cena_koncowa = round($cena_podstawowa * (1 - ($laczna_znizka / 100)), 2);
    return [$cena_koncowa, $promo];
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
    public function pobierzSzczegoly($kod) {
        $query = "SELECT bilety.id_biletu, bilety.kod_qr, pociagi.numer_pociagu,
                        st1.nazwa AS stacja_start, st2.nazwa AS stacja_koniec,
                        bilety.miejsce, bilety.cena, bilety.data_podrozy, bilety.id_wagonu, bilety.id_znizki,
                        wagony.klasa,
                        pasazerowie.imie, pasazerowie.nazwisko, transakcje.data_transakcji,
                        znizki.nazwa_znizki, znizki.wymiar_znizki
                FROM bilety
                JOIN pociagi ON bilety.id_pociagu = pociagi.id_pociagu
                JOIN stacje st1 ON bilety.id_stacji_start = st1.id_stacji
                JOIN stacje st2 ON bilety.id_stacji_koniec = st2.id_stacji
                JOIN pasazerowie ON bilety.id_pasazera = pasazerowie.id_pasazera
                JOIN transakcje ON bilety.id_biletu = transakcje.id_biletu
                JOIN znizki ON bilety.id_znizki = znizki.id_znizki
                JOIN wagony ON bilety.id_wagonu = wagony.id_wagonu
                WHERE bilety.kod_qr = :kod OR bilety.id_biletu = :kod_int";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":kod", $kod, PDO::PARAM_STR);
        $stmt->bindValue(":kod_int", (int)$kod, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBiletById($id_biletu) {
        $query = "SELECT b.*, p.numer_pociagu, ss.nazwa AS stacja_start, sk.nazwa AS stacja_koniec, z.nazwa_znizki AS nazwa_znizki, z.wymiar_znizki, pas.imie, pas.nazwisko, t.data_transakcji, w.klasa FROM bilety b JOIN pociagi p ON b.id_pociagu = p.id_pociagu JOIN stacje ss ON b.id_stacji_start = ss.id_stacji JOIN stacje sk ON b.id_stacji_koniec = sk.id_stacji LEFT JOIN znizki z ON b.id_znizki = z.id_znizki LEFT JOIN pasazerowie pas ON b.id_pasazera = pas.id_pasazera JOIN transakcje t on t.id_biletu=b.id_biletu JOIN wagony w on w.id_wagonu=b.id_wagonu
                WHERE b.id_biletu = :id_biletu";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_biletu', $id_biletu, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
