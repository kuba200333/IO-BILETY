<?php
require_once "config.php";
require_once "class/Bilet.php";

session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Pracownik") {
    header("Location: index.php");
    exit;
}

if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "sprzedaj_bilet_pracownik.php") === false) {
    header("Location: index.php");
    exit;
}


$database = new Database();
$db = $database->getConnection();
$bilet = new Bilet($db);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numer_pociagu = $_POST["numer_pociagu"];
    $id_stacji_start = $_POST["stacja_start"];
    $id_stacji_koniec = $_POST["stacja_koniec"];
    $klasa = $_POST["klasa"];
    $id_znizki = $_POST["znizka"];
    $wagon = $_POST["wagon"];
    $miejsce = $_POST["miejsce"];
    $metoda_platnosci = $_POST["metoda_platnosci"];
    $cena = $_POST["cena"];
    $data_podrozy = $_POST["data_podrozy"];
    $oplata_dodatkowa= $_POST["oplata_dodatkowa"];

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
    $query_bilet = "INSERT INTO bilety (id_pociagu, id_stacji_start, id_stacji_koniec, miejsce, cena, data_podrozy, kod_qr, id_wagonu, id_znizki, oplata_dodatkowa) 
                    VALUES (:id_pociagu, :id_stacji_start, :id_stacji_koniec, :miejsce, :cena, :data_podrozy, :kod_qr, :id_wagonu, :id_znizki, :oplata_dodatkowa)";
    $stmt = $db->prepare($query_bilet);
    $stmt->execute([
        ":id_pociagu" => $id_pociagu,
        ":id_stacji_start" => $id_stacji_start,
        ":id_stacji_koniec" => $id_stacji_koniec,
        ":miejsce" => $miejsce,
        ":cena" => $cena,
        ":data_podrozy" => $data_podrozy,
        ":kod_qr" => $kod_qr,
        ":id_wagonu" => $wagon,
        ":oplata_dodatkowa" => $oplata_dodatkowa,
        ":id_znizki" => $id_znizki
    ]);

    $id_biletu = $db->lastInsertId();

    // Wstawienie transakcji do bazy
    $query_transakcja = "INSERT INTO transakcje (id_biletu, kwota, metoda_platnosci, status, data_transakcji) 
                         VALUES (:id_biletu, :kwota, :metoda_platnosci, 'Zrealizowana', NOW())";
    $stmt = $db->prepare($query_transakcja);
    $stmt->execute([
        ":id_biletu" => $id_biletu,
        ":kwota" => $cena,
        ":metoda_platnosci" => $metoda_platnosci
    ]);

    //echo "<p>Zakup zakończony! Twój bilet został zapisany.</p>";
    echo '
    <form id="redirectForm" action="podsumowanie_sprzedazy.php" method="post" target="_blank" style="display:none;">
        <input type="hidden" name="id_biletu" value="' . htmlspecialchars($id_biletu) . '">
    </form>
    <script>
        // Otwórz w nowej karcie
        document.getElementById("redirectForm").submit();

        // Przekieruj bieżącą stronę do index.php
        window.location.href = "sprzedaj_bilet.php";
    </script>';
    exit;

}
?>
