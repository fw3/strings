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

namespace fw3\tests\strings\builder\modifys\security;

use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\strings\builder\modifiers\security\EscapeModifier;
use fw3\strings\converter\Convert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class EscapeModifierTest extends TestCase
{
    /**
     * @test
     */
    public function modify(): void
    {
        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = EscapeModifier::class;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = '&lt;a href=&quot;#id&quot; onclick=&quot;alert(&apos;alert&apos;);&quot;&gt;';
        $actual     = EscapeModifier::modify('<a href="#id" onclick="alert(\'alert\');">');

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $options    = [
            'type'  => Convert::ESCAPE_TYPE_JS,
        ];

        $expected   = '\x3ca\x20href\x3d\x22\x23id\x22\x20onclick\x3d\x22alert\x28\x27alert\x27\x29\x3b\x22\x3e';
        $actual     = EscapeModifier::modify('<a href="#id" onclick="alert(\'alert\');">', $options);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $modifier   = new EscapeModifier();

        // ----------------------------------------------
        $expected   = ModifierInterface::class;
        $actual     = $modifier;

        $this->assertTrue(\is_subclass_of($actual, $expected));

        // ----------------------------------------------
        $expected   = '&lt;a href=&quot;#id&quot; onclick=&quot;alert(&apos;alert&apos;);&quot;&gt;';
        $actual     = $modifier('<a href="#id" onclick="alert(\'alert\');">');

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $options    = [
            'type'  => Convert::ESCAPE_TYPE_JS,
        ];

        $expected   = '\x3ca\x20href\x3d\x22\x23id\x22\x20onclick\x3d\x22alert\x28\x27alert\x27\x29\x3b\x22\x3e';
        $actual     = $modifier('<a href="#id" onclick="alert(\'alert\');">', $options);

        $this->assertEquals($expected, $actual);
    }
}
