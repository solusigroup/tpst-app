<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTimeInterface;

class DateHelper
{
    /**
     * Parse input into a Carbon instance or null if invalid/empty.
     */
    public static function parse($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        if ($date instanceof Carbon) {
            return $date->copy()->locale('id');
        }

        if ($date instanceof DateTimeInterface) {
            return Carbon::instance($date)->locale('id');
        }

        try {
            return Carbon::parse($date)->locale('id');
        } catch (\Throwable $e) {
            return null;
        }
    }

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

    /**
     * Format date in Indonesian format (default: 20 Agustus 2026).
     */
    public static function formatTanggal($date, string $format = 'd F Y', string $default = '-'): string
    {
        $c = self::parse($date);
        return $c ? $c->translatedFormat($format) : $default;
    }

    /**
     * Format day and date in Indonesian format (contoh: Kamis, 20 Agustus 2026).
     */
    public static function formatHariTanggal($date, string $default = '-'): string
    {
        $c = self::parse($date);
        return $c ? $c->translatedFormat('l, d F Y') : $default;
    }

    /**
     * Format date and time in Indonesian format (contoh: 20 Agustus 2026 14:12 WIB).
     */
    public static function formatTanggalWaktu($date, bool $withWib = true, string $default = '-'): string
    {
        $c = self::parse($date);
        if (!$c) return $default;
        $formatted = $c->translatedFormat('d F Y H:i');
        return $withWib ? $formatted . ' WIB' : $formatted;
    }

    /**
     * Format short numeric date (contoh: 20/08/2026).
     */
    public static function formatSingkat($date, string $default = '-'): string
    {
        $c = self::parse($date);
        return $c ? $c->format('d/m/Y') : $default;
    }

    /**
     * Format month and year in Indonesian format (contoh: Agustus 2026).
     */
    public static function formatBulanTahun($date, string $default = '-'): string
    {
        $c = self::parse($date);
        return $c ? $c->translatedFormat('F Y') : $default;
    }

    /**
     * Format time (contoh: 14:12 WIB).
     */
    public static function formatWaktu($date, bool $withWib = true, string $default = '-'): string
    {
        $c = self::parse($date);
        if (!$c) return $default;
        $formatted = $c->format('H:i');
        return $withWib ? $formatted . ' WIB' : $formatted;
    }

    /**
     * Format date range (contoh: 01/08/2026 - 31/08/2026 atau 1 s/d 31 Agustus 2026).
     */
    public static function formatRentang($startDate, $endDate, bool $short = false, string $default = '-'): string
    {
        $start = self::parse($startDate);
        $end = self::parse($endDate);

        if (!$start && !$end) return $default;
        if ($start && !$end) return self::formatTanggal($start);
        if (!$start && $end) return self::formatTanggal($end);

        if ($short) {
            return $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        }

        if ($start->format('Y-m') === $end->format('Y-m')) {
            return $start->format('d') . ' - ' . $end->translatedFormat('d F Y');
        }

        return $start->translatedFormat('d F Y') . ' s/d ' . $end->translatedFormat('d F Y');
    }

    /**
     * Format relative time in Indonesian (contoh: 2 jam yang lalu).
     */
    public static function diffForHumans($date, string $default = '-'): string
    {
        $c = self::parse($date);
        return $c ? $c->diffForHumans() : $default;
    }
}
