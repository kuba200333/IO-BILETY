<?php
require_once "config.php";
require_once "class/SkanowanieBiletow.php";

session_start();
$database = new Database();
$db = $database->getConnection();

$skanowanieObj = new SkanowanieBiletow($db);

$data = $_GET['data'] ?? date('Y-m-d');
$statystyki = [];

if (!empty($data)) {
    $statystyki = $skanowanieObj->getStatystykiPracownikow($data);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki Pracowników</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="container">
        <h1>System Sprzedaży Biletów</h1>
    </div>
</header>

<main class="container">
    <section class="hero">
        <a class="btn" href="index.php">Strona główna</a><br><br>
        <h2>Statystyki pracowników - zeskanowane bilety</h2>

        <form method="get">
            <label for="data">Wybierz datę:</label>
            <input type="date" name="data" id="data" value="<?= htmlspecialchars($data) ?>"><br><br>
            <button type="submit">Pokaż statystyki</button>
        </form>

        <?php if (!empty($statystyki)): ?>
            <table class="styled-table" style="margin-top:1rem;">
                <thead>
                    <tr>
                        <th>Pracownik</th>
                        <th>Ilość zeskanowanych biletów</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statystyki as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['pracownik']) ?></td>
                            <td><?= htmlspecialchars($row['ilosc_zeskanowanych']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="margin-top:1rem;">Brak skanowań dla wybranej daty.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <div class="container">
        &copy; <?= date("Y") ?> System Sprzedaży Biletów PolRail
    </div>
</footer>

</body>
</html>
