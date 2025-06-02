<?php
session_start();
require_once "config.php";
require_once "class/Zwroty.php";
require_once "class/Pracownik.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$pracownikObj = new Pracownik($db);
$zwrotyObj = new Zwroty($db);

$login = $_SESSION["user"];
$id_pracownika = $pracownikObj->getIdByLogin($login);

if (!$id_pracownika) {
    echo "Nie znaleziono pracownika.";
    exit;
}

$zwroty = $zwrotyObj->getZwrotyByPracownikId($id_pracownika);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zwroty przypisane do pracownika</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Zwroty przypisane do Ciebie</h2>

    <?php if (count($zwroty) === 0): ?>
        <p>Brak przypisanych zwrotów.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Numer Zwrotu</th>
                    <th>Numer Biletu</th>
                    <th>Pasażer</th>
                    <th>Status</th>
                    <th>Data Zwrotu</th>
                    <th>Relacja</th>
                    <th>Uwagi Pasażera</th>
                    <th>Uwagi Pracownika</th>
                    <th>Szczegóły</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($zwroty as $zwrot): ?>
                    <tr>
                        <td><?= htmlspecialchars($zwrot["id_zwrotu"]) ?></td>
                        <td><?= htmlspecialchars($zwrot["id_biletu"]) ?></td>
                        <td><?= htmlspecialchars($zwrot["pasazer"] ?? '') ?></td>
                        <td><?= htmlspecialchars($zwrot["status"]) ?></td>
                        <td><?= htmlspecialchars($zwrot["data_zwrotu"]) ?></td>
                        <td><?= htmlspecialchars($zwrot["relacja"]) ?></td>
                        <td><?= htmlspecialchars($zwrot["uwagi_pasazer"]) ?></td>
                        <td><?= htmlspecialchars($zwrot["uwagi_pracownik"]) ?></td>
                        <td>
                            <form method="POST" action="szczegoly_zwrotu_pracownik.php">
                                <input type="hidden" name="id_zwrotu" value="<?= $zwrot['id_zwrotu'] ?>">
                                <button type="submit">Szczegóły</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
