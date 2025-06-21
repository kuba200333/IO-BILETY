<?php
require_once "config.php";
require_once "class/RozkladJazdy.php";
require_once "class/Stacje.php";
require_once "class/Pociag.php";

session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Kierownik") {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$rozklad = new RozkladJazdy($db);

$pociagiObj = new Pociag($db);
$pociagi = $pociagiObj->pobierzPociagi();

$stacjeObj = new Stacje($db);
$stacje = $stacjeObj->pobierzWszystkie();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_pociagu"])) {
    $id_pociagu = $_POST["id_pociagu"];
    $entries = $_POST["entries"];

    foreach ($entries as $entry) {
        $rozklad->dodajRozklad($id_pociagu, $entry["id_stacji"], $entry["godzina_przyjazdu"], $entry["godzina_odjazdu"]);
    }

    header("Location: index.php");
    exit;
}
?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj rozkład jazdy</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function dodajWiersz() {
            let container = document.getElementById("entries");
            let index = container.children.length;
            let div = document.createElement("div");
            div.classList.add("entry-row");
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
                <button type="button" class="btn logout" onclick="this.parentElement.remove()">Usuń</button>
                <br><br>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Dodaj rozkład jazdy</h1>
        </div>
    </header>

    <nav class="container" style="margin-top: 1rem;">
        <a href="index.php" class="btn">Powrót do strony głównej</a>
    </nav>

    <main class="container">
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

            <button type="button" class="btn" onclick="dodajWiersz()">Dodaj kolejną stację</button>
            <br><br>

            <input type="submit" value="Zapisz rozkład">
        </form>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date("Y") ?> PolRail. Wszelkie prawa zastrzeżone.
        </div>
    </footer>
</body>
</html>
