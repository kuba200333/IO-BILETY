<?php
require_once "config.php";
require_once "class/Pociag.php";
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Kierownik") {
    header("Location: index.php");
    exit;
}

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
            header("Location: index.php");
            exit;
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>System Kolejowy</h1>
        </div>
    </header>

    <nav class="container" style="margin-top: 1rem;">
        <a href="index.php" class="btn">Strona główna</a>
    </nav>

    <main>
        <section class="container">
            <div class="hero">
                <h2>Dodaj nowy pociąg do rozkładu</h2>

                <?php if ($wiadomosc): ?>
                    <p><strong><?= $wiadomosc ?></strong></p>
                <?php endif; ?>

                <form method="post">
                    <label for="numer_pociagu">Numer pociągu:</label>
                    <input type="text" id="numer_pociagu" name="numer_pociagu" required>

                    <label for="typ">Typ pociągu:</label>
                    <select id="typ" name="typ" required>
                        <option value="IC">InterCity</option>
                        <option value="TLK">Twoje Linie Kolejowe</option>
                        <option value="EIC">Express InterCity</option>
                        <option value="EIP">Express InterCity Premium</option>
                    </select>

                    <label for="nazwa">Nazwa pociągu (opcjonalnie):</label>
                    <input type="text" id="nazwa" name="nazwa">

                    <label for="od">Data od:</label>
                    <input type="date" id="od" name="od" required>

                    <label for="do">Data do:</label>
                    <input type="date" id="do" name="do" required><br><br>

                    <input type="submit" value="Dodaj pociąg">
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date("Y") ?> System Kolejowy | Wszelkie prawa zastrzeżone
        </div>
    </footer>
</body>
</html>