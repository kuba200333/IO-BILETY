<?php
require_once "config.php";

$database = new Database();
$db = $database->getConnection();

// Pobranie listy pociągów do selecta
$query_pociagi = "SELECT id_pociagu, numer_pociagu FROM pociagi ORDER BY numer_pociagu";
$stmt = $db->prepare($query_pociagi);
$stmt->execute();
$pociagi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sprawdzenie, czy wysłano formularz
$miejsca_zajete = [];
$miejsca_wolne = [];
$wagony = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pociagu = $_POST["id_pociagu"];
    $data_podrozy = $_POST["data_podrozy"];

    // Pobranie wszystkich miejsc w pociągu (z tabeli `wagony`)
    $query_wagony = "SELECT w.id_wagonu, w.numer_wagonu, w.miejsce_od, w.miejsce_do 
                     FROM wagony w
                     JOIN sklady_pociagow s ON w.id_skladu = s.id_skladu
                     WHERE s.id_pociagu = :id_pociagu";
    $stmt = $db->prepare($query_wagony);
    $stmt->bindParam(":id_pociagu", $id_pociagu, PDO::PARAM_INT);
    $stmt->execute();
    $wagony = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pobranie zajętych miejsc z relacjami podróży
    $query_bilety = "SELECT b.id_wagonu, b.miejsce, st1.nazwa AS stacja_start, st2.nazwa AS stacja_koniec
                     FROM bilety b
                     JOIN stacje st1 ON b.id_stacji_start = st1.id_stacji
                     JOIN stacje st2 ON b.id_stacji_koniec = st2.id_stacji
                     WHERE b.id_pociagu = :id_pociagu AND b.data_podrozy = :data_podrozy";
    $stmt = $db->prepare($query_bilety);
    $stmt->bindParam(":id_pociagu", $id_pociagu, PDO::PARAM_INT);
    $stmt->bindParam(":data_podrozy", $data_podrozy, PDO::PARAM_STR);
    $stmt->execute();
    $bilety = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tworzenie listy zajętych miejsc
    foreach ($bilety as $bilet) {
        $miejsca_zajete[$bilet["id_wagonu"]][$bilet["miejsce"]] = $bilet["stacja_start"] . " → " . $bilet["stacja_koniec"];
    }

    // Tworzenie listy wolnych miejsc
    foreach ($wagony as $wagon) {
        for ($i = $wagon["miejsce_od"]; $i <= $wagon["miejsce_do"]; $i++) {
            if (!isset($miejsca_zajete[$wagon["id_wagonu"]][$i])) {
                $miejsca_wolne[$wagon["id_wagonu"]][] = $i;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graficzny układ miejsc</title>
    <style>
        .wagon {
            display: flex;
            flex-wrap: wrap;
            width: 320px;
            border: 2px solid black;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .miejsce {
            width: 40px;
            height: 40px;
            margin: 5px;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .wolne { background-color: green; color: white; }
        .zajete { background-color: gray; color: white; position: relative; }
        .zajete:hover::after {
            content: attr(data-relacja);
            position: absolute;
            background-color: black;
            color: white;
            padding: 5px;
            font-size: 12px;
            white-space: nowrap;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
</head>
<body>
    <h2>Sprawdź dostępne miejsca</h2>
    <form method="post">
        <label><strong>Numer pociągu:</strong></label>
        <select name="id_pociagu" required>
            <?php foreach ($pociagi as $pociag): ?>
                <option value="<?= $pociag['id_pociagu'] ?>" <?= isset($id_pociagu) && $id_pociagu == $pociag['id_pociagu'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pociag['numer_pociagu']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label><strong>Data podróży:</strong></label>
        <input type="date" name="data_podrozy" value="<?= isset($data_podrozy) ? $data_podrozy : date('Y-m-d') ?>" required><br><br>

        <input type="submit" value="Sprawdź miejsca">
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <h3>Wizualizacja miejsc w pociągu</h3>
        <?php foreach ($wagony as $wagon): ?>
            <h4>Wagon <?= htmlspecialchars($wagon["numer_wagonu"]) ?></h4>
            <div class="wagon">
                <?php for ($i = $wagon["miejsce_od"]; $i <= $wagon["miejsce_do"]; $i++): ?>
                    <?php if (isset($miejsca_zajete[$wagon["id_wagonu"]][$i])): ?>
                        <div class="miejsce zajete" data-relacja="<?= htmlspecialchars($miejsca_zajete[$wagon["id_wagonu"]][$i]) ?>">
                            <?= $i ?>
                        </div>
                    <?php else: ?>
                        <div class="miejsce wolne">
                            <?= $i ?>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
