<?php
require_once "config.php";
require_once "class/Wagony.php";
require_once "class/Pociag.php";
require_once "class/Sklady.php";
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Kierownik") {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$wagon = new Wagony($db);

$wiadomosc = "";

$skladyObj = new Sklady($db);
$sklady = $skladyObj->pobierzSklady();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_skladu = $_POST["id_skladu"];
    $numer_wagonu = $_POST["numer_wagonu"];
    $typ = $_POST["typ"];
    $klasa = $_POST["klasa"];
    $liczba_miejsc = $_POST["liczba_miejsc"];
    $miejsce_od = $_POST["miejsce_od"];
    $miejsce_do = $_POST["miejsce_do"];

    if ($wagon->dodajWagon($id_skladu, $numer_wagonu, $typ, $klasa, $liczba_miejsc, $miejsce_od, $miejsce_do)) {
        header("Location: index.php");
        exit;
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Dodaj wagon do składu</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <nav>
                <a href="index.php" class="btn">Strona główna</a>
            </nav>
            <div class="hero">

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
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date('Y') ?> System Kolejowy. Wszelkie prawa zastrzeżone.
        </div>
    </footer>
</body>
</html>
