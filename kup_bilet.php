<?php
require_once "config.php";
require_once "Bilet.php";

$database = new Database();
$db = $database->getConnection();
$bilet = new Bilet($db);

// Pobranie dostępnych zniżek z tabeli znizki
$query_znizki = "SELECT id_znizki, nazwa_znizki, wymiar_znizki FROM znizki order by wymiar_znizki asc";
$stmt = $db->prepare($query_znizki);
$stmt->execute();
$znizki = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numer_pociagu = $_POST["numer_pociagu"];
    $stacja_start = $_POST["stacja_start"];
    $stacja_koniec = $_POST["stacja_koniec"];

    $query_id_start = "SELECT id_stacji FROM stacje WHERE nazwa = :stacja_start";
    $stmt = $db->prepare($query_id_start);
    $stmt->bindParam(":stacja_start", $stacja_start, PDO::PARAM_STR);
    $stmt->execute();
    $row_start = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_stacji_start = $row_start ? $row_start["id_stacji"] : null;

    $query_id_koniec = "SELECT id_stacji FROM stacje WHERE nazwa = :stacja_koniec";
    $stmt = $db->prepare($query_id_koniec);
    $stmt->bindParam(":stacja_koniec", $stacja_koniec, PDO::PARAM_STR);
    $stmt->execute();
    $row_koniec = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_stacji_koniec = $row_koniec ? $row_koniec["id_stacji"] : null;

    $odleglosc = $bilet->obliczOdleglosc($numer_pociagu, $stacja_start, $stacja_koniec);
    
    $cena_klasa_1 = $bilet->obliczCene($odleglosc, 1, 0);
    $cena_klasa_2 = $bilet->obliczCene($odleglosc, 2, 0);
    $cena_sypialny = $cena_klasa_2 + 79; // Dopłata 79 zł
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kup bilet</title>
    <style>
        .kafelki { display: flex; gap: 10px; margin-bottom: 20px; }
        .kafelek {
            padding: 15px; border: 2px solid black; cursor: pointer;
            width: 150px; text-align: center; font-weight: bold;
            border-radius: 10px; transition: 0.3s;
        }
        .kafelek:hover { background-color: lightgray; }
        .wybrany { background-color: dodgerblue; color: white; }
        #formularz { display: none; }
    </style>
    <script>
        function wybierzKlase(klasa, cena) {
            document.getElementById("klasa").value = klasa;
            document.getElementById("cena_koncowa").innerText = cena.toFixed(2) + " PLN";
            document.getElementById("cena").value = cena.toFixed(2);
            document.getElementById("cena_bazowa").value = cena.toFixed(2);

            // Pokaż formularz wyboru miejsca po kliknięciu w ofertę
            document.getElementById("formularz").style.display = "block";

            // Zmiana podświetlenia
            document.querySelectorAll(".kafelek").forEach(k => k.classList.remove("wybrany"));
            document.getElementById("kafelek_" + klasa).classList.add("wybrany");
        }

        function aktualizujCene() {
            let cenaBazowa = parseFloat(document.getElementById("cena_bazowa").value); // ZAMIENNIK
            let znizkaSelect = document.getElementById("znizka");
            let klasa = document.getElementById("klasa").value;
            
            let znizkaProcent = parseFloat(znizkaSelect.options[znizkaSelect.selectedIndex].getAttribute("data-znizka"));
            let cenaPoZnizce = cenaBazowa * (1 - znizkaProcent / 100);

            if (klasa === "sypialny") {
                cenaPoZnizce = cenaBazowa + 79.00;
            }

            let idZnizki = znizkaSelect.value;
            if ((idZnizki == 5 || idZnizki == 6 || idZnizki == 31) && cenaPoZnizce < 7.50) {
                cenaPoZnizce = 7.50;
            }

            document.getElementById("cena_koncowa").innerText = cenaPoZnizce.toFixed(2) + " PLN";
            document.getElementById("cena").value = cenaPoZnizce.toFixed(2);
        }


    </script>
</head>
<body>
    <h2>Zakup biletu</h2>
    <p><strong>Odległość:</strong> <?= $odleglosc ?> km</p>

    <h3>Wybierz ofertę:</h3>
    <div class="kafelki">
        <div class="kafelek" id="kafelek_1" onclick="wybierzKlase('1', <?= $cena_klasa_1 ?>)">1 klasa<br><?= $cena_klasa_1 ?> PLN</div>
        <div class="kafelek" id="kafelek_2" onclick="wybierzKlase('2', <?= $cena_klasa_2 ?>)">2 klasa<br><?= $cena_klasa_2 ?> PLN</div>
        <div class="kafelek" id="kafelek_sypialny" onclick="wybierzKlase('sypialny', <?= $cena_sypialny ?>)">Sypialny<br><?= $cena_sypialny ?> PLN</div>
    </div>

    <div id="formularz">
        <p><strong>Cena po zniżce:</strong> <span id="cena_koncowa"></span></p>

        <form method="post" action="finalizuj_bilet.php">
            <input type="hidden" name="numer_pociagu" value="<?= $numer_pociagu ?>">
            <input type="hidden" name="id_stacja_start" value="<?= $id_stacji_start ?>">
            <input type="hidden" name="id_stacja_koniec" value="<?= $id_stacji_koniec ?>">
            <input type="hidden" name="odleglosc" value="<?= $odleglosc ?>">
            <input type="hidden" id="cena" name="cena">
            <input type="hidden" id="cena_bazowa">
            <input type="hidden" id="klasa" name="klasa">

            <label><strong>Zniżka:</strong></label>
            <select name="id_znizki" id="znizka" onchange="aktualizujCene()">
                <?php foreach ($znizki as $znizka): ?>
                    <option value="<?= $znizka['id_znizki'] ?>" data-znizka="<?= $znizka['wymiar_znizki'] ?>">
                        <?= htmlspecialchars($znizka['nazwa_znizki']) ?> (<?= htmlspecialchars($znizka['wymiar_znizki']) ?>%)
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label><strong>Numer wagonu:</strong></label>
            <input type="number" name="wagon" required><br><br>

            <label><strong>Numer miejsca:</strong></label>
            <input type="number" name="miejsce" required><br><br>
            <label><strong>Wybór płatności:</strong></label>
            <select name="metoda_platnosci">
                <option value="karta">Karta płatnicza</option>
                <option value="blik">BLIK</option>
                <option value="gotówka">Gotówka</option>
            </select><br><br>
            <input type="submit" value="Zatwierdź zakup">
        </form>
    </div>
</body>
</html>
