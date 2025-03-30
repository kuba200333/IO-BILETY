<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Administrator") {
    die("Dostęp zabroniony.");
}
?>

<?php
require_once "Pracownik.php";

$database = new Database();
$db = $database->getConnection();
$pracownik = new Pracownik($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pracownik->imie = htmlspecialchars(strip_tags($_POST["imie"]));
    $pracownik->nazwisko = htmlspecialchars(strip_tags($_POST["nazwisko"]));
    $pracownik->stanowisko = htmlspecialchars(strip_tags($_POST["stanowisko"]));
    $pracownik->login = htmlspecialchars(strip_tags($_POST["login"]));
    $pracownik->haslo = $_POST["haslo"]; 

    if ($pracownik->dodajPracownika()) {
        echo "Pracownik został dodany pomyślnie!";
    } else {
        echo "Wystąpił błąd podczas dodawania pracownika.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj pracownika</title>
</head>
<body>
    <h2>Dodaj nowego pracownika</h2>
    <form method="post">
        <label>Imię:</label>
        <input type="text" name="imie" required><br><br>
        
        <label>Nazwisko:</label>
        <input type="text" name="nazwisko" required><br><br>
        
        <label>Stanowisko:</label>
        <select name="stanowisko">
            <option value="Administrator">Administrator</option>
            <option value="Kontroler">Kontroler</option>
        </select><br><br>

        <label>Login:</label>
        <input type="text" name="login" required><br><br>

        <label>Hasło:</label>
        <input type="password" name="haslo" required><br><br>

        <input type="submit" value="Dodaj pracownika">
    </form>
</body>
</html>
