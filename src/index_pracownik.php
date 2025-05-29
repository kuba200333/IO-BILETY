<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Pracownik") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Zalogowany jako: <?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['role']; ?>)</p>
    <a href="wyloguj.php">Wyloguj</a>
</body>
</html>