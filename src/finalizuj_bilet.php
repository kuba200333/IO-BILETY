<?php
require_once "config.php";
require_once "class/Bilet.php";
require_once "class/Transakcja.php"; 
require_once "class/Pociag.php";
require_once "class/Pasazer.php";

session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "kup_bilet.php") === false) {
    header("Location: index.php");
    exit;
}


$database = new Database();
$db = $database->getConnection();
$biletObj  = new Bilet($db);
$transakcjaObj = new Transakcja($db);
$pasazerObj= new Pasazer($db);


$id_pasazera = $pasazerObj->getIdByLogin($_SESSION["user"]);

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

    $id_pociagu = 0;

    $kod_qr = md5(uniqid(rand(), true));

    $oplata_dodatkowa=0;
    $id_biletu = $biletObj->dodajDaneBiletu(
        $id_pasazera,
        $numer_pociagu,
        $id_stacji_start,
        $id_stacji_koniec,
        $miejsce,
        $cena,
        $data_podrozy,
        $kod_qr,
        $wagon,
        $id_znizki,
        $oplata_dodatkowa
    );

    $transakcjaDodana = $transakcjaObj->dodajTransakcje(
        $id_biletu,
        $cena,
        $metoda_platnosci,
        $id_pasazera 
    );

    //echo "<p>Zakup zakończony! Twój bilet został zapisany.</p>";
    echo '
    <form id="redirectForm" action="pokaz_bilet.php" method="post">
        <input type="hidden" name="id_biletu" value="' . htmlspecialchars($id_biletu) . '">
    </form>
    <script>
        document.getElementById("redirectForm").submit();
    </script>';
    exit;

}
?>
