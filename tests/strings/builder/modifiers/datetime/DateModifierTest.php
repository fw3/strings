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

use fw3\strings\builder\modifiers\datetime\DateModifier;
use fw3\strings\builder\modifiers\ModifierInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DateModifierTest extends TestCase
{
    /**
     * @test
     */
    public function modify(): void
    {
        $base_ts    = \strtotime('2020-01-01 00:00:00');

        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = DateModifier::class;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = '2020-01-01 00:00:00';
        $actual     = DateModifier::modify($base_ts, ['Y-m-d H:i:s']);

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $expected   = '2020/01/01 00:00:00';
        $actual     = DateModifier::modify($base_ts);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $base_ts    = \strtotime('2020-01-01 00:00:00');
        $modifier   = new DateModifier();

        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = $modifier;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = '2020-01-01 00:00:00';
        $actual     = $modifier($base_ts, ['Y-m-d H:i:s']);

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $expected   = '2020/01/01 00:00:00';
        $actual     = $modifier($base_ts);

        $this->assertEquals($expected, $actual);
    }
}
