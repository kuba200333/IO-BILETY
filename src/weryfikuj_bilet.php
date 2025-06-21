<?php
require_once "config.php";
require_once "class/Bilet.php";
require_once "class/RozkladJazdy.php";
require_once "class/SkanowanieBiletow.php";
require_once "class/Pracownik.php";

session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Kierownik") {
    header("Location: index.php");
    exit;
}
$database = new Database();
$db = $database->getConnection();

$kod = $_GET['kod'] ?? '';
$biletObj = new Bilet($db);
$rozkladObj = new RozkladJazdy($db);

$bilet = null;
$godzina_odjazdu = $godzina_przyjazdu = $liczba_km = "";

$pracownikObj = new Pracownik($db);
$login = $_SESSION["user"];
$id_pracownika = $pracownikObj->getIdByLogin($login);

if (!empty($kod)) {
    $bilet = $biletObj->pobierzSzczegoly($kod);

    if ($bilet) {
        $id_biletu = $bilet["id_biletu"];
        $numer_pociagu = $bilet["numer_pociagu"];
        $stacja_start = $bilet["stacja_start"];
        $stacja_koniec = $bilet["stacja_koniec"];

        $godzina_odjazdu = $rozkladObj->getGodzinaOdjazdu($id_biletu);
        $godzina_przyjazdu = $rozkladObj->getGodzinaPrzyjazdu($id_biletu);
        $liczba_km = $biletObj->obliczOdleglosc($numer_pociagu, $stacja_start, $stacja_koniec);
        $skanowanieObj = new SkanowanieBiletow($db);
        $data_skanowania = date('Y-m-d H:i:s');
        $sukces = $skanowanieObj->zapiszSkanowanie($bilet['id_biletu'], $id_pracownika, $data_skanowania);
    
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weryfikacja Biletu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="container">
        <h1>System Sprzedaży Biletów</h1>
    </div>
</header>

<main class="container">
    <section class="hero">
        <a class="btn" href="index.php">Strona główna</a>
        <a class="btn" href="weryfikuj_bilet.php">Weryfikacja</a><br><br><br>
        <h2>Weryfikacja biletu</h2>
        <form method="get">
            <label for="kod">Wprowadź ID biletu lub kod QR:</label>
            <input type="text" name="kod" id="kod" placeholder="np. 123 lub ABC123" required><br><br>
            <button type="submit">Wyszukaj</button>
        </form>

        <?php if ($bilet): ?>
            <h3>Bilet nr BK<?= htmlspecialchars($bilet["id_biletu"]) ?></h3>
            <table class="styled-table">
                <tr><th>Imię i nazwisko</th><td><?= htmlspecialchars($bilet["imie"] . " " . $bilet["nazwisko"]) ?></td></tr>
                <tr><th>Pociąg</th><td><?= htmlspecialchars($bilet["numer_pociagu"]) ?></td></tr>
                <tr><th>Trasa</th><td><?= htmlspecialchars($bilet["stacja_start"]) ?> (<?= $godzina_odjazdu ?>) - <?= htmlspecialchars($bilet["stacja_koniec"]) ?> (<?= $godzina_przyjazdu ?>)</td></tr>
                <tr><th>Data podróży</th><td><?= date("d.m.Y", strtotime($bilet["data_podrozy"])) ?></td></tr>
                <tr><th>Wagon</th><td><?= htmlspecialchars($bilet["id_wagonu"]) ?> (klasa <?= htmlspecialchars($bilet["klasa"]) ?>)</td></tr>
                <tr><th>Miejsce</th><td><?= htmlspecialchars($bilet["miejsce"]) ?></td></tr>
                <tr><th>Cena</th><td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td></tr>
                <tr><th>Zniżka</th><td><?= htmlspecialchars($bilet["nazwa_znizki"]) ?> (<?= $bilet["wymiar_znizki"] ?>%)</td></tr>
                <tr><th>Liczba km</th><td><?= $liczba_km ?> km</td></tr>
                <tr><th>Data zakupu</th><td><?= date("d.m.Y H:i", strtotime($bilet["data_transakcji"])) ?></td></tr>
                <!-- <tr><th>Kod QR</th><td><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode('BK' . $bilet["kod_qr"]) ?>" alt="Kod QR"></td></tr> -->
            </table>

        <?php elseif (!empty($kod)): ?>
            <p style="color:red; margin-top:1rem;">Nie znaleziono biletu dla podanego kodu.</p>
        <?php endif; ?>
        <p id="processing-message" style="color: green; display: none; margin-top:1rem;">Przetwarzanie...</p>

        <button type="button" onclick="startScanner()">Skanuj kod QR</button>
        <div id="qr-reader" style="width:100%; max-width:500px; margin-top:1rem;"></div>

        <audio id="scan-sound" src="dzwiek.mp3" preload="auto"></audio>
    </section>
</main>

<footer>
    <div class="container">
        &copy; <?= date("Y") ?> System Sprzedaży Biletów PolRail
    </div>
</footer>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function startScanner() {
        const qrReader = new Html5Qrcode("qr-reader");

        qrReader.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                document.getElementById("processing-message").style.display = "block";
                document.getElementById("scan-sound").play();
                document.getElementById("kod").value = decodedText;
                setTimeout(() => {
                    qrReader.stop().then(() => {
                        document.getElementById("kod").form.submit();
                    });
                }, 1500);
            },
            (errorMessage) => {}
        ).catch(err => {
            console.error("Błąd przy uruchamianiu kamery: ", err);
        });
    }
</script>

</body>
</html>
