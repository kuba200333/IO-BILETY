<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "class/Pasazer.php";
require_once "class/Bilet.php";

$database = new Database();
$db = $database->getConnection();

$pasazer = new Pasazer($db);
$pasazer->loadByLogin($_SESSION["user"]);

$bilet = new Bilet($db);
$bilety = $pasazer->getBilety();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje bilety</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1>Moje bilety</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <a href="index.php" class="btn" style="margin-bottom: 1rem; display: inline-block;">Powrót do strony głównej</a>

            <?php if (empty($bilety)): ?>
                <p>Brak zapisanych biletów.</p>
            <?php else: ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Data podróży</th>
                            <th>Pociąg</th>
                            <th>Trasa</th>
                            <th>Wagon/Miejsce</th>
                            <th>Cena</th>
                            <th>Status</th>
                            <th>Szczegóły</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bilety as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['data_podrozy']) ?></td>
                                <td><?= htmlspecialchars($b['numer_pociagu']) ?></td>
                                <td><?= htmlspecialchars($b['stacja_start']) ?> → <?= htmlspecialchars($b['stacja_koniec']) ?></td>
                                <td><?= htmlspecialchars($b['wagon']) ?>/<?= htmlspecialchars($b['miejsce']) ?></td>
                                <td><?= number_format($b['cena'], 2) ?> zł</td>
                                <td><?= htmlspecialchars($b['status_biletu']) ?></td>
                                <td>
                                    <?php if ($b['status_biletu'] !== 'Zwrócony'): ?>
                                        <form action="pokaz_bilet.php" method="post" style="margin:0;">
                                            <input type="hidden" name="id_biletu" value="<?= htmlspecialchars($b['id_biletu']) ?>">
                                            <button type="submit" class="btn">Pokaż bilet</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date('Y') ?> PolRail
        </div>
    </footer>
</body>
</html>
