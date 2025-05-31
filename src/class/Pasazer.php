<?php
require_once "config.php";

class Pasazer {
    private $conn;
    private $table_name = "pasazerowie";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($imie, $nazwisko, $login, $haslo, $telefon, $email, $kod_pocztowy, $adres, $miejscowosc) {
        $hashed_password = password_hash($haslo, PASSWORD_DEFAULT);

        $query = "INSERT INTO pasazerowie (imie, nazwisko, login, haslo, telefon, email, kod_pocztowy, adres, miejscowosc) 
                  VALUES (:imie, :nazwisko, :login, :haslo, :telefon, :email, :kod_pocztowy, :adres, :miejscowosc)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":imie", $imie);
        $stmt->bindParam(":nazwisko", $nazwisko);
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":haslo", $hashed_password);
        $stmt->bindParam(":telefon", $telefon);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":kod_pocztowy", $kod_pocztowy);
        $stmt->bindParam(":adres", $adres);
        $stmt->bindParam(":miejscowosc", $miejscowosc);

        return $stmt->execute();
    }

    public function login($login, $haslo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE login = :login";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $login);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($haslo, $user["haslo"])) {
            session_start();
            $_SESSION["user"] = $user["login"];
            $_SESSION["role"] = "pasazer";
            return true;
        }
        return false;
    }

    public function loadByLogin($login) {
        $query = "SELECT * FROM pasazerowie WHERE login = :login";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $login);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id = $row['id_pasazera'];
            $this->imie = $row['imie'];
            $this->nazwisko = $row['nazwisko'];
            return true;
        }
        return false;
    }

    public function getBilety() {
        $query = "SELECT b.id_biletu, b.kod_qr, p.numer_pociagu, 
                        s1.nazwa AS stacja_start, s2.nazwa AS stacja_koniec, 
                        b.miejsce, b.cena, b.data_podrozy, b.id_wagonu, b.id_znizki,
                        w.klasa, w.numer_wagonu AS wagon,
                        pas.imie, pas.nazwisko, t.data_transakcji,
                        z.nazwa_znizki, z.wymiar_znizki,
						IF(zw.id_biletu IS NOT NULL, 'Zwrócony',
                        IF(b.data_podrozy >= CURDATE(), 'Ważny', 'Nieważny')
                        ) AS status_biletu
                        FROM bilety b
                        JOIN pociagi p ON b.id_pociagu = p.id_pociagu
                        JOIN stacje s1 ON b.id_stacji_start = s1.id_stacji
                        JOIN stacje s2 ON b.id_stacji_koniec = s2.id_stacji
                        JOIN pasazerowie pas ON b.id_pasazera = pas.id_pasazera
                        LEFT JOIN transakcje t ON b.id_biletu = t.id_biletu
                        LEFT JOIN znizki z ON b.id_znizki = z.id_znizki
                        LEFT JOIN zwroty zw ON zw.id_biletu = b.id_biletu
                        LEFT JOIN wagony w ON b.id_wagonu = w.id_wagonu
                        WHERE b.id_pasazera = :id_pasazera
                        ORDER BY b.data_podrozy DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pasazera", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
