<?php
class Zwroty {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getZwrotyByPasazerId($id_pasazera) {
        $query = "SELECT `id_zwrotu`, `id_biletu`, `id_pasazera`, `status`, `data_zwrotu`, `id_pracownika`
                  FROM `zwroty` WHERE `id_pasazera` = :id_pasazera";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pasazera", $id_pasazera, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
