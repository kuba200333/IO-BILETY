<?php
session_start();
require_once "config.php";
require_once "class/Pasazer.php";
require_once "class/Zwroty.php";

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$pasazer = new Pasazer($db);
$pasazer->loadByLogin($_SESSION["user"]);
$bilety = $pasazer->getBilety();

$zwroty = new Zwroty($db);
$komunikat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_biletu = (int)$_POST["id_biletu"];
    $nr_konta = trim($_POST["nr_konta"]);
    $zgoda = isset($_POST["zgoda"]);
    $uwagi_pasazer = trim($_POST["uwagi_pasazer"]);
    $typ = trim($_POST["typ"]);

    if (!$zgoda) {
        $komunikat = "<span class='error'>Musisz zaakceptować potrącenie 15% wartości biletu.</span>";
    } elseif (!preg_match('/^\d{26}$/', $nr_konta)) {
        $komunikat = "<span class='error'>Numer konta musi zawierać dokładnie 26 cyfr.</span>";
    } elseif (!$zwroty->czyBiletNalezyDoPasazera($id_biletu, $bilety)) {
        $komunikat = "<span class='error'>Wybrany bilet nie należy do Ciebie.</span>";
    } elseif ($zwroty->czyZwrotIstnieje($id_biletu)) {
        $komunikat = "<span class='error'>Zwrot dla tego biletu już został zgłoszony.</span>";
    } else {
        if ($zwroty->dodajZwrot($id_biletu, $pasazer->id, $nr_konta, $uwagi_pasazer, $typ)) {
            header("Location: moje_zwroty.php");
            exit;
        } else {
            $komunikat = "<span class='error'>Błąd podczas składania wniosku. Spróbuj ponownie.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Zwrot biletu</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1>Zwrot biletu</h1>
    </header>
    <main class="container">
        <a href="moje_zwroty.php" class="btn" style="margin-bottom: 1rem; display: inline-block;">Powrót do moich zwrotów</a>
        <h2>Formularz zwrotu biletu</h2>

        <?= $komunikat ?>

        <form method="POST" novalidate>
            <label for="id_biletu">Wybierz bilet:</label>
            <select name="id_biletu" id="id_biletu" required>
                <?php foreach ($bilety as $bilet): ?>
                    <?php if ($bilet["status_biletu"] !== "Zwrócony"): ?>
                        <option value="<?= htmlspecialchars($bilet["id_biletu"]) ?>">
                            <?= htmlspecialchars("Bilet #{$bilet["id_biletu"]}: {$bilet["stacja_start"]} ➔ {$bilet["stacja_koniec"]} ({$bilet["data_podrozy"]})") ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>

            <label for="typ">Wybierz typ zwrotu:</label>
            <select name="typ" id="typ" required>
                <option>Zwrot należności za niewykorzystany bilet</option>
                <option>Wezwanie do zapłaty</option>
                <option>Bilet z opłatą dodatkową wystawiony w pociągu</option>
                <option>Odszkodowanie z tytułu opóźnienia pociągu</option>
                <option>Dodatkowe koszty podróży</option>
                <option>Skargi</option>
                <option>Wnioski</option>
            </select>

            <label for="nr_konta">Numer konta bankowego (26 cyfr):</label>
            <input type="text" name="nr_konta" id="nr_konta" pattern="\d{26}" required placeholder="np. 12345678901234567890123456" maxlength="26" />

            <label for="uwagi_pasazer">Treść zgłoszenia:</label>
            <input type="text" name="uwagi_pasazer" id="uwagi_pasazer" required />

            <label>
                <input type="checkbox" name="zgoda" required />
                Jestem świadomy, że zostanie potrącone 15% wartości biletu
            </label>

            <button type="submit">Wyślij wniosek</button>
        </form>
    </main>
    <footer>
        &copy; <?= date("Y") ?> PolRail
    </footer>
</body>
</html>
