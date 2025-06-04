<?php
session_start();

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
    <title>InterTicket</title>
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
            <h2>Witamy w systemie sprzedaży biletów InterTicket</h2>
            <?php if (isset($_SESSION['user'])): ?>
                <p>Zalogowany jako: <strong><?php echo $_SESSION['user']; ?></strong> (<?php echo $_SESSION['role']; ?>)</p>
                <a class="btn logout" href="wyloguj.php">Wyloguj</a>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="logowanie.php" class="btn">Zaloguj się</a>
                    <a href="rejestracja.php" class="btn">Zarejestruj się</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 InterTicket. Wszelkie prawa zastrzeżone.</p>
        </div>
    </footer>
</body>
</html>
