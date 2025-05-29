<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "Pasazer.php";
require_once "Bilet.php";
require_once "RozkladJazdy.php";

$database = new Database();
$db = $database->getConnection();

$pasazer = new Pasazer($db);
if (!$pasazer->loadByLogin($_SESSION["user"])) {
    die("Błąd ładowania użytkownika.");
}

// Sprawdzenie, czy id_biletu zostało przesłane POSTem
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST["id_biletu"])) {
    header("Location: moje_bilety.php");
    exit;
}

$bilety = $pasazer->getBilety();
$id_biletu = (int)$_POST["id_biletu"];

// Sprawdzenie, czy bilet należy do zalogowanego pasażera
$bilet = null;
foreach ($bilety as $b) {
    if ($b["id_biletu"] == $id_biletu) {
        $bilet = $b;
        break;
    }
}
if (!$bilet) {
    die("Nie znaleziono biletu lub nie masz do niego dostępu.");
}

$biletObj = new Bilet($db);

// Oblicz liczbę kilometrów przez metodę Bilet (używając numeru pociągu i nazw stacji)
$liczba_km = $biletObj->obliczOdleglosc($bilet["numer_pociagu"], $bilet["stacja_start"], $bilet["stacja_koniec"]);

// Formatowanie dat
$data_podrozy = date("d.m.Y", strtotime($bilet["data_podrozy"]));
$data_transakcji = date("d.m.Y H:i", strtotime($bilet["data_transakcji"]));

$rozkladJazdy = new RozkladJazdy($db);
$godzina_odjazdu = $rozkladJazdy->getGodzinaOdjazdu($id_biletu);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Szczegóły Biletu</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <a href="moje_bilety.php">Powrót do listy biletów</a>
    <h2>Bilet</h2>
    <table>
        <tr><th>PODRÓŻ TAM KURS</th></tr>
        <tr>
            <td><?= $data_podrozy ?> <?= htmlspecialchars($godzina_odjazdu ?? "Brak danych") ?> <?= htmlspecialchars($bilet["stacja_start"]) ?> → <?= htmlspecialchars($bilet["stacja_koniec"]) ?> <?= htmlspecialchars($bilet["numer_pociagu"]) ?></td>
        </tr>
    </table>

    <h2>Informacja o cenie</h2>
    <table>
        <tr><th>Opłata za przejazd</th><td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td></tr>
        <tr><th>RAZEM</th><td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td></tr>
    </table>

    <h2>BILET</h2>
    <table>
        <tr><th>Właściciel biletu</th><td><?= htmlspecialchars($bilet["imie"] . " " . $bilet["nazwisko"]) ?></td></tr>
        <tr>
            <th>Numer biletu</th>
            <td>BK<?= htmlspecialchars($bilet["id_biletu"]) ?>
                <h2>Kod QR</h2>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($bilet["kod_qr"]) ?>" alt="Kod QR">
            </td>
        </tr>
        <tr><th>Numer wagonu</th><td><?= htmlspecialchars($bilet["id_wagonu"]) ?></td></tr>
        <tr><th>Miejsce</th><td><?= htmlspecialchars($bilet["miejsce"]) ?></td></tr>
        <tr><th>Klasa</th><td><?= htmlspecialchars($bilet["klasa"]) ?></td></tr>
        <tr><th>Zniżka</th><td><?= htmlspecialchars($bilet["nazwa_znizki"]) ?> (<?= htmlspecialchars($bilet["wymiar_znizki"]) ?>%)</td></tr>
        <tr><th>Liczba km</th><td><?= htmlspecialchars($liczba_km) ?> km</td></tr>
        <tr><th>Sprzedawca</th><td>InterTicket</td></tr>
        <tr><th>Data zakupu</th><td><?= $data_transakcji ?></td></tr>
    </table>

</body>
</html>
