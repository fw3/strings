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

use PHPUnit\Framework\TestCase;
use fw3\strings\converter\Convert;
use fw3\strings\builder\StringBuilder;
use fw3\strings\builder\modifiers\ModifierTrait;
use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\tests\strings\utilitys\convertrs\UrlConvertr;
use fw3\strings\builder\modifiers\datetime\DateModifier;
use fw3\strings\builder\traits\converter\ConverterTrait;
use fw3\strings\builder\modifiers\security\EscapeModifier;
use fw3\strings\builder\modifiers\security\JsExprModifier;
use fw3\tests\strings\utilitys\modifiers\ZeroToDqModifier;
use fw3\tests\strings\utilitys\convertrs\DoNothingConvertr;
use fw3\tests\strings\utilitys\modifiers\DoNothingModifier;
use fw3\strings\builder\traits\converter\ConverterInterface;
use fw3\strings\builder\modifiers\datetime\StrtotimeModifier;
use fw3\strings\builder\modifiers\strings\ToDebugStringModifier;

/**
 * @runTestsInSeparateProcesses
 * @internal
 */
class StringBuilderTest extends TestCase
{
    protected $internalEncoding;

    public static function defaultEnclosureBeginExceptionDataProvider(): array
    {
        return [
            [':', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['}', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public static function defaultEnclosureEndExceptionDataProvider(): array
    {
        return [
            [':', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの変数部開始文字列と同じ値を設定しようとしています。value:{:'],
        ];
    }

    public static function defaultNameSeparatorExceptionDataProvider(): array
    {
        return [
            ['|', \InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', \InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', \InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', \InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public static function defaultModifierSeparatorExceptionDataProvider(): array
    {
        return [
            [':', \InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数名セパレータと同じ値を設定しようとしています。value::'],
            ['■', \InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', \InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', \InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public static function enclosureBeginExceptionDataProvider(): array
    {
        return [
            [':', \InvalidArgumentException::class, '変数部開始文字列に変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', \InvalidArgumentException::class, '変数部開始文字列に修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', \InvalidArgumentException::class, '変数部開始文字列に変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['}', \InvalidArgumentException::class, '変数部開始文字列に変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public static function enclosureEndExceptionDataProvider(): array
    {
        return [
            [':', \InvalidArgumentException::class, '変数部終了文字列に変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', \InvalidArgumentException::class, '変数部終了文字列に修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', \InvalidArgumentException::class, '変数部終了文字列に変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', \InvalidArgumentException::class, '変数部終了文字列に変数部開始文字列と同じ値を設定しようとしています。value:{:'],
        ];
    }

    public static function nameSeparatorExceptionDataProvider(): array
    {
        return [
            ['|', \InvalidArgumentException::class, '変数名セパレータに修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', \InvalidArgumentException::class, '変数名セパレータに変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', \InvalidArgumentException::class, '変数名セパレータに変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', \InvalidArgumentException::class, '変数名セパレータに変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public static function modifierSeparatorExceptionDataProvider(): array
    {
        return [
            [':', \InvalidArgumentException::class, '修飾子セパレータに変数名セパレータと同じ値を設定しようとしています。value::'],
            ['■', \InvalidArgumentException::class, '修飾子セパレータに変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', \InvalidArgumentException::class, '修飾子セパレータに変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', \InvalidArgumentException::class, '修飾子セパレータに変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    /**
     * @test
     */
    public function build(): void
    {
        $stringBuilder    = StringBuilder::factory();

        // ----------------------------------------------
        $expected   = '';
        $actual     = $stringBuilder->buildMessage('');
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|e}';
        $values     = ['"'];

        $expected   = '&quot;';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|e}';
        $values     = (object) ['"'];

        $expected   = '&quot;';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|e}{:ickx}{:0|e}';
        $values     = (object) ['"'];
        $converter  = UrlConvertr::class;

        $expected   = '&quot;https://ickx.jp&quot;';
        $actual     = $stringBuilder->buildMessage($message, $values, $converter);
        $this->assertSame($expected, $actual);

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

    /**
     * @test
     */
    public function characterEncoding(): void
    {
        // ==============================================
        $character_encoding = StringBuilder::factory()->characterEncoding();

        $expected   = 'UTF-8';
        $actual     = $character_encoding;
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::get()->characterEncoding('SJIS-win');

        $expected   = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';
        $actual     = StringBuilder::get()->characterEncoding();
        $this->assertSame($expected, $actual);
    }

    /**
     * @preserveGlobalState disable
     *
     * @test
     */
    public function converter(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getUrlConverterSet();

        // ----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertSame($expected, $actual);

        // ==============================================
        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME);

        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getDoNothingConverterSet();

        // ----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function defaultCharacterEncoding(): void
    {
        // ==============================================
        $character_encoding = StringBuilder::defaultCharacterEncoding();

        $expected   = null;
        $actual     = $character_encoding;
        $this->assertSame($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $expected   = 'UTF-8';
        $actual     = StringBuilder::get()->characterEncoding();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $encoding = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';

        StringBuilder::defaultCharacterEncoding($encoding);

        $expected   = $encoding;
        $actual     = StringBuilder::defaultCharacterEncoding();
        $this->assertSame($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        // ----------------------------------------------
        $expected   = \version_compare(\PHP_VERSION, '8.1') ? 'CP932' : 'SJIS-win';
        $actual     = StringBuilder::get()->characterEncoding();
        $this->assertSame($expected, $actual);
    }

    /**
     * @preserveGlobalState disable
     *
     * @test
     */
    public function defaultConverter(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getUrlConverterSet();

        // ----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertSame($expected, $actual);

        // ==============================================
        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getDoNothingConverterSet();

        // ----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function defaultEnclosure(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultEnclosure('${', '}}');
        $this->assertSame($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $expected   = ['begin' => '${', 'end' => '}}'];
        $actual     = StringBuilder::defaultEnclosure();
        $this->assertSame($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosure(['begin' => '${', 'end' => '}}', '{:', '}'])::factory();

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf {:html} zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosure(['${', '}}'])::factory();

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider defaultEnclosureBeginExceptionDataProvider
     *
     * @test
     */
    public function defaultEnclosureBeginException($value, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException(\InvalidArgumentException::class);
        StringBuilder::defaultEnclosure([$value, '}']);
    }

    /**
     * @dataProvider defaultEnclosureEndExceptionDataProvider
     *
     * @test
     */
    public function defaultEnclosureEndException($value, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException(\InvalidArgumentException::class);
        StringBuilder::defaultEnclosure(['{:', $value]);
    }

    /**
     * @test
     */
    public function defaultEnclosureBegin(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_begin    = StringBuilder::defaultEnclosureBegin();

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultEnclosureBegin('${');
        $this->assertSame($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $expected   = '${';
        $actual     = StringBuilder::defaultEnclosureBegin();
        $this->assertSame($expected, $actual);

        $message    = 'asdf ${html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosureBegin($enclosure_begin)::factory();

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function setDefaultValue(): void
    {
        // ==============================================
        $expected   = [];
        $actual     = StringBuilder::defaultValues();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::setDefaultValue('html', '<a href="#id">')::factory();
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME);

        // ----------------------------------------------
        $actual     = StringBuilder::setDefaultValue('html', '<a href=\'#id\'>')::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function defaultEnclosureEnd(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_End    = StringBuilder::defaultEnclosureEnd();

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultEnclosureEnd('}}');
        $this->assertSame($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $expected   = '}}';
        $actual     = StringBuilder::defaultEnclosureEnd();
        $this->assertSame($expected, $actual);

        $message    = 'asdf {:html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosureEnd($enclosure_End)::factory();

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function defaultNameSeparator(): void
    {
        // ==============================================
        $values     = [
            '0'         => '00000',
            'begin'     => 'begin',
            'begin:0'   => 'aaaa',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        // ==============================================
        $expected   = ':';
        $actual     = StringBuilder::defaultNameSeparator();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory();

        $message    = '{:begin:0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0:begin}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:begin:0|zero_to_dq|escape}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0:begin|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultNameSeparator('<>');
        $this->assertSame($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $message    = '{:begin:0}';

        $expected   = 'aaaa';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:begin<>0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider defaultNameSeparatorExceptionDataProvider
     *
     * @test
     */
    public function defaultNameSeparatorException($value, $exception_class, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::defaultNameSeparator($value);
    }

    /**
     * @test
     */
    public function defaultModifierSeparator(): void
    {
        // ==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        // ==============================================
        $expected   = '|';
        $actual     = StringBuilder::defaultModifierSeparator();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultModifierSeparator('<>');
        $this->assertSame($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $message    = '{:0<>zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0<>zero_to_dq<>escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0<>zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider defaultModifierSeparatorExceptionDataProvider
     *
     * @test
     */
    public function defaultModifierSeparatorException($value, $exception_class, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::defaultModifierSeparator($value);
    }

    /**
     * @test
     */
    public function defaultModifierSet(): void
    {
        // ==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        // ==============================================
        $expected   = [
            'date'              => DateModifier::class,
            'strtotime'         => StrtotimeModifier::class,
            'escape'            => EscapeModifier::class,
            'e'                 => EscapeModifier::class,
            'js_expr'           => JsExprModifier::class,
            'to_debug'          => ToDebugStringModifier::class,
            'to_debug_str'      => ToDebugStringModifier::class,
            'to_debug_string'   => ToDebugStringModifier::class,
        ];
        $actual     = StringBuilder::defaultModifierSet();
        $this->assertSame($expected, $actual);

        // ==============================================
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultModifierSet($modifier_list);
        $this->assertSame($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        // ----------------------------------------------
        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function defaultSubstitute(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
            'alt'   => '{:html}',
        ];

        $message    = '{:html2}/{:alt2}';

        // ==============================================
        $expected   = '';
        $actual     = StringBuilder::defaultSubstitute();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory();

        $expected   = '/';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = null;

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultSubstitute($substitute);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $expected   = '{:html2}/{:alt2}';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = '';

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultSubstitute($substitute)::factory();

        $expected   = '/';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = '■';

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultSubstitute($substitute)::factory();

        $expected   = '■/■';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = '{:alt}';

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultSubstitute($substitute)::factory();

        $expected   = '<a href="#id">/<a href="#id">';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function defaultValues(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = [];
        $actual     = StringBuilder::defaultValues();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultValues($values);
        $this->assertSame($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $values     = [
            'html'  => '<a href=\'#id\'>',
        ];

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultValues($values)::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function enclosure(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->enclosure('${', '}}');
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $expected   = ['begin' => '${', 'end' => '}}'];
        $actual     = StringBuilder::factory()->enclosure();
        $this->assertSame($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory()->enclosure(['begin' => '${', 'end' => '}}', '{:', '}']);

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf {:html} zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory()->enclosure(['${', '}}']);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider enclosureBeginExceptionDataProvider
     *
     * @test
     */
    public function enclosureBeginException($value, $exception_class, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->enclosure([$value, '}']);
    }

    /**
     * @dataProvider enclosureEndExceptionDataProvider
     *
     * @test
     */
    public function enclosureEndException($value, $exception_class, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->enclosure(['{:', $value]);
    }

    /**
     * @test
     */
    public function enclosureBegin(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_begin    = StringBuilder::factory()->enclosureBegin();

        $stringBuilder        = StringBuilder::factory()->enclosureBegin('${');

        $expected   = StringBuilder::class;
        $actual     = $stringBuilder;
        $this->assertInstanceOf($expected, $actual);

        // ----------------------------------------------
        $expected   = '${';
        $actual     = StringBuilder::factory()->enclosureBegin();
        $this->assertSame($expected, $actual);

        $message    = 'asdf ${html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory()->enclosureBegin($enclosure_begin);

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function enclosureEnd(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_End    = StringBuilder::factory()->enclosureEnd();

        $stringBuilder        = StringBuilder::factory()->enclosureEnd('}}');

        $expected   = StringBuilder::class;
        $actual     = $stringBuilder;
        $this->assertInstanceOf($expected, $actual);

        // ----------------------------------------------
        $expected   = '}}';
        $actual     = StringBuilder::factory()->enclosureEnd();
        $this->assertSame($expected, $actual);

        $message    = 'asdf {:html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory()->enclosureEnd($enclosure_End);

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function factory(): void
    {
        // ==============================================
        $message    = '{:text}';

        $values     = [
            'text'  => StringBuilder::DEFAULT_NAME,
        ];
        StringBuilder::factory(StringBuilder::DEFAULT_NAME, $values);

        $name   = 'a1';
        $values = [
            'text'  => $name,
        ];
        StringBuilder::factory($name, $values);

        $name   = 'b2';
        $values = [
            'text'  => $name,
        ];
        StringBuilder::factory($name, $values);

        // ----------------------------------------------
        $expected   = StringBuilder::DEFAULT_NAME;
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = 'a1';
        $actual     = StringBuilder::get('a1')->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = 'b2';
        $actual     = StringBuilder::get('b2')->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function get(): void
    {
        // ==============================================
        $message    = '{:text}';

        $values     = [
            'text'  => StringBuilder::DEFAULT_NAME,
        ];
        StringBuilder::factory(StringBuilder::DEFAULT_NAME, $values);

        $name   = 'a1';
        $values = [
            'text'  => $name,
        ];
        StringBuilder::factory($name, $values);

        $name   = 'b2';
        $values = [
            'text'  => $name,
        ];
        StringBuilder::factory($name, $values);

        // ----------------------------------------------
        $expected   = StringBuilder::DEFAULT_NAME;
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = 'a1';
        $actual     = StringBuilder::get('a1')->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = 'b2';
        $actual     = StringBuilder::get('b2')->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getName(): void
    {
        // ----------------------------------------------
        $expected   = StringBuilder::DEFAULT_NAME;
        $actual     = StringBuilder::factory()->getName();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = 'testGetName';
        $actual     = StringBuilder::factory($expected)->getName();
        $this->assertSame($expected, $actual);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disable
     *
     * @test
     */
    public function nameSeparator(): void
    {
        // ==============================================
        $values     = [
            '0'         => '00000',
            'begin'     => 'begin',
            'begin:0'   => 'aaaa',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        // ==============================================
        $expected   = ':';
        $actual     = StringBuilder::factory()->nameSeparator();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:begin:0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0:begin}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:begin:0|zero_to_dq|escape}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0:begin|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::get()->nameSeparator('<>');
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        $message    = '{:begin:0}';

        $expected   = 'aaaa';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:begin<>0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider nameSeparatorExceptionDataProvider
     *
     * @test
     */
    public function nameSeparatorException($value, $exception_class, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->nameSeparator($value);
    }

    /**
     * @test
     */
    public function modifierSeparator(): void
    {
        // ==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        // ==============================================
        $expected   = '|';
        $actual     = StringBuilder::factory()->modifierSeparator();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->modifierSeparator('<>');
        $this->assertInstanceOf($expected, $actual);

        $message    = '{:0<>zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0<>zero_to_dq<>escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        $message    = '{:0<>zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider modifierSeparatorExceptionDataProvider
     *
     * @test
     */
    public function modifierSeparatorException($value, $exception_class, $message): void
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->modifierSeparator($value);
    }

    /**
     * @test
     */
    public function modifierSet(): void
    {
        // ==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        // ==============================================
        $expected   = [
            'date'              => DateModifier::class,
            'strtotime'         => StrtotimeModifier::class,
            'escape'            => EscapeModifier::class,
            'e'                 => EscapeModifier::class,
            'js_expr'           => JsExprModifier::class,
            'to_debug'          => ToDebugStringModifier::class,
            'to_debug_str'      => ToDebugStringModifier::class,
            'to_debug_string'   => ToDebugStringModifier::class,
        ];
        $actual     = StringBuilder::factory()->modifierSet();
        $this->assertSame($expected, $actual);

        // ==============================================
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->modifierSet($modifier_list);
        $this->assertInstanceOf($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function modify(): void
    {
        // ==============================================
        $modifier_list  = [
            'zero_to_dq'        => ZeroToDqModifier::class,
            'escape'            => EscapeModifier::class,
            'obj_zero_to_dq'    => new ZeroToDqModifier(),
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        // ==============================================
        StringBuilder::factory();

        $message    = '00000';

        $modifiers  = [
            'zero_to_dq'    => [],
        ];

        $expected   = '"""""';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '00000';

        $modifiers  = [
            'zero_to_dq'    => [],
            'escape'        => [],
        ];

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '00000';

        $modifiers  = [
            'zero_to_dq'    => [],
            'escape'        => [Convert::ESCAPE_TYPE_JAVASCRIPT],
        ];

        $expected   = '\x22\x22\x22\x22\x22';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '00000';

        $modifiers  = [
            'obj_zero_to_dq'    => [],
            'escape'            => [Convert::ESCAPE_TYPE_JAVASCRIPT],
        ];

        $expected   = '\x22\x22\x22\x22\x22';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '00000';

        $modifiers  = [
        ];

        $expected   = '00000';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function remove01(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('StringBuilderキャッシュに無いキーを指定されました。name:\':default:\'');

        StringBuilder::factory();
        StringBuilder::get()->buildMessage('');
        StringBuilder::remove(StringBuilder::DEFAULT_NAME);
        StringBuilder::get();
    }

    /**
     * @test
     */
    public function remove02(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('StringBuilderキャッシュに無いキーを指定されました。name:\'a1\'');

        StringBuilder::factory('a1');
        StringBuilder::get('a1')->buildMessage('');
        StringBuilder::remove('a1');
        StringBuilder::get('a1');
    }

    /**
     * @test
     */
    public function removeDefaultModifier(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $escape_modifier_set    = $this->getEscapeModifierSet();

        StringBuilder::defaultModifierSet($escape_modifier_set)::factory();

        StringBuilder::removeDefaultModifier('classpath')::factory('removed');

        $message    = '{:html|classpath}';

        // ----------------------------------------------
        $expected   = '&lt;a href=&quot;#id&quot;&gt;';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $this->assertNotEquals($escape_modifier_set, StringBuilder::get('removed')->modifierSet());
        $this->assertArrayNotHasKey('classpath', StringBuilder::get('removed')->modifierSet());

        $expected   = '<a href="#id">';
        $actual     = StringBuilder::get('removed')->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function removeDefaultValue(): void
    {
        // ----------------------------------------------
        StringBuilder::setDefaultValue('html', '<a href="#id">')::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME);

        // ----------------------------------------------
        StringBuilder::removeDefaultValue('html')::remove(StringBuilder::DEFAULT_NAME)::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf  zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function removeModifier(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $escape_modifier_set    = $this->getEscapeModifierSet();

        $stringBuilder    = StringBuilder::factory()->modifierSet($escape_modifier_set);

        $message    = '{:html|classpath}';

        // ----------------------------------------------
        $this->assertSame($escape_modifier_set, $stringBuilder->modifierSet());

        // ----------------------------------------------
        $expected   = '&lt;a href=&quot;#id&quot;&gt;';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $stringBuilder->removeModifier('classpath');

        $this->assertNotEquals($escape_modifier_set, $stringBuilder->modifierSet());
        $this->assertArrayNotHasKey('classpath', $stringBuilder->modifierSet());

        $expected   = '<a href="#id">';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function removeValue(): void
    {
        // ----------------------------------------------
        StringBuilder::factory()->setValue('html', '<a href="#id">');

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::get()->removeValue('html');

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf  zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function setDefaultModifier(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $modifier_set   = $this->getEscapeModifierSet();

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';

        // ----------------------------------------------
        $stringBuilder    = StringBuilder::setDefaultModifier('closure', $modifier_set['closure'])::setDefaultModifier('instance', $modifier_set['instance'])::setDefaultModifier('classpath', $modifier_set['classpath'])::factory();

        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        // ----------------------------------------------
        $message    = '{:html|closure}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:html|instance}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $modifier_set   = $this->getDoNothingModifierSet();

        $expected   = '<a href="#id">';

        // ----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $modifier_set   = $this->getEscapeModifierSet();

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';

        // ----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function setModifier(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $modifier_set   = $this->getEscapeModifierSet();

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';

        // ----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);

        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $modifier_set   = $this->getDoNothingModifierSet();

        $expected   = '<a href="#id">';

        // ----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function setValue(): void
    {
        // ==============================================
        $expected   = [];
        $actual     = StringBuilder::factory()->values();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->setValue('html', '<a href="#id">');
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        StringBuilder::factory()->setValue('html', '<a href=\'#id\'>');

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function substitute(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
            'alt'   => '{:html}',
        ];

        $message    = '{:html2}/{:alt2}';

        // ==============================================
        $expected   = '';
        $actual     = StringBuilder::factory()->substitute();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = '/';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = null;

        $expected   = StringBuilder::get();
        $actual     = StringBuilder::get()->substitute($substitute);
        $this->assertSame($expected, $actual);

        $expected   = '{:html2}/{:alt2}';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = '';

        StringBuilder::get()->substitute($substitute);

        $expected   = '/';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = '■';

        StringBuilder::get()->substitute($substitute);

        $expected   = '■/■';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);

        // ==============================================
        $substitute = '{:alt}';

        StringBuilder::get()->substitute($substitute);

        $expected   = '<a href="#id">/<a href="#id">';
        $actual     = StringBuilder::get()->buildMessage($message, $values);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function values(): void
    {
        // ==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = [];
        $actual     = StringBuilder::factory()->values();
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->values($values);
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        // ----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);

        // ----------------------------------------------
        $values     = [
            'html'  => '<a href=\'#id\'>',
        ];

        StringBuilder::factory()->values($values);

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function toDebug(): void
    {
        StringBuilder::factory()->values([
            'value' => null,
        ]);

        $message    = 'asdf {:value|to_debug} zxcv';
        $expected   = 'asdf NULL zxcv';
        $actual     = StringBuilder::get()->buildMessage($message);
        $this->assertSame($expected, $actual);
    }

    protected function getDoNothingConverterSet(): array
    {
        return [
            'closure'   => function(string $name, string $search, array $values): ?string {
                return null;
            },
            'instance'  => new DoNothingConvertr(),
            'classpath' => DoNothingConvertr::class,
        ];
    }

    protected function getUrlConverterSet(): array
    {
        return [
            'closure'   => function(string $name, string $search, array $values): ?string {
                return UrlConvertr::URL_MAP[$name] ?? null;
            },
            'instance'  => new UrlConvertr(),
            'classpath' => UrlConvertr::class,
        ];
    }

    protected function getDoNothingModifierSet(): array
    {
        return [
            'closure'   => function($replace, array $parameters = [], array $context = []) {
                return $replace;
            },
            'instance'  => new DoNothingModifier(),
            'classpath' => DoNothingModifier::class,
        ];
    }

    protected function getEscapeModifierSet(): array
    {
        return [
            'closure'   => function($replace, array $parameters = [], array $context = []) {
                return Convert::htmlEscape($replace);
            },
            'instance'  => new EscapeModifier(),
            'classpath' => EscapeModifier::class,
        ];
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

