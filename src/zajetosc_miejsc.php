<?php
require_once "config.php";
require_once "class/Bilet.php";
require_once "class/Wagony.php";
require_once "class/Stacje.php";
require_once "class/Pociag.php";

$database = new Database();
$db = $database->getConnection();
$biletObj = new Bilet($db);
$wagonyObj = new Wagony($db);
$stacjeObj = new Stacje($db);
$pociagiObj = new Pociag($db);

$numer_pociagu = $_POST['numer_pociagu'] ?? '';
$data_podrozy = $_POST['data_podrozy'] ?? date('Y-m-d');
$id_stacji_start = $_POST['id_stacji_start'] ?? '';
$id_stacji_koniec = $_POST['id_stacji_koniec'] ?? '';
$klasa = $_POST['klasa'] ?? '';

$zajete = [];
$wagony = [];

if ($numer_pociagu && $data_podrozy && $id_stacji_start && $id_stacji_koniec && $klasa) {
    $zajete = $wagonyObj->pobierzZajeteMiejscaNaOdcinku($numer_pociagu, $data_podrozy, $id_stacji_start, $id_stacji_koniec);
    $wagony = $wagonyObj->pobierzWagonyDlaPociagu($numer_pociagu, $klasa);
}

$pociagi =$pociagiObj-> pobierzPociagi($db);
$stacje = $stacjeObj-> pobierzWszystkie($db);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zajętość miejsc - wybór wagonu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Dodatkowy CSS dla widoku wagonów */
        .kontener-wagonow {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding: 5px;
            margin-top: 1rem;
        }
        .wagon {
            background: #fff;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            min-width: 220px;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .wagon-nazwa {
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }
        .miejsca {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: center;
        }
        .miejsce {
            width: 35px;
            height: 35px;
            background-image: url('image/zielone_miejsce.png');
            background-size: cover;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .miejsce.wybrane {
            background-image: url('image/pomaranczowe_miejsce.png');
            color: white;
        }
        .miejsce.zajete {
            background-image: url('image/szare_miejsce.png');
            pointer-events: none;
        }
        form {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        form label {
            display: block;
            margin-top: 1rem;
            margin-bottom: 0.3rem;
            font-weight: bold;
        }
        form select, form input[type="date"] {
            width: 100%;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        form button {
            margin-top: 1rem;
        }
        .info {
            margin-top: 1rem;
            background-color: #f8d7da;
            color: #842029;
            padding: 0.75rem;
            border-radius: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <h1>System Rezerwacji - Zajętość Miejsc</h1>
</header>

<main class="container">
    <a href="index.php" class="btn" style="margin-bottom: 1rem; display: inline-block;">Powrót do strony głównej</a>
    <section class="hero">
        <h2>Sprawdź zajętość miejsc</h2>
        <form method="POST">
            <label>Pociąg:</label>
            <select name="numer_pociagu" required>
                <option value="">-- wybierz --</option>
                <?php foreach ($pociagi as $p): ?>
                    <option value="<?= htmlspecialchars($p['numer_pociagu']) ?>" <?= $p['numer_pociagu'] == $numer_pociagu ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['typ']) ?> <?= htmlspecialchars($p['numer_pociagu']) ?> - <?= htmlspecialchars($p['nazwa']) ?>
                    </option>
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

            <label>Klasa wagonu:</label>
            <select name="klasa" required>
                <option value="">-- wybierz --</option>
                <option value="1" <?= $klasa == '1' ? 'selected' : '' ?>>1 klasa</option>
                <option value="2" <?= $klasa == '2' ? 'selected' : '' ?>>2 klasa</option>
                <option value="3" <?= $klasa == '3' ? 'selected' : '' ?>>Sypialny</option>
            </select>

            <button type="submit" class="btn">Pokaż zajętość</button>
        </form>
    </section>

    <?php if ($wagony): ?>
    <div class="kontener-wagonow">
    <?php foreach ($wagony as $wagon): ?>
        <div class="wagon">
            <div class="wagon-nazwa">
                Wagon <?= htmlspecialchars($wagon['numer_wagonu']) ?> (<?= htmlspecialchars($wagon['klasa']) ?> klasa)
            </div>
            <div class="miejsca" style="display: grid; grid-template-rows: repeat(4, auto); grid-auto-flow: column; gap: 5px;">
                <?php
                    $zajete_miejsca = array_column(array_filter($zajete, function ($m) use ($wagon) {
                        return $m['numer_wagonu'] == $wagon['numer_wagonu'];
                    }), 'miejsce');
                    $zajete_miejsca = array_map('intval', $zajete_miejsca);
                    $liczba_miejsc = $wagon['liczba_miejsc'] ?? 40; // domyślnie 40 jeśli brak w bazie
                ?>
                <?php for ($i = 1; $i <= $liczba_miejsc; $i++): ?>
                    <div class="miejsce <?= in_array($i, $zajete_miejsca) ? 'zajete' : '' ?>"
                         data-nr="<?= $i ?>"
                         data-wagon="<?= htmlspecialchars($wagon['numer_wagonu']) ?>"
                         onclick="wybierzMiejsce('<?= htmlspecialchars($wagon['numer_wagonu']) ?>', <?= $i ?>, this)">
                        <?= $i ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="info"><strong>Brak dostępnych wagonów lub miejsc dla wybranej klasy.</strong></div>
    <?php endif; ?>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> PolRail. Wszelkie prawa zastrzeżone.</p>
    </div>
</footer>
</body>
</html>
