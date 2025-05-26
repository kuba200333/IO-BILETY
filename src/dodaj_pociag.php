<?php
require_once "config.php";
require_once "Pociag.php";
session_start();



$database = new Database();
$db = $database->getConnection();
$pociag = new Pociag($db);

$wiadomosc = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numer_pociagu = $_POST["numer_pociagu"];
    $typ = $_POST["typ"];
    $nazwa = $_POST["nazwa"];
    $od = $_POST["od"];
    $do = $_POST["do"];

    if ($pociag->dodajPociag($numer_pociagu, $typ, $nazwa, $od, $do)) {
        $wiadomosc = "Pociąg został dodany pomyślnie!";
    } else {
        $wiadomosc = "Błąd podczas dodawania pociągu.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj pociąg</title>
</head>
<body>
    <h2>Dodaj nowy pociąg do rozkładu</h2>
    <?php if ($wiadomosc): ?>
        <p><strong><?= $wiadomosc ?></strong></p>
    <?php endif; ?>
    
    <form method="post">
        <label>Numer pociągu:</label>
        <input type="text" name="numer_pociagu" required><br><br>

        <label>Typ pociągu:</label>
        <select name="typ">
            <option value="IC">InterCity</option>
            <option value="TLK">Twoje Linie Kolejowe</option>
            <option value="EIC">Express InterCity</option>
            <option value="EIP">Express InterCity Premium</option>
        </select><br><br>

        <label>Nazwa pociągu:</label>
        <input type="text" name="nazwa" ><br><br>

        <label>Data od:</label>
        <input type="date" name="od" required><br><br>

        <label>Data do:</label>
        <input type="date" name="do" required><br><br>

        <input type="submit" value="Dodaj pociąg">
    </form>
</body>
</html>
