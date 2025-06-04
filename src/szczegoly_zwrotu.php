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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Szczegóły zwrotu</h1>
        </div>
    </header>

    <main class="container">
        <a href="moje_zwroty.php" class="btn" style="margin-bottom: 1rem; display: inline-block;">« Powrót do moich zwrotów</a>

        <section class="hero">
            <h2>Dane pasażera</h2>
            <table class="styled-table">
                <tbody>
                    <tr><th>Imię i nazwisko</th><td><?= htmlspecialchars($zwrot['imie'] . ' ' . $zwrot['nazwisko']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($zwrot['email']) ?></td></tr>
                    <tr><th>Telefon</th><td><?= htmlspecialchars($zwrot['telefon']) ?></td></tr>
                    <tr><th>Adres</th><td><?= htmlspecialchars($zwrot['adres'] . ', ' . $zwrot['kod_pocztowy'] . ' ' . $zwrot['miejscowosc']) ?></td></tr>
                </tbody>
            </table>
        </section>

        <section class="hero" style="margin-top: 2rem;">
            <h2>Szczegóły zwrotu</h2>
            <table class="styled-table">
                <tbody>
                    <tr><th>ID Zwrotu</th><td><?= htmlspecialchars($zwrot['id_zwrotu']) ?></td></tr>
                    <tr><th>Status</th><td><?= htmlspecialchars($zwrot['status']) ?></td></tr>
                    <tr><th>Data zwrotu</th><td><?= htmlspecialchars($zwrot['data_zwrotu']) ?></td></tr>
                    <tr><th>Numer konta</th><td><?= htmlspecialchars($zwrot['nr_konta']) ?></td></tr>
                    <tr><th>Uwagi pasażera</th><td><?= nl2br(htmlspecialchars($zwrot['uwagi_pasazer'])) ?></td></tr>
                    <tr><th>Uwagi pracownika</th><td><?= nl2br(htmlspecialchars($zwrot['uwagi_pracownik'])) ?></td></tr>
                    <tr><th>ID Biletu</th><td><?= htmlspecialchars($zwrot['id_biletu']) ?></td></tr>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <div class="container">
            &copy; <?= date("Y") ?> PolRail - System Zwrotów Biletów
        </div>
    </footer>
</body>
</html>
