<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "Pasazer.php";
require_once "Bilet.php";

$database = new Database();
$db = $database->getConnection();

// Tworzymy obiekt pasażera na podstawie sesji
$pasazer = new Pasazer($db);
$pasazer->loadByLogin($_SESSION["user"]); // metoda ładuje dane pasażera z bazy

$bilet = new Bilet($db);
$bilety = $pasazer->getBilety(); // tablica asocjacyjna z biletami

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje bilety</title>
</head>
<body>
    <a href="index.php">Powrót do strony głównej</a>

    <h2>Twoje bilety</h2>
    <?php if (empty($bilety)): ?>
        <p>Brak zapisanych biletów.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Data podróży</th>
                <th>Pociąg</th>
                <th>Trasa</th>
                <th>Wagon/Miejsce</th>
                <th>Cena</th>
                <th>Szczegóły</th>
            </tr>
            <?php foreach ($bilety as $b): ?>
                <tr>                   
                    <td><?= htmlspecialchars($b['data_podrozy']) ?></td>
                    <td><?= htmlspecialchars($b['numer_pociagu']) ?></td>
                    <td><?= htmlspecialchars($b['stacja_start']) ?> → <?= htmlspecialchars($b['stacja_koniec']) ?></td>
                    <td><?= htmlspecialchars($b['wagon']) ?>/<?= htmlspecialchars($b['miejsce']) ?></td>
                    <td><?= number_format($b['cena'], 2) ?> zł</td>
                    <td>
                        <form action="pokaz_bilet.php" method="post" style="margin:0;">
                            <input type="hidden" name="id_biletu" value="<?= htmlspecialchars($b['id_biletu']) ?>">
                            <button type="submit">Pokaż bilet</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
