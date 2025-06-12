<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Pracownik") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "class/Bilet.php";
require_once "class/Znizka.php";
require_once "class/Wagony.php";

$database = new Database();
$db = $database->getConnection();
$bilet = new Bilet($db);

$znizkaObj = new Znizka($db);
$znizki = $znizkaObj->pobierzZnizki();

$numer_pociagu = $_POST["numer_pociagu"] ?? null;
$stacja_start = $_POST["stacja_start"] ?? null;
$stacja_koniec = $_POST["stacja_koniec"] ?? null;
$klasa_wybrana = $_POST["klasa"] ?? null;

$odleglosc = null;
$cena_klasa_1 = null;
$cena_klasa_2 = null;
$cena_sypialny = null;
$wagon_lista = [];


if ($numer_pociagu && $stacja_start && $stacja_koniec) {
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

    $data_podrozy = $_POST["data_podrozy"];

    list($cena_klasa_1, $promo1) = $bilet->obliczCene($odleglosc, 1, 0, $data_podrozy);
    list($cena_klasa_2, $promo2) = $bilet->obliczCene($odleglosc, 2, 0, $data_podrozy);
    $cena_sypialny = $cena_klasa_2 + 79;


    $wagonyObj = new Wagony($db);

    if ($klasa_wybrana && $numer_pociagu) {
        $wagon_lista = $wagonyObj->pobierzWagonyDlaPociagu($numer_pociagu, $klasa_wybrana);
    }
    $zajete_miejsca = [];

    if ($klasa_wybrana && $numer_pociagu) {
        $wagon_lista = $wagonyObj->pobierzWagonyDlaPociagu($numer_pociagu, $klasa_wybrana);
        $zajete_miejsca = $wagonyObj->pobierzZajeteMiejscaNaOdcinku($numer_pociagu, $data_podrozy, $id_stacji_start, $id_stacji_koniec);
    }
    //echo '<pre>' . print_r($zajete_miejsca, true) . '</pre>';
    echo '<script>';
    echo 'const zajeteMiejsca = ' . json_encode($zajete_miejsca) . ';';
    echo '</script>';


} else {
    // Domyślne wartości, jeśli nie podano
    $id_stacji_start = null;
    $id_stacji_koniec = null;
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sprzedaj bilet</title>
    <style>
        .kafelki { display: flex; gap: 10px; margin-bottom: 20px; }
        .kafelek {
            padding: 15px; border: 2px solid black; cursor: pointer;
            width: 150px; text-align: center; font-weight: bold;
            border-radius: 10px; transition: 0.3s;
            user-select: none;
        }
        .kafelek:hover { background-color: lightgray; }
        .wybrany { background-color: dodgerblue; color: white; }
        #formularz { display: none; }

        .wagon {
            border: 2px solid #444;
            border-radius: 6px;
            padding: 4px;
            min-width: 220px;
            flex-shrink: 0;
        }

        img.lokomotywa {
            height: 58px;
            margin-right: 10px;
        }

        .wagon-nazwa {
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
            font-size: 0.85em;
        }

        .miejsca {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            justify-content: center;
        }

        .miejsce {
            width: 40px;
            height: 40px;
            border: 1px solid #888;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            transition: background-color 0.2s, border-color 0.2s;
            font-size: 0.7em;
            background-image: url('image/zielone_miejsce.png');
            margin: 2px;
        }

        .miejsce:hover {
            color: white;
        }

        .miejsce.wybrane {
            color: white;
            background-image: url('image/pomaranczowe_miejsce.png');
        }

        .miejsce.zajete {
            pointer-events: none;
            background-color: #999;
            color: white;
            background-image: url('image/szare_miejsce.png');
        }

        .cos {
            clear: both;
        }

        .kontener-wagonow {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding: 5px;
            margin-bottom: 10px;
            max-width: 100%;
        }

        .kontener-wagonow::-webkit-scrollbar {
            height: 6px;
        }

        .kontener-wagonow::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 5px;
        }


    </style>

    <script>
        // Przy ładowaniu strony, jeśli jest klasa, pokazujemy formularz
        window.onload = function() {
            const klasa = '<?= htmlspecialchars($klasa_wybrana) ?>';
            if (klasa) {
                document.getElementById("formularz").style.display = "block";
                document.getElementById("kafelek_" + klasa).classList.add("wybrany");
                // ustaw też cenę bazową i końcową
                let ceny = {
                    "1": <?= json_encode($cena_klasa_1) ?>,
                    "2": <?= json_encode($cena_klasa_2) ?>,
                    "sypialny": <?= json_encode($cena_sypialny) ?>
                };
                let cena = ceny[klasa];
                if (cena !== undefined) {
                    document.getElementById("cena_koncowa").innerText = cena.toFixed(2) + " PLN";
                    document.getElementById("cena").value = cena.toFixed(2);
                    document.getElementById("cena_bazowa").value = cena.toFixed(2);
                    document.getElementById("klasa").value = klasa;
                }
            }
            document.querySelectorAll('.miejsce').forEach(miejsce => {
            const nr = miejsce.getAttribute('data-nr');
            const wagon = miejsce.getAttribute('data-wagon');

            for (const zaj of zajeteMiejsca) {
                if (zaj.miejsce == nr && zaj.numer_wagonu == wagon) {
                    miejsce.classList.add('zajete');
                    miejsce.style.backgroundColor = '#ccc';
                    miejsce.style.cursor = 'not-allowed';
                    miejsce.onclick = null;
                }
            }
        });

        }

        function wybierzKlase(klasa) {
            // Ustaw ukryte pole i wyślij formularz wyboru klasy
            document.getElementById("klasa_wybor").value = klasa;
            document.getElementById("form_klasa").submit();
        }

        function wybierzMiejsce(wagonId, miejsceNr, elem) {
            // Odznacz poprzednio wybrane miejsce
            document.querySelectorAll(".miejsce.wybrane").forEach(el => el.classList.remove("wybrane"));

            // Zaznacz kliknięte miejsce
            elem.classList.add("wybrane");

            // Ustaw ukryte pola formularza
            document.getElementById("wagon").value = wagonId;
            document.getElementById("miejsce").value = miejsceNr;
        }

        function aktualizujCene() {
            let cenaBazowa = parseFloat(document.getElementById("cena_bazowa").value);
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
    <h2>Wybierz klasę wagonu:</h2>

    <!-- Formularz tylko do wyboru klasy -->
    <form id="form_klasa" method="post" action="">
        <input type="hidden" id="klasa_wybor" name="klasa" value="" />
        <input type="hidden" name="numer_pociagu" value="<?= htmlspecialchars($numer_pociagu) ?>" />
        <input type="hidden" name="stacja_start" value="<?= htmlspecialchars($stacja_start) ?>" />
        <input type="hidden" name="stacja_koniec" value="<?= htmlspecialchars($stacja_koniec) ?>" />
        <input type="hidden" name="data_podrozy" value="<?= htmlspecialchars($data_podrozy) ?>" />
    </form>

    <div class="kafelki">
        <div id="kafelek_1" class="kafelek" onclick="wybierzKlase('1')">Klasa 1</div>
        <div id="kafelek_2" class="kafelek" onclick="wybierzKlase('2')">Klasa 2</div>
        <div id="kafelek_sypialny" class="kafelek" onclick="wybierzKlase('sypialny')">Sypialny</div>
    </div>

    <!-- Ten formularz jest widoczny po wybraniu klasy -->
    <div id="formularz" style="display:none;">
        <form id="form_bilet" method="post" action="sprzedaz_finalizuj_bilet.php">
            <input type="hidden" id="klasa" name="klasa" value="<?= htmlspecialchars($klasa_wybrana) ?>" />
            <input type="hidden" id="numer_pociagu" name="numer_pociagu" value="<?= htmlspecialchars($numer_pociagu) ?>" />
            <input type="hidden" id="stacja_start" name="stacja_start" value="<?= htmlspecialchars($id_stacji_start) ?>" />
            <input type="hidden" id="stacja_koniec" name="stacja_koniec" value="<?= htmlspecialchars($id_stacji_koniec) ?>" />
            <input type="hidden" id="cena_bazowa" name="cena_bazowa" value="<?= $cena_klasa_1 ?>" />
            <input type="hidden" id="cena" name="cena" value="<?= $cena_klasa_1 ?>" />
            <input type="hidden" id="wagon" name="wagon" value="" />
            <input type="hidden" id="miejsce" name="miejsce" value="" />
            <input type="hidden" name="data_podrozy" value="<?= htmlspecialchars($data_podrozy) ?>" />

            <h3>Wybierz wagon i miejsce:</h3>
            <div class="kontener-wagonow">

            <?php if (!empty($wagon_lista)) : ?>
                <?php foreach ($wagon_lista as $wagon) : ?>
                    <div class="wagon">
                        <div class="wagon-nazwa">Wagon: <?= htmlspecialchars($wagon["numer_wagonu"]) ?> (<?= htmlspecialchars($wagon["typ"]) ?>)</div>
                            <div class="miejsca" style="display: grid; grid-template-rows: repeat(4, auto); grid-auto-flow: column; gap: 5px;">
                                <?php
                                $max_wysokosc = 4;
                                $liczba_miejsc = $wagon["liczba_miejsc"];
                                for ($m = 1; $m <= $liczba_miejsc; $m++) {
                                    ?>
                                    <div class="miejsce"
                                        data-nr="<?= $m ?>"
                                        data-wagon="<?= $wagon["numer_wagonu"] ?>"
                                        onclick="wybierzMiejsce('<?= $wagon["numer_wagonu"] ?>', <?= $m ?>, this)">
                                        <?= $m ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Brak dostępnych wagonów dla tej klasy.</p>
            <?php endif; ?>
            </div>
            <div class='cos'>
            <h3>Legenda</h3>
            Miejsce wolne: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src="image/zielone_miejsce.png" alt="Miejsce wolne">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
            Miejsce zajęte: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src="image/szare_miejsce.png" alt="Miejsce zajęte"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
            Miejsce wybrane:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <img src="image/pomaranczowe_miejsce.png" alt="Miejsce wybrane"><br>

            <h3>Wybierz zniżkę:</h3>
            <select name="znizka" id="znizka" onchange="aktualizujCene()">
                <?php foreach ($znizki as $znizka): ?>
                    <option value="<?= $znizka["id_znizki"] ?>" data-znizka="<?= $znizka["wymiar_znizki"] ?>"><?= htmlspecialchars($znizka["nazwa_znizki"]) ?></option>
                <?php endforeach; ?>
            </select>
                <br>
                <br>

                <h3>Wybór płatności:</h3>
            <select name="metoda_platnosci">
                <option value="karta">Karta płatnicza</option>
                <option value="blik">BLIK</option>
                <option value="google_pay">Google Pay</option>
                <option value="apple_pay">Apple Pay</option>
            </select><br><br>

            <h3>Cena końcowa:</h3>
            <?php 
                if($promo1>0){
                    echo "Zastosowano: PROMO$promo1<br>";
                }
            ?>
            <p><span id="cena_koncowa"><?= number_format($cena_klasa_1, 2) ?> PLN</span></p>

            <input type="submit" value="Kup bilet" />
        </form>
        </div>
    </div>
</body>
</html>