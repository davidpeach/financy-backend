<?php

namespace App\Utils;

use NumberFormatter;

class MoneyFormatter
{
    /**
     * @param int $amountInPennies
     * @return string
     */
    public static function format(int $amountInPennies): string
    {
        $formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amountInPennies/100, 'GBP');
    }
}
