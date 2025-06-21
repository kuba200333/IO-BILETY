<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "class/RozkladJazdy.php";
require_once "class/Stacje.php";

$database = new Database();
$db = $database->getConnection();

$rozklad = new RozkladJazdy($db);
$stacjeHandler = new Stacje($db);

// Pobranie listy stacji
$stacje = $stacjeHandler->pobierzWszystkie();

$dzisiejsza_data = date("Y-m-d");
$aktualna_godzina = date("H:i");
$wyniki = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stacja_start = $_POST["stacja_start"];
    $stacja_koniec = $_POST["stacja_koniec"];
    $data_podrozy  = $_POST["data"];
    $godzina = !empty($_POST["godzina"]) ? $_POST["godzina"] : $aktualna_godzina;
    $typy_pociagow = $_POST["typ_pociagu"] ?? [];

    if (strtotime($data_podrozy) < strtotime(date("Y-m-d"))) {
        echo "<p style='color:red; text-align:center; margin-top:1rem;'>Nie możesz wyszukiwać połączeń w przeszłości!</p>";
    } else {
        $wyniki = $rozklad->znajdzPolaczenia($stacja_start, $stacja_koniec, $data_podrozy, $godzina, $typy_pociagow);
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Wyszukiwanie połączeń</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1>Wyszukiwanie połączeń kolejowych</h1>
    </header>

    <main class="container">
        <a href="index.php" class="btn" style="margin-bottom:1rem; display:inline-block;">Powrót do strony głównej</a>

        <form method="post" novalidate>
            <label for="stacja_start">Ze stacji:</label>
            <select id="stacja_start" name="stacja_start" required>
                <?php foreach ($stacje as $stacja): ?>
                    <option value="<?= $stacja['id_stacji'] ?>" <?= (isset($stacja_start) && $stacja_start == $stacja['id_stacji']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($stacja['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="stacja_koniec">Do stacji:</label>
            <select id="stacja_koniec" name="stacja_koniec" required>
                <?php foreach ($stacje as $stacja): ?>
                    <option value="<?= $stacja['id_stacji'] ?>" <?= (isset($stacja_koniec) && $stacja_koniec == $stacja['id_stacji']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($stacja['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="data">Data:</label>
            <input type="date" id="data" name="data" value="<?= isset($data_podrozy) ? htmlspecialchars($data_podrozy) : $dzisiejsza_data ?>" min="<?= date('Y-m-d') ?>" required>

            <label for="godzina">Godzina:</label>
            <input type="time" id="godzina" name="godzina" value="<?= isset($godzina) ? htmlspecialchars($godzina) : $aktualna_godzina ?>">

            <fieldset style="margin-top:1rem;">
                <legend>Typ pociągu:</legend>
                <?php
                $typy = ["TLK", "IC", "EIC", "EIP"];
                foreach ($typy as $typ):
                    $checked = (!isset($typy_pociagow) || in_array($typ, $typy_pociagow)) ? 'checked' : '';
                ?>
                    <label>
                        <input type="checkbox" name="typ_pociagu[]" value="<?= $typ ?>" <?= $checked ?>> <?= $typ ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>

            <input type="submit" value="Szukaj" style="margin-top:1rem;">
        </form>

        <?php if (!empty($wyniki)): ?>
            <h2 style="margin-top:2rem;">Wyniki wyszukiwania:</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Pociąg</th>
                        <th>Typ</th>
                        <th>Ze stacji</th>
                        <th>Do stacji</th>
                        <th>Godzina odjazdu</th>
                        <th>Akcja</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($wyniki as $pociag): ?>
                    <tr>
                        <td><?= htmlspecialchars($pociag["numer_pociagu"]) ?></td>
                        <td><?= htmlspecialchars($pociag["typ"]) ?> <?= htmlspecialchars($pociag["nazwa"]) ?></td>
                        <td><?= htmlspecialchars($pociag["stacja_pocz"]) ?></td>
                        <td><?= htmlspecialchars($pociag["stacja_konc"]) ?></td>
                        <td><?= htmlspecialchars($pociag["godzina_wyjazdu"]) ?></td>
                        <td>
                            <form method="post" action="kup_bilet.php">
                                <input type="hidden" name="numer_pociagu" value="<?= htmlspecialchars($pociag["numer_pociagu"]) ?>">
                                <input type="hidden" name="stacja_start" value="<?= htmlspecialchars($pociag["stacja_pocz"]) ?>">
                                <input type="hidden" name="stacja_koniec" value="<?= htmlspecialchars($pociag["stacja_konc"]) ?>">
                                <input type="hidden" name="data_podrozy" value="<?= htmlspecialchars($data_podrozy) ?>">
                                <input type="submit" value="Kup bilet" class="btn">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p style="text-align:center; margin-top:1rem;">Brak wyników dla podanych kryteriów.</p>
        <?php endif; ?>
    </main>

    <footer>
        &copy; <?= date("Y") ?> InterTicket
    </footer>
</body>
</html>
