<?php
session_start();

// Przekierowanie na podstawie zapisanej roli w sesji, jeśli użytkownik jest zalogowany
if (isset($_SESSION["user"]) && isset($_SESSION["role"])) {
    if ($_SESSION["role"] === "pasazer") {
        header("Location: index_pasazer.php");
        exit();
    } elseif ($_SESSION["role"] === "Pracownik") {
        header("Location: index_pracownik.php");
        exit();
    } elseif ($_SESSION["role"] === "Kierownik") {
        header("Location: index_kierownik.php");
        exit();
    } elseif ($_SESSION["role"] === "Administrator") {
        header("Location: index_admin.php");
        exit();
    } else {
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
</head>
<body>
    <h1>Witamy w systemie sprzedaży biletów InterTicket</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Zalogowany jako: <?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['role']; ?>)</p>
        <a href="wyloguj.php">Wyloguj</a>
    <?php else: ?>
        <a href="logowanie.php"><button>Zaloguj się</button></a>
        <a href="rejestracja.php"><button>Zarejestruj się</button></a>
    <?php endif; ?>
</body>
</html>
