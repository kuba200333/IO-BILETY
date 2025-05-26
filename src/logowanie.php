<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit;
}

require_once "User.php";

$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["rola"];
    $user = new User($db, $role);

    $login = htmlspecialchars(strip_tags($_POST["login"]));
    $haslo = $_POST["haslo"];

    if ($user->login($login, $haslo)) {
        header("Location: dashboard.php");
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
