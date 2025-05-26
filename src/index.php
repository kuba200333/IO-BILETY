<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
</head>
<body>
    <h1>Witamy w systemie rezerwacji PKP Intercity</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Zalogowany jako: <?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['role']; ?>)</p>
        <a href="dashboard.php">Przejdź do panelu</a> |
        <a href="wyloguj.php">Wyloguj</a>
    <?php else: ?>
        <a href="logowanie.php"><button>Zaloguj się</button></a>
        <a href="rejestracja.php"><button>Zarejestruj się</button></a>
    <?php endif; ?>
</body>
</html>
