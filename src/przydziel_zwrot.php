<?php

require_once 'config.php';       
require_once 'class/Zwroty.php';
require_once 'class/Pracownik.php';

$database = new Database();
$db = $database->getConnection();

$zwrotyObj = new Zwroty($db);
$pracownikObj = new Pracownik($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_zwrotu'], $_POST['id_pracownika'])) {
    $id_zwrotu = (int)$_POST['id_zwrotu'];
    $id_pracownika = (int)$_POST['id_pracownika'];

    if ($zwrotyObj->przypiszPracownikaDoZwrotu($id_zwrotu, $id_pracownika)) {
        header("Location: przydziel_zwrot.php");
        exit();
    } else {
        $error = "Nie udało się przypisać pracownika do zwrotu.";
    }
}

$zwroty = $zwrotyObj->getZwrotyBezPracownika();
$pracownicy = $pracownikObj->getAllPracownicy();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Przydziel pracownika do zwrotu</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <div class="container">
            <h1>Przydziel pracownika do zwrotu</h1>
        </div>
    </header>

    <main>
        <section class="container">
            <a href="index.php" class="btn" style="margin-bottom: 1rem; display: inline-block;">Powrót do strony głównej</a>
            <?php if (!empty($error)): ?>
                <p style="color: red; margin-bottom: 1rem;"><?=htmlspecialchars($error)?></p>
            <?php endif; ?>

            <?php if (count($zwroty) === 0): ?>
                <p>Brak zwrotów bez przypisanego pracownika.</p>
            <?php else: ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID zwrotu</th>
                            <th>ID biletu</th>
                            <th>ID pasażera</th>
                            <th>Status</th>
                            <th>Data zwrotu</th>
                            <th>Relacja</th>
                            <th>Przypisz pracownika</th>
                            <th>Akcja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zwroty as $zwrot): ?>
                            <tr>
                                <td><?=htmlspecialchars($zwrot['id_zwrotu'])?></td>
                                <td><?=htmlspecialchars($zwrot['id_biletu'])?></td>
                                <td><?=htmlspecialchars($zwrot['id_pasazera'])?></td>
                                <td><?=htmlspecialchars($zwrot['status'])?></td>
                                <td><?=htmlspecialchars($zwrot['data_zwrotu'])?></td>
                                <td><?=htmlspecialchars($zwrot['relacja'])?></td>
                                <td>
                                    <form method="post" action="przydziel_zwrot.php">
                                        <input type="hidden" name="id_zwrotu" value="<?=htmlspecialchars($zwrot['id_zwrotu'])?>" />
                                        <select name="id_pracownika" required>
                                            <option value="">wybierz</option>
                                            <?php foreach ($pracownicy as $pracownik): ?>
                                                <option value="<?=htmlspecialchars($pracownik['id_pracownika'])?>">
                                                    <?=htmlspecialchars($pracownik['nazwisko'] . ' ' . $pracownik['imie'])?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" class="btn">Przypisz</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            &copy; <?=date('Y')?> System zwrotów PolRail
        </div>
    </footer>
</body>
</html>
