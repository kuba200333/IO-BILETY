<?php
    $data_podrozy=
    $dzisiaj = new DateTime();
    $data_podrozy_dt = new DateTime($data_podrozy);

    // Oblicz liczbę dni różnicy – biorąc pod uwagę, że data_podrozy musi być po dziś
    $timestamp_diff = $data_podrozy_dt->getTimestamp() - $dzisiaj->getTimestamp();
    $roznica_dni = floor($timestamp_diff / (60 * 60 * 24));  // pełne dni

    $promo = 0;
    if ($roznica_dni >= 21) {
        $promo = 30;
    } elseif ($roznica_dni >= 14) {
        $promo = 20;
    } elseif ($roznica_dni >= 7) {
        $promo = 10;
    }


    $laczna_znizka = $znizka + $promo;
    if ($laczna_znizka > 90) $laczna_znizka = 90;

    $cena_koncowa = round($cena_podstawowa * (1 - ($laczna_znizka / 100)), 2);
?>