<?php
session_start();
require_once "config.php";
require_once "class/Zwroty.php";
require_once "class/Pasazer.php";

if (!isset($_SESSION["user"])) {
    header("Location: zwroty.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$zwrotyObj = new Zwroty($db);
$pasazerObj = new Pasazer($db);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_zwrotu"]) && isset($_POST["status"]) && isset($_POST["uwagi_pracownik"])) {
    // Obsługa zapisu zmian
    $id_zwrotu = $_POST["id_zwrotu"];
    $status = $_POST["status"];
    $uwagi_pracownik = $_POST["uwagi_pracownik"];

    $updated = $zwrotyObj->updateZwrot($id_zwrotu, $status, $uwagi_pracownik);

    if ($updated) {
        echo "<p style='color:green;'>Dane zwrotu zostały zaktualizowane.</p>";
        header("Location: zwroty_pracownik.php");
    } else {
        echo "<p style='color:red;'>Wystąpił błąd podczas aktualizacji danych.</p>";
    }
} elseif (isset($_POST["id_zwrotu"])) {
    $id_zwrotu = $_POST["id_zwrotu"];
} else {
    header("Location: zwroty.php");
    exit;
}

$zwrot = $zwrotyObj->getZwrotById($id_zwrotu);

if (!$zwrot) {
    echo "Nie znaleziono zwrotu.";
    exit;
}

$pasazer = $pasazerObj->getPasazerById($zwrot["id_pasazera"]);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Szczegóły Zwrotu</title>
</head>
<body>
    <a href="zwroty_pracownik.php">Powrót do listy zwrotów</a>
    <h2>Szczegóły Zwrotu #<?= htmlspecialchars($zwrot["id_zwrotu"]) ?></h2>

    <table border="1" cellpadding="6">
        <tr><th>Bilet</th><td><?= htmlspecialchars($zwrot["id_biletu"]) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($zwrot["status"]) ?></td></tr>
        <tr><th>Data Zwrotu</th><td><?= htmlspecialchars($zwrot["data_zwrotu"]) ?></td></tr>
        <tr><th>Relacja</th><td><?= htmlspecialchars($zwrot["relacja"]) ?></td></tr>
        <tr><th>Uwagi Pasażera</th><td><?= htmlspecialchars($zwrot["uwagi_pasazer"]) ?></td></tr>
        <tr><th>Uwagi Pracownika</th><td><?= htmlspecialchars($zwrot["uwagi_pracownik"]) ?></td></tr>
    </table>

    <h3>Dane Pasażera</h3>
    <table border="1" cellpadding="6">
        <tr><th>Imię i nazwisko</th><td><?= htmlspecialchars($pasazer["imie"] . " " . $pasazer["nazwisko"]) ?></td></tr>
        <tr><th>Adres</th><td><?= htmlspecialchars($pasazer["adres"]) ?></td></tr>
        <tr><th>Miejscowość</th><td><?= htmlspecialchars($pasazer["miejscowosc"]) ?></td></tr>
        <tr><th>Kod pocztowy</th><td><?= htmlspecialchars($pasazer["kod_pocztowy"]) ?></td></tr>
        <tr><th>Numer telefonu</th><td><?= htmlspecialchars($pasazer["telefon"]) ?></td></tr>
    </table>

    <h3>Aktualizuj dane</h3>
    <form method="POST" action="">
        <input type="hidden" name="id_zwrotu" value="<?= htmlspecialchars($zwrot["id_zwrotu"]) ?>">
        <label>Uwagi pracownika:<br>
            <textarea name="uwagi_pracownik" rows="4" cols="50"><?= htmlspecialchars($zwrot["uwagi_pracownik"]) ?></textarea>
        </label><br><br>
        <label>Status zwrotu:<br>
            <select name="status">
                <option value="oczekujący" <?= $zwrot["status"] === "oczekujący" ? "selected" : "" ?>>oczekujący</option>
                <option value="zaakceptowany" <?= $zwrot["status"] === "zaakceptowany" ? "selected" : "" ?>>zaakceptowany</option>
                <option value="odrzucony" <?= $zwrot["status"] === "odrzucony" ? "selected" : "" ?>>odrzucony</option>
                <option value="do uzupełnienia" <?= $zwrot["status"] === "do uzupełnienia" ? "selected" : "" ?>>do uzupełnienia</option>
            </select>
        </label><br><br>
        <button type="submit">Zapisz zmiany</button>
    </form>
</body>
</html>
