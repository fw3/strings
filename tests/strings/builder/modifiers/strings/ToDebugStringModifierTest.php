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

namespace fw3\tests\strings\builder\modifys\strings;

use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\strings\builder\modifiers\strings\ToDebugStringModifier;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ToDebugStringModifierTest extends TestCase
{
    public static function modifyDataProvider(): \Generator
    {
        yield ['true', true];

        yield ['false', false];

        yield ['-1', -1];

        yield ['0', 0];

        yield ['1', 1];

        yield ['-1.1', -1.1];

        yield ['0.1', 0.1];

        yield ['1.1', 1.1];

        yield ['-1.0', -1.0];

        yield ['0.0', 0.0];

        yield ['1.0', 1.0];

        yield ['\'asdf\'', 'asdf'];

        yield ['\'a\'sd"f\'', 'a\'sd"f'];

        yield ['Array', []];

        yield ['NULL', null];
    }

    /**
     * @test
     */
    public function instance(): void
    {
        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = ToDebugStringModifier::class;

        $this->assertTrue(\is_subclass_of($actual, $expected));
    }

    /**
     * @dataProvider modifyDataProvider
     *
     * @test
     */
    public function modify($expected, $actual): void
    {
        $this->assertSame($expected, ToDebugStringModifier::modify($actual));
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $toDebugString   = new ToDebugStringModifier();

        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = $toDebugString;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = '\'asdf\'';
        $actual     = $toDebugString('asdf');

        $this->assertSame($expected, $actual);
    }
}
