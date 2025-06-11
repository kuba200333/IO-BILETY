<?php
require_once "config.php";

class Pracownik {
    private $conn;
    private $table_name = "pracownicy";

    public function __construct($db) {
        $this->conn = $db;
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
            $_SESSION["role"] = $user["stanowisko"]; 
            return true;
        }
        return false;
    }

    public function getIdByLogin($login) {
        $query = "SELECT id_pracownika FROM " . $this->table_name . " WHERE login = :login";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $login);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row["id_pracownika"] : null;
    }

    public function getAllPracownicy() {
        $query = "SELECT id_pracownika, imie, nazwisko FROM pracownicy where stanowisko ='Pracownik' ORDER BY nazwisko, imie";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
