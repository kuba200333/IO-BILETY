<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Pracownik") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "class/Pasazer.php";
require_once "class/Bilet.php";
require_once "class/RozkladJazdy.php";

$database = new Database();
$db = $database->getConnection();

// Sprawdzenie, czy id_biletu zostało przesłane POSTem
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST["id_biletu"])) {
    header("Location: moje_bilety.php");
    exit;
}

$id_biletu = (int)$_POST["id_biletu"];

$biletObj = new Bilet($db);
$bilet = $biletObj->getBiletById($id_biletu);

if (!$bilet) {
    die("Nie znaleziono biletu.");
}

$liczba_km = $biletObj->obliczOdleglosc($bilet["numer_pociagu"], $bilet["stacja_start"], $bilet["stacja_koniec"]);

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
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
    <h1>Szczegóły Biletu</h1>
</header>

<main class="container">

    <nav>
        <a class="btn" href="moje_bilety.php">Powrót do listy biletów</a>
    </nav>

    <!-- Sekcja podróży -->
    <section class="hero">
        <h2>Podróż tam - kurs</h2>
        <table class="styled-table">
            <tbody>
                <tr>
                    <td><?= $data_podrozy ?> <?= htmlspecialchars($godzina_odjazdu ?? "Brak danych") ?> <br><?= htmlspecialchars($bilet["stacja_start"]) ?> → <?= htmlspecialchars($bilet["stacja_koniec"]) ?> <?= htmlspecialchars($bilet["numer_pociagu"]) ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- Sekcja ceny -->
    <section>
        <h2>Informacja o cenie</h2>
        <table class="styled-table">
            <tbody>
                <tr><th>Opłata za przejazd</th><td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td></tr>
                <tr><th>RAZEM</th><td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td></tr>
            </tbody>
        </table>
    </section>

    <!-- Sekcja biletu -->
    <section>
        <h2>Bilet</h2>
        <table class="styled-table">
            <tbody>
                <tr>
                    <th>Numer biletu</th>
                    <td>
                        BK<?= htmlspecialchars($bilet["id_biletu"]) ?>
                        <div>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($bilet["kod_qr"]) ?>" alt="Kod QR">
                        </div>
                    </td>
                </tr>
                <tr><th>Numer wagonu</th><td><?= htmlspecialchars($bilet["id_wagonu"]) ?></td></tr>
                <tr><th>Miejsce</th><td><?= htmlspecialchars($bilet["miejsce"]) ?></td></tr>
                <tr><th>Klasa</th><td><?= htmlspecialchars($bilet["klasa"]) ?></td></tr>
                <tr><th>Zniżka</th><td><?= htmlspecialchars($bilet["nazwa_znizki"]) ?> (<?= htmlspecialchars($bilet["wymiar_znizki"]) ?>%)</td></tr>
                <tr><th>Liczba km</th><td><?= htmlspecialchars($liczba_km) ?> km</td></tr>
                <tr><th>Sprzedawca</th><td>InterTicket</td></tr>
                <tr><th>Data zakupu</th><td><?= $data_transakcji ?></td></tr>
            </tbody>
        </table>
    </section>

</main>

<footer>
    &copy; <?= date("Y") ?> InterTicket
</footer>

</body>
</html>
