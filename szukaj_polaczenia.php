<?php
session_start();
require_once "config.php";
require_once "Wyszukiwarka.php";

$database = new Database();
$db = $database->getConnection();
$wyszukiwarka = new Wyszukiwarka($db);

// Pobranie listy stacji
$stacje_query = $db->query("SELECT id_stacji, nazwa FROM stacje ORDER BY nazwa ASC");
$stacje = $stacje_query->fetchAll(PDO::FETCH_ASSOC);

// Domyślna data i godzina (dzisiejszy dzień i aktualna godzina)
$dzisiejsza_data = date("Y-m-d");
$aktualna_godzina = date("H:i");

$wyniki = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stacja_start = $_POST["stacja_start"];
    $stacja_koniec = $_POST["stacja_koniec"];
    $data = $_POST["data"];
    $godzina = !empty($_POST["godzina"]) ? $_POST["godzina"] : $aktualna_godzina;
    $typy_pociagow = isset($_POST["typ_pociagu"]) ? $_POST["typ_pociagu"] : [];

    if (strtotime($data) < strtotime(date("Y-m-d"))) {
        echo "<p style='color:red;'>Nie możesz wyszukiwać połączeń w przeszłości!</p>";
    } else {
        $wyniki = $wyszukiwarka->znajdzPolaczenia($stacja_start, $stacja_koniec, $data, $godzina, $typy_pociagow);
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wyszukiwanie połączeń</title>
</head>
<body>
    <h2>Wyszukiwarka połączeń</h2>
    <form method="post">
        <label>Ze stacji:</label>
        <select name="stacja_start" required>
            <?php foreach ($stacje as $stacja): ?>
                <option value="<?= $stacja['id_stacji'] ?>"><?= $stacja['nazwa'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Do stacji:</label>
        <select name="stacja_koniec" required>
            <?php foreach ($stacje as $stacja): ?>
                <option value="<?= $stacja['id_stacji'] ?>"><?= $stacja['nazwa'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Data:</label>
        <input type="date" name="data" value="<?= $dzisiejsza_data ?>" required><br><br>

        <label>Godzina:</label>
        <input type="time" name="godzina" value="<?= $aktualna_godzina ?>"><br><br>

        <label>Typ pociągu:</label><br>
        <input type="checkbox" name="typ_pociagu[]" value="TLK"> TLK
        <input type="checkbox" name="typ_pociagu[]" value="IC"> IC
        <input type="checkbox" name="typ_pociagu[]" value="EIC"> EIC
        <input type="checkbox" name="typ_pociagu[]" value="EIP"> EIP
        <br><br>

        <input type="submit" value="Szukaj">
    </form>

    <?php if (!empty($wyniki)): ?>
    <h3>Wyniki wyszukiwania:</h3>
    <table border="1">
        <tr>
            <th>Pociąg</th>
            <th>Typ</th>
            <th>Ze stacji</th>
            <th>Do stacji</th>
            <th>Godzina odjazdu</th>
            <th>Akcja</th>
        </tr>
        <?php foreach ($wyniki as $pociag): ?>
            <tr>
                <td><?= htmlspecialchars($pociag["numer_pociagu"]) ?></td>
                <td><?= htmlspecialchars($pociag["typ"]) ?></td>
                <td><?= htmlspecialchars($pociag["stacja_pocz"]) ?></td>
                <td><?= htmlspecialchars($pociag["stacja_konc"]) ?></td>
                <td><?= htmlspecialchars($pociag["godzina_wyjazdu"]) ?></td>
                <td>
                    <form method="post" action="kup_bilet.php">
                        <input type="hidden" name="numer_pociagu" value="<?= htmlspecialchars($pociag["numer_pociagu"]) ?>">
                        <input type="hidden" name="stacja_start" value="<?= htmlspecialchars($pociag["stacja_pocz"]) ?>">
                        <input type="hidden" name="stacja_koniec" value="<?= htmlspecialchars($pociag["stacja_konc"]) ?>">
                        <input type="submit" value="Kup bilet">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
