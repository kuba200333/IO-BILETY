<?php
session_start();
if (isset($_SESSION["user"])) {
    if ($_SESSION["role"] === "pasazer") {
        header("Location: index_pasazer.php");
    } elseif ($_SESSION["role"] === "Pracownik") {
        header("Location: index_pracownik.php");
    } elseif ($_SESSION["role"] === "Kierownik") {
        header("Location: index_kierownik.php");
    } elseif ($_SESSION["role"] === "Administrator") {
        header("Location: index_admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

require_once "Pasazer.php";
require_once "Pracownik.php";
require_once "config.php";

$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["rola"];

    if ($role === "pracownik") {
        $user = new Pracownik($db);
    } else {
        $user = new Pasazer($db);
    }

    $login = htmlspecialchars(strip_tags($_POST["login"]));
    $haslo = $_POST["haslo"];

    if ($user->login($login, $haslo)) {
        // Przekierowanie na podstawie zapisanej roli w sesji
        if ($_SESSION["role"] === "pasazer") {
            header("Location: index_pasazer.php");
        } elseif ($_SESSION["role"] === "Pracownik") {
            header("Location: index_pracownik.php");
        } elseif ($_SESSION["role"] === "Kierownik") {
            header("Location: index_kierownik.php");
        } elseif ($_SESSION["role"] === "Administrator") {
            header("Location: index_admin.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        echo "Nieprawidłowy login lub hasło.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
</head>
<body>
    <h2>Logowanie</h2>
    <form method="post">
        <label>Login:</label>
        <input type="text" name="login" required><br><br>

        <label>Hasło:</label>
        <input type="password" name="haslo" required><br><br>

        <label>Rola:</label>
        <input type="radio" name="rola" value="pasazer" checked> Pasażer
        <input type="radio" name="rola" value="pracownik"> Pracownik<br><br>

        <input type="submit" value="Zaloguj się">
    </form>
</body>
</html>
