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

require_once "class/Pasazer.php";
require_once "class/Pracownik.php";
require_once "config.php";

$database = new Database();
$db = $database->getConnection();

$blad_logowania = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["rola"];
    $user = ($role === "pracownik") ? new Pracownik($db) : new Pasazer($db);
    $login = htmlspecialchars(strip_tags($_POST["login"]));
    $haslo = $_POST["haslo"];

    if ($user->login($login, $haslo)) {
        switch ($_SESSION["role"]) {
            case "pasazer": header("Location: index_pasazer.php"); break;
            case "Pracownik": header("Location: index_pracownik.php"); break;
            case "Kierownik": header("Location: index_kierownik.php"); break;
            case "Administrator": header("Location: index_admin.php"); break;
            default: header("Location: dashboard.php");
        }
        exit;
    } else {
        $blad_logowania = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>InterTicket</h1>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <h2>Logowanie</h2>

            <?php if ($blad_logowania): ?>
                <p style="color: red; font-weight: bold;">Nieprawidłowy login lub hasło.</p>
            <?php endif; ?>

            <form method="post" class="login-form">
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" name="login" id="login" required>
                </div>

                <div class="form-group">
                    <label for="haslo">Hasło:</label>
                    <input type="password" name="haslo" id="haslo" required>
                </div>

                <div class="form-group">
                    <label>Rola:</label><br>
                    <label><input type="radio" name="rola" value="pasazer" checked> Pasażer</label>
                    <label><input type="radio" name="rola" value="pracownik"> Pracownik</label>
                </div>

                <input type="submit" value="Zaloguj się" class="btn">
            </form>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 InterTicket. Wszelkie prawa zastrzeżone.</p>
        </div>
    </footer>
</body>
</html>
