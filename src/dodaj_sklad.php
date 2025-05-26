<?php
require_once "config.php";
require_once "Sklad.php";
session_start();

// Sprawdzenie, czy użytkownik jest administratorem
// if (!isset($_SESSION["rola"]) || $_SESSION["rola"] !== "Administrator") {
//     die("Brak dostępu! Ta funkcja jest dostępna tylko dla administratora.");
// }

$database = new Database();
$db = $database->getConnection();
$sklad = new Sklad($db);

$wiadomosc = "";
$pociagi = $sklad->pobierzPociagi();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pociagu = $_POST["id_pociagu"];
    $nazwa_skladu = $_POST["nazwa_skladu"];

    if ($sklad->dodajSklad($id_pociagu, $nazwa_skladu)) {
        $wiadomosc = "Skład został dodany pomyślnie!";
    } else {
        $wiadomosc = "Błąd podczas dodawania składu.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj skład pociągu</title>
</head>
<body>
    <h2>Dodaj skład pociągu</h2>
    <?php if ($wiadomosc): ?>
        <p><strong><?= $wiadomosc ?></strong></p>
    <?php endif; ?>
    
    <form method="post">
        <label>Pociąg:</label>
        <select name="id_pociagu" required>
            <?php foreach ($pociagi as $pociag): ?>
                <option value="<?= $pociag['id_pociagu'] ?>"><?= htmlspecialchars($pociag['numer_pociagu']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Nazwa składu:</label>
        <input type="text" name="nazwa_skladu" required><br><br>

        <input type="submit" value="Dodaj skład">
    </form>
</body>
</html>
