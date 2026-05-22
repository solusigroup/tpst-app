<?php

namespace App\Helpers;

class DateHelper
{
    /**
     * Get Indonesian month names mapping.
     *
     * @return array<string, string>
     */
    public static function indonesianMonths(): array
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
            '9' => 'September'
        ];
    }

    /**
     * Get Indonesian month name by index.
     *
     * @param string|int $month
     * @return string
     */
    public static function indonesianMonthName(string|int $month): string
    {
        $padded = str_pad((string)$month, 2, '0', STR_PAD_LEFT);
        return self::indonesianMonths()[$padded] ?? (string)$month;
    }
}
