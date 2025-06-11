<?php
// BLOK 1: Inicjalizacja sesji i załadowanie zależności
session_start();
require_once "config.php";
require_once "class/Zwroty.php";
require_once "class/Pracownik.php";

// BLOK 2: Sprawdzenie logowania
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

// BLOK 3: Połączenie z bazą danych i inicjalizacja obiektów
$database = new Database();
$db = $database->getConnection();
$pracownikObj = new Pracownik($db);
$zwrotyObj = new Zwroty($db);

// BLOK 4: Pobranie ID pracownika
$login = $_SESSION["user"];
$id_pracownika = $pracownikObj->getIdByLogin($login);

if (!$id_pracownika) {
    echo "Nie znaleziono pracownika.";
    exit;
}

// BLOK 5: Pobranie zwrotów przypisanych do pracownika
$zwroty = $zwrotyObj->getZwrotyByPracownikId($id_pracownika);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zwroty przypisane do pracownika</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Zwroty przypisane do pracownika</h1>
        </header>

        <main>
            <a href="index.php" class="btn">Powrót do strony głównej</a>
            <div class="hero">
                <h2>Zwroty przypisane do Ciebie</h2>

                <?php if (count($zwroty) === 0): ?>
                    <p>Brak przypisanych zwrotów.</p>
                <?php else: ?>
                    <table class="styled-table">
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
                                            <button type="submit" class="btn">Szczegóły</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; <?= date("Y") ?> System zwrotów biletów | PolRail</p>
        </footer>
    </div>
</body>
</html>
