<?php
require_once "config.php";
require_once "Wagon.php";
session_start();

// Sprawdzenie, czy użytkownik jest administratorem
// if (!isset($_SESSION["rola"]) || $_SESSION["rola"] !== "admin") {
//     die("Brak dostępu! Ta funkcja jest dostępna tylko dla administratora.");
// }

$database = new Database();
$db = $database->getConnection();
$wagon = new Wagon($db);

$wiadomosc = "";
$sklady = $wagon->pobierzSklady();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_skladu = $_POST["id_skladu"];
    $numer_wagonu = $_POST["numer_wagonu"];
    $typ = $_POST["typ"];
    $klasa = $_POST["klasa"];
    $liczba_miejsc = $_POST["liczba_miejsc"];
    $miejsce_od = $_POST["miejsce_od"];
    $miejsce_do = $_POST["miejsce_do"];

    if ($wagon->dodajWagon($id_skladu, $numer_wagonu, $typ, $klasa, $liczba_miejsc, $miejsce_od, $miejsce_do)) {
        $wiadomosc = "Wagon został dodany pomyślnie!";
    } else {
        $wiadomosc = "Błąd podczas dodawania wagonu.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj wagon</title>
</head>
<body>
    <h2>Dodaj wagon do składu</h2>
    <?php if ($wiadomosc): ?>
        <p><strong><?= $wiadomosc ?></strong></p>
    <?php endif; ?>
    
    <form method="post">
    <label>Skład:</label>
    <select name="id_skladu" required>
        <?php foreach ($sklady as $sklad): ?>
            <option value="<?= $sklad['id_skladu'] ?>"><?= htmlspecialchars($sklad['nazwa_skladu']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Numer wagonu:</label>
    <input type="number" name="numer_wagonu" required><br><br>

    <label>Typ wagonu:</label>
    <select name="typ">
        <option value="Sypialny">Sypialny</option>
        <option value="Przedziałowy">Przedziałowy</option>
        <option value="Bezprzedziałowy">Bezprzedziałowy</option>
        <option value="Restauracyjny">Restauracyjny</option>
    </select><br><br>

    <label>Klasa:</label>
    <input type="number" name="klasa" required><br><br>

    <label>Liczba miejsc:</label>
    <input type="number" name="liczba_miejsc" required><br><br>

    <label>Miejsce od:</label>
    <input type="number" name="miejsce_od" required><br><br>

    <label>Miejsce do:</label>
    <input type="number" name="miejsce_do" required><br><br>

    <input type="submit" value="Dodaj wagon">
</form>

</body>
</html>
