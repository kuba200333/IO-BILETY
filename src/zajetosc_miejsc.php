<?php
require_once "config.php";
require_once "class/Bilet.php";
require_once "class/Wagony.php";

$database = new Database();
$db = $database->getConnection();
$biletObj = new Bilet($db);
$wagonyObj = new Wagony($db);

// Dane z formularza
// $numer_pociagu = $_POST['numer_pociagu'] ?? '';
// $data_podrozy = $_POST['data_podrozy'] ?? date('Y-m-d');
// $id_stacji_start = $_POST['id_stacji_start'] ?? '';
// $id_stacji_koniec = $_POST['id_stacji_koniec'] ?? '';

$numer_pociagu = 81170;
$data_podrozy = date('Y-m-d');
$id_stacji_start = 1;
$id_stacji_koniec = 2;

$zajete = [];

if ($numer_pociagu && $data_podrozy && $id_stacji_start && $id_stacji_koniec) {
    $zajete = $wagonyObj->pobierzZajeteMiejscaNaOdcinku($numer_pociagu, $data_podrozy, $id_stacji_start, $id_stacji_koniec);
}

function pobierzPociagi($db) {
    $stmt = $db->query("SELECT numer_pociagu FROM pociagi ORDER BY numer_pociagu");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function pobierzStacje($db) {
    $stmt = $db->query("SELECT id_stacji, nazwa FROM stacje ORDER BY nazwa");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pociagi = pobierzPociagi($db);
$stacje = pobierzStacje($db);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zajętość miejsc</title>
    <style>
        .schemat { margin-top: 30px; }
        .miejsce {
            display: inline-block;
            width: 30px;
            height: 30px;
            margin: 4px;
            border: 1px solid black;
            text-align: center;
            line-height: 30px;
            border-radius: 5px;
        }
        .zajete { background-color: red; color: white; }
        .wolne { background-color: green; color: white; }
    </style>
</head>
<body>
    <h2>Sprawdź zajętość miejsc</h2>
    <form method="POST">
        <label>Pociąg:</label>
        <select name="numer_pociagu" required>
            <option value="">-- wybierz --</option>
            <?php foreach ($pociagi as $numer): ?>
                <option value="<?= $numer ?>" <?= $numer === $numer_pociagu ? 'selected' : '' ?>><?= $numer ?></option>
            <?php endforeach; ?>
        </select>

        <label>Data podróży:</label>
        <input type="date" name="data_podrozy" value="<?= htmlspecialchars($data_podrozy) ?>" required>

        <label>Stacja początkowa:</label>
        <select name="id_stacji_start" required>
            <option value="">-- wybierz --</option>
            <?php foreach ($stacje as $s): ?>
                <option value="<?= $s['id_stacji'] ?>" <?= $s['id_stacji'] == $id_stacji_start ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nazwa']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Stacja końcowa:</label>
        <select name="id_stacji_koniec" required>
            <option value="">-- wybierz --</option>
            <?php foreach ($stacje as $s): ?>
                <option value="<?= $s['id_stacji'] ?>" <?= $s['id_stacji'] == $id_stacji_koniec ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nazwa']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Pokaż zajętość</button>
    </form>

    <?php if (!empty($zajete)): ?>
        <div class="schemat">
            <h3>Schemat zajętości:</h3>
            <?php
            $grupowanie = [];
            foreach ($zajete as $m) {
                $grupowanie[$m['numer_wagonu']][] = $m['miejsce'];
            }

            foreach ($grupowanie as $wagon => $miejsca) {
                echo "<h4>Wagon $wagon</h4>";
                for ($i = 1; $i <= 40; $i++) {
                    $klasa = in_array($i, array_map('intval', $miejsca)) ? 'zajete' : 'wolne';
                    echo "<div class='miejsce $klasa'>$i</div>";
                    if ($i % 10 === 0) echo "<br>";
                }
            }
            ?>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p><strong>Brak zajętych miejsc na tym odcinku w wybranym dniu.</strong></p>
    <?php endif; ?>
</body>
</html>
