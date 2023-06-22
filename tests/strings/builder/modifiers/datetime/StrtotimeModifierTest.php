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

namespace fw3\tests\strings\builder\modifys\datetime;

use fw3\strings\builder\modifiers\datetime\StrtotimeModifier;
use fw3\strings\builder\modifiers\ModifierInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StrtotimeModifierTest extends TestCase
{
    /**
     * @test
     */
    public function modify(): void
    {
        $base_ts    = \strtotime('2020-01-01 00:00:00');

        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = StrtotimeModifier::class;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = $base_ts;
        $actual     = StrtotimeModifier::modify('2020-01-01 00:00:00');

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $opsions    = [
            'baseTimestamp' => \strtotime('3020-01-01 00:00:00'),
        ];

        $expected   = \strtotime(\date('3020-01-02 00:00:00'));
        $actual     = StrtotimeModifier::modify('+ 1 day', $opsions);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $base_ts    = \strtotime('2020-01-01 00:00:00');
        $modifier   = new StrtotimeModifier();

        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = $modifier;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = $base_ts;
        $actual     = $modifier('2020-01-01 00:00:00');

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $opsions    = [
            'baseTimestamp' => \strtotime('3020-01-01 00:00:00'),
        ];

        $expected   = \strtotime(\date('3020-01-02 00:00:00'));
        $actual     = $modifier('+ 1 day', $opsions);

        $this->assertEquals($expected, $actual);
    }
}
