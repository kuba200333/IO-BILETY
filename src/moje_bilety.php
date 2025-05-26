<?php
require_once "config.php";

session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION["user"])) {
    die("Musisz być zalogowany, aby zobaczyć swoje bilety.");
}

$database = new Database();
$db = $database->getConnection();

$login = $_SESSION["user"];

// Pobranie ID pasażera na podstawie loginu
$query_pasazer = "SELECT id_pasazera FROM pasazerowie WHERE login = :login";
$stmt = $db->prepare($query_pasazer);
$stmt->bindParam(":login", $login, PDO::PARAM_STR);
$stmt->execute();
$row_pasazer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row_pasazer) {
    die("Błąd: Nie znaleziono pasażera w bazie.");
}

$id_pasazera = $row_pasazer["id_pasazera"];

// Pobranie biletów pasażera
$query_bilety = "SELECT bilety.id_biletu, pociagi.numer_pociagu, st1.nazwa AS stacja_start, st2.nazwa AS stacja_koniec,
                        bilety.miejsce, bilety.cena, bilety.data_podrozy, bilety.id_wagonu 
                 FROM bilety 
                 JOIN pociagi ON bilety.id_pociagu = pociagi.id_pociagu 
                 JOIN stacje st1 ON bilety.id_stacji_start = st1.id_stacji 
                 JOIN stacje st2 ON bilety.id_stacji_koniec = st2.id_stacji 
                 WHERE bilety.id_pasazera = :id_pasazera";
$stmt = $db->prepare($query_bilety);
$stmt->bindParam(":id_pasazera", $id_pasazera, PDO::PARAM_INT);
$stmt->execute();
$bilety = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje Bilety</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Moje Bilety</h2>
    <table>
        <tr>
            <th>ID Biletu</th>
            <th>Numer Pociągu</th>
            <th>Stacja Początkowa</th>
            <th>Stacja Końcowa</th>
            <th>Miejsce</th>
            <th>Wagon</th>
            <th>Cena</th>
            <th>Data Podróży</th>
            <th>Akcja</th>
        </tr>
        <?php foreach ($bilety as $bilet): ?>
        <tr>
            <td><?= htmlspecialchars($bilet["id_biletu"]) ?></td>
            <td><?= htmlspecialchars($bilet["numer_pociagu"]) ?></td>
            <td><?= htmlspecialchars($bilet["stacja_start"]) ?></td>
            <td><?= htmlspecialchars($bilet["stacja_koniec"]) ?></td>
            <td><?= htmlspecialchars($bilet["miejsce"]) ?></td>
            <td><?= htmlspecialchars($bilet["id_wagonu"]) ?></td>
            <td><?= htmlspecialchars($bilet["cena"]) ?> PLN</td>
            <td><?= htmlspecialchars($bilet["data_podrozy"]) ?></td>
            <td>
                <a href="pokaz_bilet.php?id_biletu=<?= $bilet['id_biletu'] ?>">Pokaż bilet</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
