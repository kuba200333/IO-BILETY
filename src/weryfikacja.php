<?php

require_once "config.php";

session_start();



$database = new Database();

$db = $database->getConnection();



// ObsĹ‚uga formularza wyszukiwania

$kod_wyszukiwany = $_GET['kod'] ?? '';

$bilet = null;

$godzina_odjazdu = "";

$liczba_km = "";



if (!empty($kod_wyszukiwany)) {

    $query_bilet = "SELECT bilety.id_biletu, bilety.kod_qr, pociagi.numer_pociagu, 

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

                    WHERE bilety.kod_qr = :kod OR bilety.id_biletu = :kod_int";



    $stmt = $db->prepare($query_bilet);

    $stmt->bindValue(":kod", $kod_wyszukiwany, PDO::PARAM_STR);

    $stmt->bindValue(":kod_int", (int)$kod_wyszukiwany, PDO::PARAM_INT);

    $stmt->execute();

    $bilet = $stmt->fetch(PDO::FETCH_ASSOC);



    if ($bilet) {

        $id_biletu = $bilet["id_biletu"];



        $query_odjazd = "SELECT godzina_odjazdu FROM rozklad_jazdy 

                         WHERE id_pociagu = (SELECT id_pociagu FROM bilety WHERE id_biletu = :id_biletu)

                         AND id_stacji = (SELECT id_stacji_start FROM bilety WHERE id_biletu = :id_biletu)";

        $stmt = $db->prepare($query_odjazd);

        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);

        $stmt->execute();

        $rozklad = $stmt->fetch(PDO::FETCH_ASSOC);

        $godzina_odjazdu = $rozklad ? $rozklad["godzina_odjazdu"] : "Brak danych";

        
        $query_przyjazd = "SELECT godzina_przyjazdu FROM rozklad_jazdy 

                         WHERE id_pociagu = (SELECT id_pociagu FROM bilety WHERE id_biletu = :id_biletu)

                         AND id_stacji = (SELECT id_stacji_koniec FROM bilety WHERE id_biletu = :id_biletu)";

        $stmt = $db->prepare($query_przyjazd);

        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);

        $stmt->execute();

        $rozklad = $stmt->fetch(PDO::FETCH_ASSOC);

        $godzina_przyjazdu= $rozklad ? $rozklad["godzina_przyjazdu"] : "Brak danych";


        $query_km = "SELECT SUM(odleglosc_km) AS suma_km FROM odleglosci_miedzy_stacjami

                     WHERE id_pociagu = (SELECT id_pociagu FROM bilety WHERE id_biletu = :id_biletu)

                     AND id_stacji_poczatek >= (SELECT id_stacji_start FROM bilety WHERE id_biletu = :id_biletu)

                     AND id_stacji_koniec <= (SELECT id_stacji_koniec FROM bilety WHERE id_biletu = :id_biletu)";

        $stmt = $db->prepare($query_km);

        $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);

        $stmt->execute();

        $row_km = $stmt->fetch(PDO::FETCH_ASSOC);

        $liczba_km = $row_km ? $row_km["suma_km"] : "Brak danych";

        
    }

}

?>



<!DOCTYPE html>

<html lang="pl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Weryfikacja Biletu</title>

    <style>

        body { font-family: Arial, sans-serif; padding: 10px; }

        input, button { font-size: 1.2em; padding: 10px; margin: 10px 0; width: 100%; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }

        th, td { border: 1px solid #ccc; padding: 10px; }

        th { background-color: #f0f0f0; }

        img { max-width: 100%; height: auto; }

    </style>

</head>

<body>
<a href="dashboard.php">Strona glowna</a>

<a href='weryfikacja.php'>Weryfikacja</a>
<h1>Weryfikacja Biletu</h1>

<form method="get">

    <label for="kod">Wprowadź ID biletu lub kod QR:</label>

    <input type="text" name="kod" id="kod" placeholder="np. 123 lub ABC123" required>

    <button type="submit">Wyszukaj</button>

</form>



<?php if ($bilet): ?>

    <h2>Bilet nr BK<?= htmlspecialchars($bilet["id_biletu"]) ?></h2>

    <table>

        <tr><th>Imię i nazwisko</th><td><?= htmlspecialchars($bilet["imie"] . " " . $bilet["nazwisko"]) ?></td></tr>

        <tr><th>Pociąg</th><td><?= htmlspecialchars($bilet["numer_pociagu"]) ?> </td></tr>

        <tr><th>Trasa</th><td> <?= htmlspecialchars($bilet["stacja_start"]) ?> (<?= $godzina_odjazdu ?>) - <?= htmlspecialchars($bilet["stacja_koniec"]) ?> (<?= $godzina_przyjazdu ?>)  </td></tr>

        <tr><th>Data podróży</th><td><?= date("d.m.Y", strtotime($bilet["data_podrozy"])) ?></td></tr>

        <tr><th>Wagon</th><td><?= htmlspecialchars($bilet["id_wagonu"]) ?> (klasa <?= htmlspecialchars($bilet["klasa"]) ?>)</td></tr>

        <tr><th>Miejsce</th><td><?= htmlspecialchars($bilet["miejsce"]) ?></td></tr>

        <tr><th>Cena</th><td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td></tr>

        <tr><th>Zniżka</th><td><?= htmlspecialchars($bilet["nazwa_znizki"]) ?> (<?= $bilet["wymiar_znizki"] ?>%)</td></tr>

        <tr><th>Liczba km</th><td><?= $liczba_km ?> km</td></tr>

        <tr><th>Data zakupu</th><td><?= date("d.m.Y H:i", strtotime($bilet["data_transakcji"])) ?></td></tr>

        <!--<tr><th>Kod QR</th><td><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode('BK' . $bilet["kod_qr"]) ?>" alt="Kod QR"></td></tr>-->

    </table>

<?php elseif (!empty($kod_wyszukiwany)): ?>

    <p style="color:red">Nie znaleziono biletu dla podanego kodu.</p>

<?php endif; ?>

<p id="processing-message" style="color: green; display: none;">Przetwarzanie...</p>
<audio id="scan-sound" src="dzwiek.mp3" preload="auto"></audio>


</body>

</html>



<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>



<button type="button" onclick="startScanner()">Skanuj kod QR</button>

<div id="qr-reader" style="width:100%; max-width:500px;"></div>



<script>

function startScanner() {
    const qrReader = new Html5Qrcode("qr-reader");

    qrReader.start(
        { facingMode: "environment" }, // Kamera tylna
        {
            fps: 10,
            qrbox: 250
        },
        (decodedText) => {
            // Wyświetl komunikat "Przetwarzanie..."
            document.getElementById("processing-message").style.display = "block";
            
            // Odtwórz dźwięk
            document.getElementById("scan-sound").play();

            // Wpisz zeskanowany kod
            document.getElementById("kod").value = decodedText;

            // Opóźnij przetwarzanie o 3 sekundy
            setTimeout(() => {
                qrReader.stop().then(() => {
                    document.getElementById("kod").form.submit();
                });
            }, 1500); // 3000 ms = 3 sekundy
        },
        (errorMessage) => {
            // Obsługa błędów (opcjonalnie)
        }
    ).catch(err => {
        console.error("Błąd przy uruchamianiu kamery: ", err);
    });
}

</script>

