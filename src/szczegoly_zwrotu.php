<?php
require_once "config.php";
require_once "class/Zwroty.php";
require_once "class/Pasazer.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_zwrotu"])) {
    $id_zwrotu = intval($_POST["id_zwrotu"]);

    try {
        $database = new Database();
        $db = $database->getConnection();
        $zwroty = new Zwroty($db);

        // Pobranie szczegółów zwrotu
        $query = "SELECT z.*, 
                         p.imie, p.nazwisko, p.email, p.telefon, p.adres, p.kod_pocztowy, p.miejscowosc 
                  FROM zwroty z 
                  JOIN pasazerowie p ON z.id_pasazera = p.id_pasazera 
                  WHERE z.id_zwrotu = :id_zwrotu";

        $stmt = $db->prepare($query);
        $stmt->bindParam(":id_zwrotu", $id_zwrotu, PDO::PARAM_INT);
        $stmt->execute();
        $zwrot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$zwrot) {
            echo "<p>Nie znaleziono zwrotu o podanym ID.</p>";
            exit;
        }

    } catch (Exception $e) {
        echo "<p>Błąd: " . $e->getMessage() . "</p>";
        exit;
    }

} else {
    echo "<p>Nieprawidłowe żądanie.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Szczegóły zwrotu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <a href="moje_zwroty.php">Moje zwroty</a>
    <h2>Dane pasażera</h2>
    <table>
        <tr><th>Imię i nazwisko</th><td><?= htmlspecialchars($zwrot['imie'] . ' ' . $zwrot['nazwisko']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($zwrot['email']) ?></td></tr>
        <tr><th>Telefon</th><td><?= htmlspecialchars($zwrot['telefon']) ?></td></tr>
        <tr><th>Adres</th><td><?= htmlspecialchars($zwrot['adres'] . ', ' . $zwrot['kod_pocztowy'] . ' ' . $zwrot['miejscowosc']) ?></td></tr>
    </table>

    <h2>Szczegóły zwrotu</h2>
    <table>
        <tr><th>ID Zwrotu</th><td><?= htmlspecialchars($zwrot['id_zwrotu']) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($zwrot['status']) ?></td></tr>
        <tr><th>Data zwrotu</th><td><?= htmlspecialchars($zwrot['data_zwrotu']) ?></td></tr>
        <tr><th>Numer konta</th><td><?= htmlspecialchars($zwrot['nr_konta']) ?></td></tr>
        <tr><th>Uwagi pasażera</th><td><?= htmlspecialchars($zwrot['uwagi_pasazer']) ?></td></tr>
        <tr><th>Uwagi pracownika</th><td><?= htmlspecialchars($zwrot['uwagi_pracownik']) ?></td></tr>
        <tr><th>ID Biletu</th><td><?= htmlspecialchars($zwrot['id_biletu']) ?></td></tr>
    </table>

</body>
</html>
