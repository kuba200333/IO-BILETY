<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

require_once "config.php";
require_once "class/Pasazer.php";
require_once "class/Zwroty.php";

$database = new Database();
$db = $database->getConnection();

$pasazer = new Pasazer($db);
$pasazer->loadByLogin($_SESSION["user"]); // metoda ładuje dane pasażera z bazy

$zwroty = new Zwroty($db);
$listaZwrotow = $zwroty->getZwrotyByPasazerId($pasazer->id);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje Zwroty</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        form { display: inline; }
    </style>
</head>
<body>
    <h2>Moje Zwroty</h2>

    <table>
        <thead>
            <tr>
                <th>ID Zwrotu</th>
                <th>ID Biletu</th>
                <th>Status</th>
                <th>Data Zwrotu</th>
                <th>ID Pracownika</th>
                <th>Szczegóły</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaZwrotow as $zwrot): ?>
                <tr>
                    <td><?= htmlspecialchars($zwrot["id_zwrotu"]) ?></td>
                    <td><?= htmlspecialchars($zwrot["id_biletu"]) ?></td>
                    <td><?= htmlspecialchars($zwrot["status"]) ?></td>
                    <td><?= htmlspecialchars($zwrot["data_zwrotu"]) ?></td>
                    <td><?= htmlspecialchars($zwrot["id_pracownika"]) ?></td>
                    <td>
                        <form method="POST" action="szczegoly_zwrotu.php">
                            <input type="hidden" name="id_zwrotu" value="<?= $zwrot["id_zwrotu"] ?>">
                            <button type="submit">Szczegóły</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <form action="formularz_zwrotu.php" method="GET">
        <button type="submit">Nowy zwrot</button>
    </form>
</body>
</html>
