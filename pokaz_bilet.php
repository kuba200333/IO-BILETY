<?php
require_once "config.php";

session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION["user"])) {
    die("Musisz być zalogowany, aby zobaczyć szczegóły biletu.");
}

// Sprawdzenie, czy podano ID biletu
if (!isset($_GET["id_biletu"])) {
    die("Nie podano ID biletu.");
}

$database = new Database();
$db = $database->getConnection();

$id_biletu = $_GET["id_biletu"];

// Pobranie szczegółów biletu, w tym klasy z tabeli `wagony`
$query_bilet = "SELECT bilety.id_biletu, pociagi.numer_pociagu, 
                        st1.nazwa AS stacja_start, st2.nazwa AS stacja_koniec, 
                        bilety.miejsce, bilety.cena, bilety.data_podrozy, bilety.id_wagonu, bilety.id_znizki,
                        wagony.klasa,
                        pasazerowie.imie, pasazerowie.nazwisko, transakcje.data_transakcji,
                        znizki.nazwa_znizki, znizki.wymiar_znizki
                FROM bilety
                JOIN pociagi ON bilety.id_pociagu = pociagi.id_pociagu
                JOIN stacje st1 ON bilety.id_stacji_start = st1.id_stacji
                JOIN stacje st2 ON bilety.id_stacji_koniec = st2.id_stacji
                JOIN pasazerowie ON bilety.id_pasazera = pasazerowie.id_pasazera
                JOIN transakcje ON bilety.id_biletu = transakcje.id_biletu
                JOIN znizki ON bilety.id_znizki = znizki.id_znizki
                JOIN wagony ON bilety.id_wagonu = wagony.id_wagonu
                WHERE bilety.id_biletu = :id_biletu";

$stmt = $db->prepare($query_bilet);
$stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
$stmt->execute();
$bilet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bilet) {
    die("Nie znaleziono biletu.");
}

// Pobranie godziny odjazdu
$query_odjazd = "SELECT godzina_odjazdu FROM rozklad_jazdy 
                 WHERE id_pociagu = (SELECT id_pociagu FROM bilety WHERE id_biletu = :id_biletu)
                 AND id_stacji = (SELECT id_stacji_start FROM bilety WHERE id_biletu = :id_biletu)";

$stmt = $db->prepare($query_odjazd);
$stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
$stmt->execute();
$rozklad = $stmt->fetch(PDO::FETCH_ASSOC);
$godzina_odjazdu = $rozklad ? $rozklad["godzina_odjazdu"] : "Brak danych";

// Pobranie liczby kilometrów
$query_km = "SELECT SUM(odleglosc_km) AS suma_km FROM odleglosci_miedzy_stacjami
             WHERE id_pociagu = (SELECT id_pociagu FROM bilety WHERE id_biletu = :id_biletu)
             AND id_stacji_poczatek >= (SELECT id_stacji_start FROM bilety WHERE id_biletu = :id_biletu)
             AND id_stacji_koniec <= (SELECT id_stacji_koniec FROM bilety WHERE id_biletu = :id_biletu)";

$stmt = $db->prepare($query_km);
$stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
$stmt->execute();
$row_km = $stmt->fetch(PDO::FETCH_ASSOC);
$liczba_km = $row_km ? $row_km["suma_km"] : "Brak danych";

// Formatowanie daty
$data_podrozy = date("d.m.Y", strtotime($bilet["data_podrozy"]));
$data_transakcji = date("d.m.Y H:i", strtotime($bilet["data_transakcji"]));

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szczegóły Biletu</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Bilet</h2>
    <table>
        <tr><th>PODRÓŻ TAM KURS</th></tr>
        <tr>
            <td><?= $data_podrozy ?> <?= $godzina_odjazdu ?> <?= htmlspecialchars($bilet["stacja_start"]) ?> → <?= htmlspecialchars($bilet["stacja_koniec"]) ?> <?= htmlspecialchars($bilet["numer_pociagu"]) ?></td>
        </tr>
    </table>

    <h2>Informacja o cenie</h2>
    <table>
        <tr>
            <th>Opłata za przejazd</th>
            <td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td>
        </tr>
        <tr>
            <th>RAZEM</th>
            <td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td>
        </tr>
    </table>

    <h2>BILET</h2>
    <table>
        <tr>
            <th>Właściciel biletu</th>
            <td><?= htmlspecialchars($bilet["imie"] . " " . $bilet["nazwisko"]) ?></td>
        </tr>
        <tr>
            <th>Numer biletu</th>
            <td>BK<?= htmlspecialchars($bilet["id_biletu"]) ?></td>
        </tr>
        <tr>
            <th>Numer wagonu</th>
            <td><?= htmlspecialchars($bilet["id_wagonu"]) ?></td>
        </tr>
        <tr>
            <th>Miejsce</th>
            <td><?= htmlspecialchars($bilet["miejsce"]) ?></td>
        </tr>
        <tr>
            <th>Klasa</th>
            <td><?= htmlspecialchars($bilet["klasa"]) ?></td>
        </tr>
        <tr>
            <th>Zniżka</th>
            <td><?= htmlspecialchars($bilet["nazwa_znizki"]) ?> (<?= htmlspecialchars($bilet["wymiar_znizki"]) ?>%)</td>
        </tr>
        <tr>
            <th>Liczba km</th>
            <td><?= $liczba_km ?> km</td>
        </tr>
        <tr>
            <th>Sprzedawca</th>
            <td>PKP Intercity</td>
        </tr>
        <tr>
            <th>Data zakupu</th>
            <td><?= $data_transakcji ?></td>
        </tr>
    </table>

    <a href="moje_bilety.php">Powrót do listy biletów</a>
</body>
</html>
