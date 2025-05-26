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
        $miejsca_zajete[$bilet["id_wagonu"]][] = [
            "miejsce" => $bilet["miejsce"],
            "relacja" => $bilet["stacja_start"] . " → " . $bilet["stacja_koniec"]
        ];
    }

    // Tworzenie listy wolnych miejsc
    foreach ($wagony as $wagon) {
        $miejsca_wolne[$wagon["id_wagonu"]] = [];
        for ($i = $wagon["miejsce_od"]; $i <= $wagon["miejsce_do"]; $i++) {
            $zajete_miejsca = array_column($miejsca_zajete[$wagon["id_wagonu"]] ?? [], "miejsce");
            if (!in_array($i, $zajete_miejsca)) {
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
    <title>Sprawdź dostępne miejsca</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
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
        <h3>Zajęte miejsca</h3>
        <table>
            <tr>
                <th>Wagon</th>
                <th>Zajęte miejsca</th>
                <th>Relacja</th>
            </tr>
            <?php foreach ($miejsca_zajete as $id_wagonu => $miejsca): ?>
                <?php foreach ($miejsca as $miejsce): ?>
                    <tr>
                        <td><?= htmlspecialchars($id_wagonu) ?></td>
                        <td><?= htmlspecialchars($miejsce["miejsce"]) ?></td>
                        <td><?= htmlspecialchars($miejsce["relacja"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </table>

        <h3>Wolne miejsca</h3>
        <table>
            <tr>
                <th>Wagon</th>
                <th>Wolne miejsca</th>
            </tr>
            <?php foreach ($miejsca_wolne as $id_wagonu => $miejsca): ?>
                <tr>
                    <td><?= htmlspecialchars($id_wagonu) ?></td>
                    <td><?= empty($miejsca) ? 'Brak' : implode(", ", $miejsca) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
