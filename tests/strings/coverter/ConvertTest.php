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

namespace fw3\tests\strings\converter;

use fw3\strings\converter\Convert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ConvertTest extends TestCase
{
    protected $internalEncoding;

    /**
     * @test
     */
    public function toJson(): void
    {
        // ----------------------------------------------
        $value = [];

        $expected = '[]';
        $actual   = Convert::toJson($value);

        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value = new \stdClass();

        $expected = '{}';
        $actual   = Convert::toJson($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value = [1, 'a', 'あ', true, null, (object) [], [1, 'a', 'あ', true, null, (object) []]];

        $expected = '[1,"a","\u3042",true,null,{},[1,"a","\u3042",true,null,{}]]';
        $actual   = Convert::toJson($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value = (object) [1, 'a', 'あ', true, null, (object) [], [1, 'a', 'あ', true, null, (object) []]];

        $expected = '{"0":1,"1":"a","2":"\u3042","3":true,"4":null,"5":{},"6":[1,"a","\u3042",true,null,{}]}';
        $actual   = Convert::toJson($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function htmlEscape(): void
    {
        // ----------------------------------------------
        $value  = 'asdf';

        $expected   = $value;
        $actual     = Convert::htmlEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = '<a href="#id">';

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';
        $actual     = Convert::htmlEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = '<a href="#id" onclick="alert(\'alert\');">';

        $expected   = '&lt;a href=&quot;#id&quot; onclick=&quot;alert(&apos;alert&apos;);&quot;&gt;';
        $actual     = Convert::htmlEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = 'alert(\'alert\');alert("alert2")';

        $expected   = 'alert\x28\x27alert\x27\x29\x3balert\x28\x22alert2\x22\x29';
        $actual     = Convert::jsEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = '<a href="#id" target=\'_blank\'>';
        $options    = [
            'flags' => \ENT_HTML401,
        ];

        $expected   = '&lt;a href=&quot;#id&quot; target=&apos;_blank&apos;&gt;';
        $actual     = Convert::htmlEscape($value);
        $this->assertEquals($expected, $actual);

        $expected   = '&lt;a href=&quot;#id&quot; target=&apos;_blank&apos;&gt;';
        $actual     = Convert::htmlEscape($value, $options);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function htmlEscapeIllegalSequence001(): void
    {
        // ----------------------------------------------
        $value = '<a href="#id" tar' . "\xff" . 'get=\'_blank\'>';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('不正なエンコーディングが検出されました。encoding:\'UTF-8\', value_encoding:false');

        $actual = Convert::htmlEscape($value);
    }

    /**
     * @test
     */
    public function htmlEscapeIllegalSequence002(): void
    {
        // ----------------------------------------------
        $value = 'あああああああああ';

        $encoding   = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('不正なエンコーディングが検出されました。encoding:\'%s\', value_encoding:\'UTF-8\'', $encoding));
        $actual = Convert::htmlEscape($value, [], $encoding);
    }

    /**
     * @test
     */
    public function htmlEscapeIllegalSequence003(): void
    {
        // ----------------------------------------------
        $value = 'あああああああああ';

        $encoding   = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('不正なエンコーディングが検出されました。encoding:\'%s\', value_encoding:\'UTF-8\'', $encoding));
        $actual = Convert::htmlEscape($value, [], $encoding);
    }

    /**
     * @test
     */
    public function htmlEscapeIllegalSequence004(): void
    {
        // ----------------------------------------------
        $encoding   = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';

        $value  = \mb_convert_encoding('あああああああああ', $encoding);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('不正なエンコーディングが検出されました。encoding:\'UTF-8\', value_encoding:\'%s\'', $encoding));
        $actual     = Convert::htmlEscape($value);
    }

    /**
     * @test
     */
    public function jsEscape(): void
    {
        // ----------------------------------------------
        $value  = 'asdf';

        $expected   = $value;
        $actual     = Convert::jsEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = '<a href="#id">';

        $expected   = '\x3ca\x20href\x3d\x22\x23id\x22\x3e';
        $actual     = Convert::jsEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = '<a href="#id" onclick="alert(\'alert\');">';

        $expected   = '\x3ca\x20href\x3d\x22\x23id\x22\x20onclick\x3d\x22alert\x28\x27alert\x27\x29\x3b\x22\x3e';
        $actual     = Convert::jsEscape($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = 'alert(\'alert\');alert("alert2")';

        $expected   = 'alert\x28\x27alert\x27\x29\x3balert\x28\x22alert2\x22\x29';
        $actual     = Convert::jsEscape($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function escape(): void
    {
        // ----------------------------------------------
        $value  = '<a href="#id">';

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';
        $actual     = Convert::escape($value, Convert::ESCAPE_TYPE_HTML);
        $this->assertEquals($expected, $actual);

        $expected   = '\x3ca\x20href\x3d\x22\x23id\x22\x3e';
        $actual     = Convert::escape($value, Convert::ESCAPE_TYPE_JS);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = 'alert(\'alert\');alert("alert2")';

        $expected   = 'alert(&apos;alert&apos;);alert(&quot;alert2&quot;)';
        $actual     = Convert::escape($value, Convert::ESCAPE_TYPE_HTML);
        $this->assertEquals($expected, $actual);

        $expected   = 'alert\x28\x27alert\x27\x29\x3balert\x28\x22alert2\x22\x29';
        $actual     = Convert::escape($value, Convert::ESCAPE_TYPE_JS);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = '<a href="#id" target=\'_blank\'>';

        $options    = [
            'flags' => \ENT_HTML401,
        ];

        $expected   = '&lt;a href=&quot;#id&quot; target=&apos;_blank&apos;&gt;';
        $actual     = Convert::escape($value, Convert::ESCAPE_TYPE_HTML, $options);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $encoding   = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';

        $value  = \mb_convert_encoding('ああああああ', $encoding);

        $expected   = $value;
        $actual     = Convert::escape($value, Convert::ESCAPE_TYPE_HTML, [], $encoding);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toTextNotation(): void
    {
        // ----------------------------------------------
        $value  = true;

        $expected   = 'true';
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = 1;

        $expected   = 1;
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = (float) 1;

        $expected   = 1.0;
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = 0.1;

        $expected   = 0.1;
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = 'string';

        $expected   = '\'string\'';
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = [1, [2, [3]], 'a' => 'a', 'b' => ['b' => 'b', 'c' => ['c' => 'c']]];

        $expected   = 'Array';
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        $expected   = '[0 => 1, 1 => Array, \'a\' => \'a\', \'b\' => Array]';
        $actual     = Convert::toDebugString($value, 1);
        $this->assertEquals($expected, $actual);

        $expected   = '[0 => 1, 1 => [0 => 2, 1 => Array], \'a\' => \'a\', \'b\' => [\'b\' => \'b\', \'c\' => Array]]';
        $actual     = Convert::toDebugString($value, 2);
        $this->assertEquals($expected, $actual);

        $expected   = '[0 => 1, 1 => [0 => 2, 1 => [0 => 3]], \'a\' => \'a\', \'b\' => [\'b\' => \'b\', \'c\' => [\'c\' => \'c\']]]';
        $actual     = Convert::toDebugString($value, 3);
        $this->assertEquals($expected, $actual);

        $actual     = Convert::toDebugString($value, 4);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value         = new MockForConvertTest();
        $object_status = \sprintf('object(%s)#%d', $value::class, \spl_object_id($value));

        $expected   = $object_status;
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        $expected   = \sprintf('%s {public static $publicStatic = Array, protected static $protectdStatic = Array, private static $privateStatic = Array, public $public = Array, protected $protectd = Array, private $private = Array}', $object_status);
        $actual     = Convert::toDebugString($value, 1);
        $this->assertEquals($expected, $actual);

        $expected   = \sprintf('%s {public static $publicStatic = [0 => Array], protected static $protectdStatic = [0 => Array], private static $privateStatic = [0 => Array], public $public = [0 => Array], protected $protectd = [0 => Array], private $private = [0 => Array]}', $object_status);
        $actual     = Convert::toDebugString($value, 2);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = \fopen('php://memory', 'wb');

        $expected   = \sprintf('stream %s', $value);
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        \fclose($value);
        $expected   = \sprintf('resource (closed) %s', $value);
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value  = null;

        $expected   = 'NULL';
        $actual     = Convert::toDebugString($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toSnakeCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'Test_To_Case_Convert';
        $actual     = Convert::toSnakeCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toUpperSnakeCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'TEST_TO_CASE_CONVERT';
        $actual     = Convert::toUpperSnakeCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toLowerSnakeCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'test_to_case_convert';
        $actual     = Convert::toLowerSnakeCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toChainCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'Test-To-Case-Convert';
        $actual     = Convert::toChainCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toUpperChainCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'TEST-TO-CASE-CONVERT';
        $actual     = Convert::toUpperChainCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toLowerChainCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'test-to-case-convert';
        $actual     = Convert::toLowerChainCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toCamelCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toCamelCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toUpperCamelCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'TestToCaseConvert';
        $actual     = Convert::toUpperCamelCase($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function toLowerCamelCase(): void
    {
        // ----------------------------------------------
        $value      = 'Test_To_Case_Convert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To-Case-Convert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To Case Convert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test_To-Case Convert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test-To Case_Convert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'Test To_Case-Convert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'testToCaseConvert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);

        // ----------------------------------------------
        $value      = 'TestToCaseConvert';
        $expected   = 'testToCaseConvert';
        $actual     = Convert::toLowerCamelCase($value);
        $this->assertEquals($expected, $actual);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->internalEncoding = \mb_internal_encoding();
        \mb_internal_encoding('UTF-8');
    }

    public function tearDown(): void
    {
        \mb_internal_encoding($this->internalEncoding);

        parent::tearDown();
    }
}

class MockForConvertTest
{
    public const public_const    = [['public const']];

    protected const PROTECTD_CONST  = [['protected const']];

    private const PRIVATE_CONST   = [['private const']];

    public static $publicStatic   = [['public static']];

    protected static $protectdStatic = [['protected static']];

    private static $privateStatic  = [['private static']];

    public $public     = [['public']];

    protected $protectd   = [['protected']];

    private $private    = [['private']];
}
