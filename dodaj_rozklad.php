<?php
require_once "config.php";
require_once "Rozklad.php";

$database = new Database();
$db = $database->getConnection();
$rozklad = new Rozklad($db);

$pociagi = $rozklad->pobierzPociagi();
$stacje = $rozklad->pobierzStacje();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_pociagu"])) {
    $id_pociagu = $_POST["id_pociagu"];
    $entries = $_POST["entries"];

    foreach ($entries as $entry) {
        $rozklad->dodajRozklad($id_pociagu, $entry["id_stacji"], $entry["godzina_przyjazdu"], $entry["godzina_odjazdu"]);
    }
    echo "<p>Dodano rozkład jazdy!</p>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj rozkład jazdy</title>
    <script>
        function dodajWiersz() {
            let container = document.getElementById("entries");
            let index = container.children.length;
            let div = document.createElement("div");
            div.innerHTML = `
                <label>Stacja:</label>
                <select name="entries[${index}][id_stacji]" required>
                    <?php foreach ($stacje as $stacja): ?>
                        <option value="<?= $stacja['id_stacji'] ?>"><?= $stacja['nazwa'] ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Godzina przyjazdu:</label>
                <input type="time" name="entries[${index}][godzina_przyjazdu]" >
                <label>Godzina odjazdu:</label>
                <input type="time" name="entries[${index}][godzina_odjazdu]" >
                <button type="button" onclick="this.parentElement.remove()">Usuń</button>
                <br><br>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <h2>Dodaj rozkład jazdy</h2>
    <form method="post">
        <label>Wybierz pociąg:</label>
        <select name="id_pociagu" required>
            <?php foreach ($pociagi as $pociag): ?>
                <option value="<?= $pociag['id_pociagu'] ?>">
                    <?= htmlspecialchars($pociag['numer_pociagu'] . " - " . $pociag['nazwa']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <div id="entries"></div>

        <button type="button" onclick="dodajWiersz()">Dodaj kolejną stację</button>
        <br><br>

        <input type="submit" value="Zapisz rozkład">
    </form>
</body>
</html>
