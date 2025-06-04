<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

require_once "class/Pasazer.php";

$database = new Database();
$db = $database->getConnection();
$user = new Pasazer($db, "pasazer");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = htmlspecialchars(strip_tags($_POST["imie"]));
    $nazwisko = htmlspecialchars(strip_tags($_POST["nazwisko"]));
    $login = htmlspecialchars(strip_tags($_POST["login"]));
    $haslo = $_POST["haslo"];
    $telefon= $_POST["telefon"];
    $email= $_POST['email'];
    $kod_pocztowy= $_POST['kod_pocztowy'];
    $adres= $_POST['adres'];
    $miejscowosc= $_POST['miejscowosc'];

    if ($user->register($imie, $nazwisko, $login, $haslo, $telefon, $email, $kod_pocztowy, $adres, $miejscowosc)) {
        header("Location: index.php");
        echo "Rejestracja zakończona sukcesem! <a href='logowanie.php'>Zaloguj się</a>";
    } else {
        echo "Błąd podczas rejestracji.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
</head>
<body>
    <h2>Rejestracja pasażera</h2>
    <form method="post">
        <label>Imię:</label>
        <input type="text" name="imie" required><br><br>

        <label>Nazwisko:</label>
        <input type="text" name="nazwisko" required><br><br>

        <label>Ulica, numer domu:</label>
        <input type="text" name="adres" required><br><br>

        <label>Miejscowość:</label>
        <input type="text" name="miejscowosc" required><br><br>

        <label>Kod pocztowy:</label>
        <input type="text" name="kod_pocztowy" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Numer telefonu:</label>
        <input type="number" name="telefon" required><br><br>

        <label>Login:</label>
        <input type="text" name="login" required><br><br>

        <label>Hasło:</label>
        <input type="password" name="haslo" required><br><br>

        <input type="submit" value="Zarejestruj się">
    </form>
</body>
</html>