<?php

namespace Tests\Unit\Utils;

use App\Utils\MoneyFormatter;
use PHPUnit\Framework\TestCase;

class MoneyFormatterTest extends TestCase
{
    /** @test */
    public function it_formats_integers_into_pounds_and_pence()
    {
        $this->assertEquals('£150.75', MoneyFormatter::format(15075));
    }
}
