<?php
require_once "config.php";
require_once "class/Sklady.php";
require_once "class/Pociag.php";

session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Kierownik") {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$sklad = new Sklady($db);

$wiadomosc = "";

$pociagObj = new Pociag($db);

$pociagi = $pociagObj->pobierzPociagi();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pociagu = $_POST["id_pociagu"];
    $nazwa_skladu = $_POST["nazwa_skladu"];

    if ($sklad->dodajSklad($id_pociagu, $nazwa_skladu)) {
        header("Location: index.php");
        exit;
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Dodaj skład pociągu</h1>
        </div>
    </header>

    <nav class="container" style="margin-top: 1rem;">
        <a href="index.php" class="btn">Powrót do strony głównej</a>
    </nav>

    <main class="container">
        <?php if ($wiadomosc): ?>
            <p><strong><?= htmlspecialchars($wiadomosc) ?></strong></p>
        <?php endif; ?>
        
        <form method="post">
            <label>Pociąg:</label>
            <select name="id_pociagu" required>
                <?php foreach ($pociagi as $pociag): ?>
                    <option value="<?= $pociag['id_pociagu'] ?>"><?= htmlspecialchars($pociag['numer_pociagu']) ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label>Nazwa składu:</label>
            <input type="text" name="nazwa_skladu" required>
            <br><br>

            <input type="submit" value="Dodaj skład">
        </form>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date("Y") ?> PolRail. Wszelkie prawa zastrzeżone.
        </div>
    </footer>
</body>
</html>
