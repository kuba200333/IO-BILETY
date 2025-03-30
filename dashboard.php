<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: logowanie.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel użytkownika</title>
</head>
<body>
    <h1>Witaj, <?php echo $_SESSION["user"]; ?>!</h1>
    <p>Twoja rola: <?php echo $_SESSION["role"]; ?></p>
    <a href="wyloguj.php">Wyloguj się</a>
</body>
</html>
