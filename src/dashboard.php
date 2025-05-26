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
    
    <?php
        if($_SESSION["role"]!="pasazer"){
            echo"<a href='dodaj_pociag.php'>Dodaj pociąg</a><br>";
            echo"<a href='dodaj_pracownika.php'>Dodaj pracownika</a><br>";
            echo"<a href='dodaj_rozklad.php'>Dodaj rozklad</a><br>";
            echo"<a href='dodaj_sklad.php'>Dodaj skład pociągu</a><br>";
            echo"<a href='dodaj_wagon.php'>Dodaj wagony do składu</a><br>";
            echo"<a href='sprawdz_miejsca_graficznie.php'>Sprawdź zajętość miejsc</a><br>";
            echo"<a href='weryfikacja.php'>Weryfikacja biletu</a><br>";
        }else{
            echo"<a href='moje_bilety.php'>Moje bilety</a><br>";
            echo"<a href='szukaj_polaczenia.php'>Szukaj połączenia</a><br>";
        }
    ?>
	<br>
    <a href="wyloguj.php">Wyloguj się</a>
</body>
</html>
