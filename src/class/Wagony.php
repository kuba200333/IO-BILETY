<?php
class Wagony {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function pobierzWagonyDlaPociagu($numer_pociagu, $klasa) {
        if ($klasa == "sypialny") {
            $query = "SELECT w.* FROM wagony w join sklady_pociagow sp on sp.id_skladu=w.id_skladu join pociagi p on p.id_pociagu=sp.id_pociagu WHERE p.numer_pociagu = :numer AND w.typ = 'Sypialny'";
        } else {
            $query = "SELECT w.* FROM wagony w join sklady_pociagow sp on sp.id_skladu=w.id_skladu join pociagi p on p.id_pociagu=sp.id_pociagu WHERE p.numer_pociagu = :numer AND klasa = :klasa AND w.typ != 'Sypialny'";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":numer", $numer_pociagu, PDO::PARAM_STR);

        if ($klasa != "sypialny") {
            $stmt->bindParam(":klasa", $klasa, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pobierzZajeteMiejsca($numer_pociagu, $data_podrozy) {
        $query = "
            SELECT w.numer_wagonu, b.miejsce
            FROM bilety b
            INNER JOIN wagony w ON w.id_wagonu = b.id_wagonu
            INNER JOIN pociagi p ON b.id_pociagu = p.id_pociagu
            WHERE p.numer_pociagu = :numer_pociagu AND b.data_podrozy = :data_podrozy
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":numer_pociagu", $numer_pociagu);
        $stmt->bindParam(":data_podrozy", $data_podrozy);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
