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
$pasazer->loadByLogin($_SESSION["user"]);

$zwroty = new Zwroty($db);
$listaZwrotow = $zwroty->getZwrotyByPasazerId($pasazer->id);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Moje Zwroty</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1 class="container" style="margin-top: 1rem;">Moje Zwroty</h1>
    </header>

    <main class="container">
        <a href="index.php" class="btn" style="margin-bottom: 1rem; display: inline-block;">Powrót do strony głównej</a>
        <section class="zwroty-lista">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Numer Zwrotu</th>
                        <th>Numer Biletu</th>
                        <th>Relacja</th>
                        <th>Status</th>
                        <th>Data Zwrotu</th>
                        <th>Szczegóły</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaZwrotow as $zwrot): ?>
                        <tr>
                            <td><?= htmlspecialchars($zwrot["id_zwrotu"]) ?></td>
                            <td><?= htmlspecialchars($zwrot["id_biletu"]) ?></td>
                            <td><?= htmlspecialchars($zwrot["relacja"]) ?></td>
                            <td><?= htmlspecialchars($zwrot["status"]) ?></td>
                            <td><?= htmlspecialchars($zwrot["data_zwrotu"]) ?></td>
                            <td>
                                <form method="POST" action="szczegoly_zwrotu.php" style="margin: 0;">
                                    <input type="hidden" name="id_zwrotu" value="<?= $zwrot["id_zwrotu"] ?>" />
                                    <button type="submit" class="btn" style="padding: 0.3rem 0.75rem; font-size: 0.9rem;">Szczegóły</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="nowy-zwrot" style="margin-top: 20px; text-align: center;">
            <form action="formularz_zwrotu.php" method="GET">
                <button type="submit" class="btn" style="font-weight: bold; font-size: 1rem;">Nowy zwrot</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date('Y') ?> PolRail
        </div>
    </footer>
</body>
</html>
