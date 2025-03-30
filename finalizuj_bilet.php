<?php
require_once "config.php";
require_once "Bilet.php";

session_start();

if (!isset($_SESSION["user"])) {
    die("Musisz być zalogowany, aby kupić bilet.");
}

$database = new Database();
$db = $database->getConnection();
$bilet = new Bilet($db);

// Pobranie ID pasażera na podstawie loginu
$login = $_SESSION["user"];
$query_pasazer = "SELECT id_pasazera FROM pasazerowie WHERE login = :login";
$stmt = $db->prepare($query_pasazer);
$stmt->bindParam(":login", $login, PDO::PARAM_STR);
$stmt->execute();
$row_pasazer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row_pasazer) {
    die("Błąd: Nie znaleziono pasażera w bazie.");
}

$id_pasazera = $row_pasazer["id_pasazera"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numer_pociagu = $_POST["numer_pociagu"];
    $id_stacji_start = $_POST["id_stacja_start"];
    $id_stacji_koniec = $_POST["id_stacja_koniec"];
    $klasa = $_POST["klasa"];
    $id_znizki = $_POST["id_znizki"];
    $wagon = $_POST["wagon"];
    $miejsce = $_POST["miejsce"];
    $metoda_platnosci = $_POST["metoda_platnosci"];
    $cena = $_POST["cena"];
    $data_podrozy = date("Y-m-d");

    // Pobranie ID pociągu na podstawie numeru
    $query_pociag = "SELECT id_pociagu FROM pociagi WHERE numer_pociagu = :numer_pociagu";
    $stmt = $db->prepare($query_pociag);
    $stmt->bindParam(":numer_pociagu", $numer_pociagu, PDO::PARAM_STR);
    $stmt->execute();
    $row_pociag = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row_pociag) {
        die("Błąd: Nie znaleziono pociągu w bazie.");
    }

    $id_pociagu = $row_pociag["id_pociagu"];

    // Generowanie kodu QR (dla uproszczenia używamy unikalnego ID)
    $kod_qr = md5(uniqid(rand(), true));

    // Wstawienie biletu do bazy danych
    $query_bilet = "INSERT INTO bilety (id_pasazera, id_pociagu, id_stacji_start, id_stacji_koniec, miejsce, cena, data_podrozy, kod_qr, id_wagonu, id_znizki) 
                    VALUES (:id_pasazera, :id_pociagu, :id_stacji_start, :id_stacji_koniec, :miejsce, :cena, :data_podrozy, :kod_qr, :id_wagonu, :id_znizki)";
    $stmt = $db->prepare($query_bilet);
    $stmt->execute([
        ":id_pasazera" => $id_pasazera,
        ":id_pociagu" => $id_pociagu,
        ":id_stacji_start" => $id_stacji_start,
        ":id_stacji_koniec" => $id_stacji_koniec,
        ":miejsce" => $miejsce,
        ":cena" => $cena,
        ":data_podrozy" => $data_podrozy,
        ":kod_qr" => $kod_qr,
        ":id_wagonu" => $wagon,
        ":id_znizki" => $id_znizki
    ]);

    $id_biletu = $db->lastInsertId();

    // Wstawienie transakcji do bazy
    $query_transakcja = "INSERT INTO transakcje (id_biletu, id_pasazera, kwota, metoda_platnosci, status, data_transakcji) 
                         VALUES (:id_biletu, :id_pasazera, :kwota, :metoda_platnosci, 'Zrealizowana', NOW())";
    $stmt = $db->prepare($query_transakcja);
    $stmt->execute([
        ":id_biletu" => $id_biletu,
        ":id_pasazera" => $id_pasazera,
        ":kwota" => $cena,
        ":metoda_platnosci" => $metoda_platnosci
    ]);

    echo "<p>Zakup zakończony! Twój bilet został zapisany.</p>";
}
?>
