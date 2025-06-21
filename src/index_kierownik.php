<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "Kierownik") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel kierownika</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            max-width: 800px;
            margin: auto;
            padding: 2rem;
            text-align: center;
        }

        .welcome {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card a {
            display: block;
            color: #333;
            font-weight: bold;
            text-decoration: none;
        }

        .logout {
            margin-top: 2rem;
            background-color: #F4F6F8;
        }

        .logout a {
            background-color: #0f62fe;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.2s ease-in-out;
        }

        .logout:hover {
            background-color: #F4F6F8;
        }

    </style>
</head>
<body>
    <header>
        <h1>InterTicket</h1>
    </header>
    <div class="dashboard">
        <p class="welcome">Witaj, <strong><?php echo $_SESSION['user']; ?></strong>! (<?php echo $_SESSION['role']; ?>)</p>

        <div class="grid">
            <div class="card">
                <a href="przydziel_zwrot.php">🔄 Przydziel zwrot</a>
            </div>
            <div class="card">
                <a href="statystyki_pracownikow.php">📊 Statystyki pracowników</a>
            </div>
            <div class="card">
                <a href="dodaj_pociag.php">🚝 Dodaj pociąg</a>
            </div>
            <div class="card">
                <a href="dodaj_rozklad.php"> 📅 Dodaj rozkład do pociągu</a>
            </div>
            <div class="card">
                <a href="dodaj_sklad.php">↔️ Dodaj skład do pociągu</a>
            </div>

            <div class="card">
                <a href="dodaj_wagon.php"> 🚋 Dodaj wagon do składu</a>
            </div>
            <div class="card">
                <a href="https://www.sejm.gov.pl/prawo/konst/polski/kon1.htm">📄 Regulamin</a>
            </div>
        </div>

        <div class="logout">
            <a href="wyloguj.php">🚪 Wyloguj się</a>
        </div>

        <footer>
            <div class="container">
                <p>&copy; 2025 InterTicket. Wszelkie prawa zastrzeżone.</p>
            </div>
        </footer>
    </div>
</body>
</html>
