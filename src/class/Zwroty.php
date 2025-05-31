<?php
class Zwroty {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getZwrotyByPasazerId($id_pasazera) {
        $query = "SELECT z.id_zwrotu, z.id_biletu, z.id_pasazera, z.status, z.data_zwrotu, z.id_pracownika, 
                         concat(st1.nazwa, '- ', st2.nazwa) as relacja, z.uwagi_pasazer, z.uwagi_pracownik 
                  FROM zwroty z 
                  JOIN bilety b ON b.id_biletu = z.id_biletu 
                  JOIN stacje st1 ON st1.id_stacji = b.id_stacji_start 
                  JOIN stacje st2 ON st2.id_stacji = b.id_stacji_koniec 
                  WHERE z.id_pasazera = :id_pasazera";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pasazera", $id_pasazera, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function czyZwrotIstnieje($id_biletu) {
        $query = "SELECT COUNT(*) FROM zwroty WHERE id_biletu = :id_biletu";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function dodajZwrot($id_biletu, $id_pasazera, $nr_konta, $uwagi_pasazer, $typ) {
        $query = "INSERT INTO zwroty (id_biletu, id_pasazera, status, data_zwrotu, nr_konta, uwagi_pasazer, typ_zgloszenia) 
                  VALUES (:id_biletu, :id_pasazera, 'oczekujący', NOW(), :nr_konta, :uwagi_pasazer, :typ_zgloszenia)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
        $stmt->bindParam(":id_pasazera", $id_pasazera, PDO::PARAM_INT);
        $stmt->bindParam(":nr_konta", $nr_konta);
        $stmt->bindParam(":uwagi_pasazer", $uwagi_pasazer);
        $stmt->bindParam(":typ_zgloszenia", $typ);
        return $stmt->execute();
    }

    public function czyBiletNalezyDoPasazera($id_biletu, $bilety) {
        foreach ($bilety as $b) {
            if ($b["id_biletu"] == $id_biletu) {
                return true;
            }
        }
        return false;
    }
}
?>
