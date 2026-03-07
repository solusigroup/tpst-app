<?php

namespace App\Helpers;

class Terbilang
{
    public static function make($angka)
    {
        $angka = abs((float) $angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = self::make($angka - 10) . " belas";
        } elseif ($angka < 100) {
            $terbilang = self::make($angka / 10) . " puluh" . self::make($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " seratus" . self::make($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = self::make($angka / 100) . " ratus" . self::make($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " seribu" . self::make($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = self::make($angka / 1000) . " ribu" . self::make($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = self::make($angka / 1000000) . " juta" . self::make($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = self::make($angka / 1000000000) . " milyar" . self::make(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $terbilang = self::make($angka / 1000000000000) . " trilyun" . self::make(fmod($angka, 1000000000000));
        }

        return trim($terbilang);
    }
}
