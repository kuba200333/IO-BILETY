<?php

?>

<?php
require_once "config.php";

class Pracownik {
    private $conn;
    private $table_name = "pracownicy";

    public $imie;
    public $nazwisko;
    public $stanowisko;
    public $login;
    public $haslo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function dodajPracownika() {
        try {
            $query = "INSERT INTO " . $this->table_name . " (imie, nazwisko, stanowisko, login, haslo) 
                      VALUES (:imie, :nazwisko, :stanowisko, :login, :haslo)";
            $stmt = $this->conn->prepare($query);

            $hashed_password = password_hash($this->haslo, PASSWORD_DEFAULT);

            $stmt->bindParam(":imie", $this->imie);
            $stmt->bindParam(":nazwisko", $this->nazwisko);
            $stmt->bindParam(":stanowisko", $this->stanowisko);
            $stmt->bindParam(":login", $this->login);
            $stmt->bindParam(":haslo", $hashed_password);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $exception) {
            echo "Błąd: " . $exception->getMessage();
            return false;
        }
    }
}
?>
