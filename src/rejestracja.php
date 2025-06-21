<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

require_once "class/Pasazer.php";
require_once "config.php";

$database = new Database();
$db = $database->getConnection();
$user = new Pasazer($db, "pasazer");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = htmlspecialchars(strip_tags($_POST["imie"]));
    $nazwisko = htmlspecialchars(strip_tags($_POST["nazwisko"]));
    $login = htmlspecialchars(strip_tags($_POST["login"]));
    $haslo = $_POST["haslo"];
    $telefon = $_POST["telefon"];
    $email = $_POST['email'];
    $kod_pocztowy = $_POST['kod_pocztowy'];
    $adres = $_POST['adres'];
    $miejscowosc = $_POST['miejscowosc'];

    if ($user->register($imie, $nazwisko, $login, $haslo, $telefon, $email, $kod_pocztowy, $adres, $miejscowosc)) {
        header("Location: index.php");
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>Błąd podczas rejestracji.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h2 style="text-align: center;">Rejestracja pasażera</h2>
        </div>
    </header>
    
    <nav class="container" style="margin-top: 1rem;">
        <a href="index.php" class="btn">Strona główna</a>
    </nav>
    <form method="post">
        <div>
            <label>Imię:</label>
            <input type="text" name="imie" required>
        </div>

        <div>
            <label>Nazwisko:</label>
            <input type="text" name="nazwisko" required>
        </div>

        <div>
            <label>Ulica, numer domu:</label>
            <input type="text" name="adres" required>
        </div>

        <div>
            <label>Miejscowość:</label>
            <input type="text" name="miejscowosc" required>
        </div>

        <div>
            <label>Kod pocztowy:</label>
            <input type="text" name="kod_pocztowy" required>
        </div>

        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Numer telefonu:</label>
            <input type="text" name="telefon" required pattern="[0-9]{9}" title="Podaj 9-cyfrowy numer telefonu">
        </div>

        <div>
            <label>Login:</label>
            <input type="text" name="login" required>
        </div>

        <div>
            <label>Hasło:</label>
            <input type="password" name="haslo" required>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <input type="submit" value="Zarejestruj się">
        </div>
    </form>
    <footer>
        <div class="container">
            <p>&copy; 2025 InterTicket. Wszelkie prawa zastrzeżone.</p>
        </div>
    </footer>
</body>
</html>
