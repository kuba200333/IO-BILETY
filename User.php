<?php
require_once "config.php";

class User {
    private $conn;
    private $table_name;

    public function __construct($db, $role) {
        $this->conn = $db;
        $this->table_name = ($role === "pracownik") ? "pracownicy" : "pasazerowie";
    }

    public function register($imie, $nazwisko, $login, $haslo) {
        $hashed_password = password_hash($haslo, PASSWORD_DEFAULT);

        $query = "INSERT INTO pasazerowie (imie, nazwisko, login, haslo) 
                  VALUES (:imie, :nazwisko, :login, :haslo)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":imie", $imie);
        $stmt->bindParam(":nazwisko", $nazwisko);
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":haslo", $hashed_password);

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
            $_SESSION["role"] = ($this->table_name === "pracownicy") ? "pracownik" : "pasazer";
            return true;
        }
        return false;
    }
}
?>
