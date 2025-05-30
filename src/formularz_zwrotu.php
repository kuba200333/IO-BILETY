<?php
session_start();
require_once "config.php";
require_once "class/Pasazer.php";

if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "pasazer") {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$pasazer = new Pasazer($db);
$pasazer->loadByLogin($_SESSION["user"]);
$bilety = $pasazer->getBilety();

$komunikat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_biletu = (int)$_POST["id_biletu"];
    $nr_konta = trim($_POST["nr_konta"]);
    $zgoda = isset($_POST["zgoda"]);

    if (!$zgoda) {
        $komunikat = "<span class='error'>Musisz zaakceptować potrącenie 15% wartości biletu.</span>";
    } elseif (!preg_match('/^\d{26}$/', $nr_konta)) {
        $komunikat = "<span class='error'>Numer konta musi zawierać dokładnie 26 cyfr.</span>";
    } else {
        // sprawdzenie czy bilet należy do użytkownika
        $bilet_ok = false;
        foreach ($bilety as $b) {
            if ($b["id_biletu"] == $id_biletu) {
                $bilet_ok = true;
                break;
            }
        }

        if (!$bilet_ok) {
            $komunikat = "<span class='error'>Wybrany bilet nie należy do Ciebie.</span>";
        } else {
            // sprawdź, czy bilet już zgłoszony
            $check = $db->prepare("SELECT * FROM zwroty WHERE id_biletu = :id_biletu");
            $check->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
            $check->execute();

            if ($check->rowCount() > 0) {
                $komunikat = "<span class='error'>Zwrot dla tego biletu już został zgłoszony.</span>";
            } else {
                // zapis do bazy
                $stmt = $db->prepare("INSERT INTO zwroty (id_biletu, id_pasazera, status, data_zwrotu, nr_konta) 
                                        VALUES (:id_biletu, :id_pasazera, 'oczekujący', NOW(), :nr_konta)");
                $stmt->bindParam(":id_biletu", $id_biletu, PDO::PARAM_INT);
                $stmt->bindParam(":id_pasazera", $pasazer->id, PDO::PARAM_INT);
                $stmt->bindParam(":nr_konta", $nr_konta);

                if ($stmt->execute()) {
                    $komunikat = "<span style='color:green;'>Wniosek o zwrot został złożony.</span>";
                } else {
                    $komunikat = "<span class='error'>Błąd podczas składania wniosku. Spróbuj ponownie.</span>";
                }
            }
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zwrot biletu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        label, input, select { display: block; margin: 10px 0; width: 100%; max-width: 400px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Formularz zwrotu biletu</h2>

    <?= $komunikat ?>

    <form method="POST">
        <label for="id_biletu">Wybierz bilet:</label>
        <select name="id_biletu" id="id_biletu" required>
            <?php foreach ($bilety as $bilet): ?>
                <?php if (strtotime($bilet["data_podrozy"]) <> time()): ?>
                    <option value="<?= $bilet["id_biletu"] ?>">
                        <?= "Bilet #{$bilet["id_biletu"]}: {$bilet["stacja_start"]} ➔ {$bilet["stacja_koniec"]} ({$bilet["data_podrozy"]})" ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <label for="nr_konta">Numer konta bankowego (26 cyfr):</label>
        <input type="text" name="nr_konta" id="nr_konta" pattern="\d{26}" required placeholder="np. 12345678901234567890123456">

        <label>
            <input type="checkbox" name="zgoda" required>
            Jestem świadomy, że zostanie potrącone 15% wartości biletu
        </label>

        <button type="submit">Wyślij wniosek</button>
    </form>
</body>
</html>
