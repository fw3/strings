<?php
/**
 *     _______       _______
 *    / ____/ |     / /__  /
 *   / /_   | | /| / / /_ <
 *  / __/   | |/ |/ /___/ /
 * /_/      |__/|__//____/
 *
 * Flywheel3: the inertia php framework
 *
 * @category    Flywheel3
 * @package     strings
 * @author      wakaba <wakabadou@gmail.com>
 * @copyright   Copyright (c) @2020  Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/). All rights reserved.
 * @license     http://opensource.org/licenses/MIT The MIT License.
 *              This software is released under the MIT License.
 * @varsion     1.0.0
 */

declare(strict_types=1);

namespace fw3\tests\strings\builder;

use fw3\strings\builder\StringBuilder;
use fw3\tests\strings\utilitys\convertrs\UrlConvertr;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @internal
 */
class StringBuilder2Test extends TestCase
{
    /**
     * @test
     */
    public function build(): void
    {
        $stringBuilder    = StringBuilder::factory();

        // ----------------------------------------------
        $message    = '{:0|e}{:ickx}{:0|e}';
        $values     = (object) ['"'];
        $converter  = new UrlConvertr();

        $expected   = '&quot;https://ickx.jp&quot;';
        $actual     = $stringBuilder->buildMessage($message, $values, $converter);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|e}{:ickx}{:0|e}';
        $values     = (object) ['"'];
        $converter  = function(string $name, string $search, array $values) {
            return $name === '0' ? '0' : $name;
        };

        $expected   = '0ickx0';
        $actual     = $stringBuilder->buildMessage($message, $values, $converter);
        $this->assertSame($expected, $actual);
    }

    // /**
    //  * @test
    //  */
    // public function test1(): void
    // {
    // $stringBuilder = StringBuilder::factory();

    // // ----------------------------------------------
    // $message    = '{:a|to_debug}';
    // $values     = ['a' => null];
    // $converter  = UrlConvertr::class;

    // $expected   = 'NULL';
    // $actual     = $stringBuilder->buildMessage($message, $values, $converter);
    // $this->assertSame($expected, $actual);

    // // ----------------------------------------------
    // $message    = '{:0|e}{:ickx}{:0|e}';
    // $values     = (object) ['"'];
    // $converter  = UrlConvertr::class;

    // $expected   = '&quot;https://ickx.jp&quot;';
    // $actual     = $stringBuilder->buildMessage($message, $values, $converter);
    // $this->assertSame($expected, $actual);

    // }
}
