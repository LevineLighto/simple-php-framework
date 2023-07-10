<?php

namespace App\Classes;

use DateTime;
use IntlDateFormatter;
use NumberFormatter;

class Formatter {
    protected static $months = [
        'Januari'   => '01',
        'Februari'  => '02',
        'Maret'     => '03',
        'April'     => '04',
        'Mei'       => '05',
        'Juni'      => '06',
        'Juli'      => '07',
        'Agustus'   => '08',
        'September' => '09',
        'Oktober'   => '10',
        'November'  => '11',
        'Desember'  => '12',
    ];

    public static function price($number) {
        $formatter  = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $replaced   = json_decode('"\u00a0"');
        $string     = $formatter->formatCurrency($number, 'IDR');
        
        return str_replace($replaced, ' ', urldecode($string));
    }

    public static function date($date, $format = 'd M Y H:i:s') {
        $date = new DateTime($date);

        return $date->format($format);
    }

    public static function readableDate($date, $dateFormat = IntlDateFormatter::FULL, $timeFormat = IntlDateFormatter::LONG) {
        $formatter = new IntlDateFormatter(
            'id-ID',
            $dateFormat,
            $timeFormat,
            'Asia/Jakarta', 
            IntlDateFormatter::GREGORIAN
        );

        return $formatter->format($date);
    }

    public static function processableDate($date) {
        [$day, $month, $year] = explode(' ', $date);

        $month = static::$months[$month];

        return "{$year}-{$month}-{$day}";
    }
}