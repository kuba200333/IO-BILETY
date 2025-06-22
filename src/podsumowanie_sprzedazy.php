<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php');
require_once "config.php";
require_once "class/Bilet.php";
require_once "class/RozkladJazdy.php";

if (!isset($_POST['id_biletu'])) {
    die("Brak biletu.");
}

$database = new Database();
$db = $database->getConnection();

$biletObj = new Bilet($db);
$bilet = $biletObj->getBiletById((int)$_POST['id_biletu']);

if (!$bilet) {
    die("Nie znaleziono biletu.");
}

if (ob_get_length()) ob_clean();

$rozkladObj = new RozkladJazdy($db);
$godzinaOdjazdu = $rozkladObj->getGodzinaOdjazdu((int)$_POST['id_biletu']);
$godzinaPrzyjazdu = $rozkladObj->getGodzinaPrzyjazdu((int)$_POST['id_biletu']);

$godzinaOdjazduFormat = substr($godzinaOdjazdu, 0, 5);
$godzinaPrzyjazduFormat = substr($godzinaPrzyjazdu, 0, 5);

$dataPodrozy = new DateTime($bilet['data_podrozy']);
$godzinaOdObj = DateTime::createFromFormat('H:i:s', $godzinaOdjazdu);
$godzinaPrzObj = DateTime::createFromFormat('H:i:s', $godzinaPrzyjazdu);
if ($godzinaPrzObj < $godzinaOdObj) {
    $dataPodrozy->modify('+1 day');
}
$dataFormat = $dataPodrozy->format('d.m');

$liczba_km = $biletObj->obliczOdleglosc($bilet["numer_pociagu"], $bilet["stacja_start"], $bilet["stacja_koniec"]);

$pdf = new TCPDF('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->SetMargins(10, 10, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 9);

$html = '
<table cellspacing="0" cellpadding="3" border="0" width="100%">
  <tr>
    <td width="30%"><b>PolRail S.A.</b><br/>BILET<br/>PRZEJAZD TAM</td>
    <td width="40%" align="center"><b>ID:</b> '.str_pad($bilet['id_biletu'], 8, "0", STR_PAD_LEFT).'</td>
    <td width="30%" align="right">
      <table border="1" cellpadding="2">
        <tr><td><b>POC: IC</b></td><td>NORMAL: '.($bilet['wymiar_znizki'] > 0 ? '0' : '1').'</td></tr>
        <tr><td><b>OF: 1</b></td><td>ULG.: '.($bilet['wymiar_znizki'] > 0 ? '1' : '0').'</td></tr>
      </table>
    </td>
  </tr>
</table>
<br/>
';

$html .= '
<table border="1" cellpadding="4" cellspacing="0">
  <tr bgcolor="#f0f0f0">
    <th><b>Data wyjazdu</b></th>
    <th><b>Godzina wyjazdu</b></th>
    <th><b>OD/VON/FROM => DO/NACH/TO</b></th>
    <th><b>Data przyjazdu</b></th>
    <th><b>Godzina przyjazdu</b></th>
    <th><b>Klasa</b></th>
  </tr>
  <tr bgcolor="#f0f0f0">  
    <td>'.$dataFormat.'</td>
    <td>'.$godzinaOdjazduFormat.'</td>
    <td>'.$bilet['stacja_start'].' => '.$bilet['stacja_koniec'].'</td>
    <td>'.$dataFormat.'</td>
    <td>'.$godzinaPrzyjazduFormat.'</td>
    <td>'.$bilet['klasa'].'</td>
  </tr>
</table>
<br/>
<b>Przewoźnik:</b> PolRail<br/>
<br/>
<b>'.$bilet['stacja_start'].' '.$godzinaOdjazduFormat.' → '.$bilet['stacja_koniec'].' '.$godzinaPrzyjazduFormat.'</b><br/>
POC. '.$bilet['numer_pociagu'].' WAGON '.$bilet['numer_wagonu'].' M. '.$bilet['miejsce'].' DO SIEDZENIA '.$bilet['miejsce'].' KOR.<br/>
Wagon z przedziałami
<br/><br/>

<table border="1" cellpadding="4" cellspacing="0">
  <tr>
    <td><b>KM:</b></td>
    <td>'.$liczba_km.'</td>
    <td><b>PLN:</b></td>
    <td><b>'.$bilet['cena'].'</b></td>
    <td><b>'.$bilet['metoda_platnosci'].'</b></td>
  </tr>
</table>
<br/>
';

$html .= '
<table border="0" width="100%">
  <tr>
    <td width="50%">
      <b>NIP:</b> 526-25-44-258<br/>
      '.date("d.m.Y H:i").'<br/>
      <b>ZZ'.str_pad($bilet['id_biletu'], 10, '0', STR_PAD_LEFT).' (1)</b>
    </td>
    <td width="50%" align="right">
';

$pdf->writeHTML($html, true, false, false, false, '');

$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($bilet['kod_qr']);
$pdf->Image($qrUrl, 160, 90, 30, 30, 'PNG');

$pdf->Output('bilet.pdf', 'I');
?>
