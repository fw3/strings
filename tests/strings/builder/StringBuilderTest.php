<?php

namespace fw3\tests\strings\builder;

use PHPUnit\Framework\TestCase;
use fw3\strings\builder\StringBuilder;
use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\strings\builder\modifiers\ModifierTrait;
use fw3\strings\builder\modifiers\datetime\DateModifier;
use fw3\strings\builder\modifiers\datetime\StrtotimeModifier;
use fw3\strings\builder\modifiers\security\EscapeModifier;
use fw3\strings\builder\traits\converter\ConverterInterface;
use fw3\strings\builder\traits\converter\ConverterTrait;
use fw3\strings\converter\Convert;
use InvalidArgumentException;
use OutOfBoundsException;
use fw3\strings\builder\modifiers\strings\ToDebugStringModifier;

/**
 * @runTestsInSeparateProcesses
 */
class StringBuilderTest extends TestCase
{
    protected $internalEncoding = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->internalEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');
    }

    public function tearDown(): void
    {
        mb_internal_encoding($this->internalEncoding);

        parent::tearDown();
    }

    public function testBuild()
    {
        $stringBuilder    = StringBuilder::factory();

        //----------------------------------------------
        $expected   = '';
        $actual     = $stringBuilder->build('');
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|e}';
        $values     = ['"'];

        $expected   = '&quot;';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|e}';
        $values     = (object) ['"'];

        $expected   = '&quot;';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|e}{:ickx}{:0|e}';
        $values     = (object) ['"'];
        $converter  = UrlConvertr::class;

        $expected   = '&quot;https://ickx.jp&quot;';
        $actual     = $stringBuilder->build($message, $values, $converter);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|e}{:ickx}{:0|e}';
        $values     = (object) ['"'];
        $converter  = new UrlConvertr();

        $expected   = '&quot;https://ickx.jp&quot;';
        $actual     = $stringBuilder->build($message, $values, $converter);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|e}{:ickx}{:0|e}';
        $values     = (object) ['"'];
        $converter  = function (string $name, string $search, array $values) {
            return $name === '0' ? '0' : $name;
        };

        $expected   = '&quot;ickx&quot;';
        $actual     = $stringBuilder->build($message, $values, $converter);
        $this->assertEquals($expected, $actual);
    }

    public function testCharacterEncoding()
    {
        //==============================================
        $character_encoding = StringBuilder::factory()->characterEncoding();

        $expected   = 'UTF-8';
        $actual     = $character_encoding;
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::get()->characterEncoding('SJIS-win');

        $expected   = 'SJIS-win';
        $actual     = StringBuilder::get()->characterEncoding();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @preserveGlobalState disable
     */
    public function testConverter()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getUrlConverterSet();

        //----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertEquals($expected, $actual);

        //==============================================
        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME);

        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getDoNothingConverterSet();

        //----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::factory()->converter($converter_set[$set_name]);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::factory()->converter();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultCharacterEncoding()
    {
        //==============================================
        $character_encoding = StringBuilder::defaultCharacterEncoding();

        $expected   = null;
        $actual     = $character_encoding;
        $this->assertEquals($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $expected   = 'UTF-8';
        $actual     = StringBuilder::get()->characterEncoding();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::defaultCharacterEncoding('SJIS-win');

        $expected   = 'SJIS-win';
        $actual     = StringBuilder::defaultCharacterEncoding();
        $this->assertEquals($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        //----------------------------------------------
        $expected   = 'SJIS-win';
        $actual     = StringBuilder::get()->characterEncoding();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @preserveGlobalState disable
     */
    public function testDefaultConverter()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getUrlConverterSet();

        //----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href="https://ickx.jp">https://ickx.jp</a><br><a href="https://effy.info">https://effy.info</a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertEquals($expected, $actual);

        //==============================================
        $values     = [
            'html'  => '<a href="{:ickx}">{:ickx}</a><br><a href="{:effy}">{:effy}</a>',
        ];

        $converter_set   = $this->getDoNothingConverterSet();

        //----------------------------------------------
        $set_name       = 'closure';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'instance';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $set_name       = 'classpath';

        $stringBuilder    = StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultConverter($converter_set[$set_name])::factory();
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html}';
        $expected   = '<a href=""></a><br><a href=""></a>';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        $expected   = $converter_set[$set_name];
        $actual     = StringBuilder::defaultConverter();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultEnclosure()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultEnclosure('${', '}}');
        $this->assertEquals($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $expected   = ['begin' => '${', 'end' => '}}'];
        $actual     = StringBuilder::defaultEnclosure();
        $this->assertEquals($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosure(['begin' => '${', 'end' => '}}', '{:', '}'])::factory();

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf {:html} zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosure(['${', '}}'])::factory();

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function defaultEnclosureBeginExceptionDataProvider(): array
    {
        return [
            [':', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['}', 'クラスデフォルトの変数部開始文字列にクラスデフォルトの変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public function defaultEnclosureEndExceptionDataProvider(): array
    {
        return [
            [':', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', 'クラスデフォルトの変数部終了文字列にクラスデフォルトの変数部開始文字列と同じ値を設定しようとしています。value:{:'],
        ];
    }

    /**
     * @dataProvider defaultEnclosureBeginExceptionDataProvider
     */
    public function testDefaultEnclosureBeginException($value, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException(InvalidArgumentException::class);
        StringBuilder::defaultEnclosure([$value, '}']);
    }

    /**
     * @dataProvider defaultEnclosureEndExceptionDataProvider
     */
    public function testDefaultEnclosureEndExceptionDataProvider($value, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException(InvalidArgumentException::class);
        StringBuilder::defaultEnclosure(['{:', $value]);
    }

    public function testDefaultEnclosureBegin()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_begin    = StringBuilder::defaultEnclosureBegin();

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultEnclosureBegin('${');
        $this->assertEquals($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $expected   = '${';
        $actual     = StringBuilder::defaultEnclosureBegin();
        $this->assertEquals($expected, $actual);

        $message    = 'asdf ${html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosureBegin($enclosure_begin)::factory();

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultValue()
    {
        //==============================================
        $expected   = [];
        $actual     = StringBuilder::defaultValues();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::setDefaultValue('html', '<a href="#id">')::factory();
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME);

        //----------------------------------------------
        $actual     = StringBuilder::setDefaultValue('html', '<a href=\'#id\'>')::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultEnclosureEnd()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_End    = StringBuilder::defaultEnclosureEnd();

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultEnclosureEnd('}}');
        $this->assertEquals($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $expected   = '}}';
        $actual     = StringBuilder::defaultEnclosureEnd();
        $this->assertEquals($expected, $actual);

        $message    = 'asdf {:html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultEnclosureEnd($enclosure_End)::factory();

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultNameSeparator()
    {
        //==============================================
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

        //==============================================
        $expected   = ':';
        $actual     = StringBuilder::defaultNameSeparator();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory();

        $message    = '{:begin:0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0:begin}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:begin:0|zero_to_dq|escape}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0:begin|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultNameSeparator('<>');
        $this->assertEquals($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $message    = '{:begin:0}';

        $expected   = 'aaaa';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:begin<>0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function defaultNameSeparatorExceptionDataProvider(): array
    {
        return [
            ['|', InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', InvalidArgumentException::class, 'クラスデフォルトの変数名セパレータにクラスデフォルトの変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    /**
     * @dataProvider defaultNameSeparatorExceptionDataProvider
     */
    public function testDefaultNameSeparatorException($value, $exception_class, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::defaultNameSeparator($value);
    }

    public function testDefaultModifierSeparator()
    {
        //==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        //==============================================
        $expected   = '|';
        $actual     = StringBuilder::defaultModifierSeparator();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultModifierSeparator('<>');
        $this->assertEquals($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $message    = '{:0<>zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0<>zero_to_dq<>escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0<>zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function defaultModifierSeparatorExceptionDataProvider(): array
    {
        return [
            [':', InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数名セパレータと同じ値を設定しようとしています。value::'],
            ['■', InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', InvalidArgumentException::class, 'クラスデフォルトの修飾子セパレータにクラスデフォルトの変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    /**
     * @dataProvider defaultModifierSeparatorExceptionDataProvider
     */
    public function testDefaultModifierSeparatorException($value, $exception_class, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::defaultModifierSeparator($value);
    }

    public function testDefaultModifierSet()
    {
        //==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        //==============================================
        $expected   = [
            'date'      => DateModifier::class,
            'strtotime' => StrtotimeModifier::class,
            'escape'    => EscapeModifier::class,
            'e'         => EscapeModifier::class,
            'to_debug'          => ToDebugStringModifier::class,
            'to_debug_str'      => ToDebugStringModifier::class,
            'to_debug_string'   => ToDebugStringModifier::class,
        ];
        $actual     = StringBuilder::defaultModifierSet();
        $this->assertEquals($expected, $actual);

        //==============================================
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultModifierSet($modifier_list);
        $this->assertEquals($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        //----------------------------------------------
        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultSubstitute()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
            'alt'   => '{:html}',
        ];

        $message    = '{:html2}/{:alt2}';

        //==============================================
        $expected   = '';
        $actual     = StringBuilder::defaultSubstitute();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory();

        $expected   = '/';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = null;

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultSubstitute($substitute);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $expected   = '{:html2}/{:alt2}';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = '';

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultSubstitute($substitute)::factory();

        $expected   = '/';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = '■';

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultSubstitute($substitute)::factory();

        $expected   = '■/■';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = '{:alt}';

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultSubstitute($substitute)::factory();

        $expected   = '<a href="#id">/<a href="#id">';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultValues()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = [];
        $actual     = StringBuilder::defaultValues();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::defaultValues($values);
        $this->assertEquals($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $values     = [
            'html'  => '<a href=\'#id\'>',
        ];

        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::defaultValues($values)::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testEnclosure()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->enclosure('${', '}}');
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $expected   = ['begin' => '${', 'end' => '}}'];
        $actual     = StringBuilder::factory()->enclosure();
        $this->assertEquals($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory()->enclosure(['begin' => '${', 'end' => '}}', '{:', '}']);

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf {:html} zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory()->enclosure(['${', '}}']);

        $message    = 'asdf ${html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function enclosureBeginExceptionDataProvider(): array
    {
        return [
            [':', InvalidArgumentException::class, '変数部開始文字列に変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', InvalidArgumentException::class, '変数部開始文字列に修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', InvalidArgumentException::class, '変数部開始文字列に変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['}', InvalidArgumentException::class, '変数部開始文字列に変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    public function enclosureEndExceptionDataProvider(): array
    {
        return [
            [':', InvalidArgumentException::class, '変数部終了文字列に変数名セパレータと同じ値を設定しようとしています。value::'],
            ['|', InvalidArgumentException::class, '変数部終了文字列に修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', InvalidArgumentException::class, '変数部終了文字列に変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', InvalidArgumentException::class, '変数部終了文字列に変数部開始文字列と同じ値を設定しようとしています。value:{:'],
        ];
    }

    /**
     * @dataProvider enclosureBeginExceptionDataProvider
     */
    public function testEnclosureBeginException($value, $exception_class, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->enclosure([$value, '}']);
    }

    /**
     * @dataProvider enclosureEndExceptionDataProvider
     */
    public function testEnclosureEndExceptionDataProvider($value, $exception_class, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->enclosure(['{:', $value]);
    }

    public function testEnclosureBegin()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_begin    = StringBuilder::factory()->enclosureBegin();

        $stringBuilder        = StringBuilder::factory()->enclosureBegin('${');

        $expected   = StringBuilder::class;
        $actual     = $stringBuilder;
        $this->assertInstanceOf($expected, $actual);

        //----------------------------------------------
        $expected   = '${';
        $actual     = StringBuilder::factory()->enclosureBegin();
        $this->assertEquals($expected, $actual);

        $message    = 'asdf ${html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory()->enclosureBegin($enclosure_begin);

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testEnclosureEnd()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $enclosure_End    = StringBuilder::factory()->enclosureEnd();

        $stringBuilder        = StringBuilder::factory()->enclosureEnd('}}');

        $expected   = StringBuilder::class;
        $actual     = $stringBuilder;
        $this->assertInstanceOf($expected, $actual);

        //----------------------------------------------
        $expected   = '}}';
        $actual     = StringBuilder::factory()->enclosureEnd();
        $this->assertEquals($expected, $actual);

        $message    = 'asdf {:html}} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory()->enclosureEnd($enclosure_End);

        $message    = 'asdf {:html} zxcv';

        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

    }

    public function testFactory()
    {
        //==============================================
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

        //----------------------------------------------
        $expected   = StringBuilder::DEFAULT_NAME;
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = 'a1';
        $actual     = StringBuilder::get('a1')->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = 'b2';
        $actual     = StringBuilder::get('b2')->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        //==============================================
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

        //----------------------------------------------
        $expected   = StringBuilder::DEFAULT_NAME;
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = 'a1';
        $actual     = StringBuilder::get('a1')->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = 'b2';
        $actual     = StringBuilder::get('b2')->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testGetName()
    {
        //----------------------------------------------
        $expected   = StringBuilder::DEFAULT_NAME;
        $actual     = StringBuilder::factory()->getName();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = 'testGetName';
        $actual     = StringBuilder::factory($expected)->getName();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disable
     */
    public function testNameSeparator()
    {
        //==============================================
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

        //==============================================
        $expected   = ':';
        $actual     = StringBuilder::factory()->nameSeparator();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:begin:0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0:begin}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:begin:0|zero_to_dq|escape}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0:begin|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::get()->nameSeparator('<>');
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        $message    = '{:begin:0}';

        $expected   = 'aaaa';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:begin<>0}';

        $expected   = 'begin';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function nameSeparatorExceptionDataProvider(): array
    {
        return [
            ['|', InvalidArgumentException::class, '変数名セパレータに修飾子セパレータと同じ値を設定しようとしています。value:|'],
            ['■', InvalidArgumentException::class, '変数名セパレータに変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', InvalidArgumentException::class, '変数名セパレータに変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', InvalidArgumentException::class, '変数名セパレータに変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    /**
     * @dataProvider nameSeparatorExceptionDataProvider
     */
    public function testNameSeparatorException($value, $exception_class, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->nameSeparator($value);
    }

    public function testModifierSeparator()
    {
        //==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        //==============================================
        $expected   = '|';
        $actual     = StringBuilder::factory()->modifierSeparator();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->modifierSeparator('<>');
        $this->assertInstanceOf($expected, $actual);

        $message    = '{:0<>zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0<>zero_to_dq<>escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        $message    = '{:0<>zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function modifierSeparatorExceptionDataProvider(): array
    {
        return [
            [':', InvalidArgumentException::class, '修飾子セパレータに変数名セパレータと同じ値を設定しようとしています。value::'],
            ['■', InvalidArgumentException::class, '修飾子セパレータに変数が存在しない場合の代替出力と同じ値を設定しようとしています。value:■'],
            ['{:', InvalidArgumentException::class, '修飾子セパレータに変数部開始文字列と同じ値を設定しようとしています。value:{:'],
            ['}', InvalidArgumentException::class, '修飾子セパレータに変数部終了文字列と同じ値を設定しようとしています。value:}'],
        ];
    }

    /**
     * @dataProvider modifierSeparatorExceptionDataProvider
     */
    public function testModifierSeparatorException($value, $exception_class, $message)
    {
        StringBuilder::defaultSubstitute('■');

        $this->expectExceptionMessage($message);
        $this->expectException($exception_class);
        StringBuilder::factory()->modifierSeparator($value);
    }

    public function testModifierSet()
    {
        //==============================================
        $values     = [
            '0'         => '00000',
        ];

        $modifier_list  = [
            'zero_to_dq'    => ZeroToDqModifier::class,
            'escape'        => EscapeModifier::class,
        ];

        //==============================================
        $expected   = [
            'date'      => DateModifier::class,
            'strtotime' => StrtotimeModifier::class,
            'escape'    => EscapeModifier::class,
            'e'         => EscapeModifier::class,
            'to_debug'          => ToDebugStringModifier::class,
            'to_debug_str'      => ToDebugStringModifier::class,
            'to_debug_string'   => ToDebugStringModifier::class,
        ];
        $actual     = StringBuilder::factory()->modifierSet();
        $this->assertEquals($expected, $actual);

        //==============================================
        StringBuilder::factory();

        $message    = '{:0|zero_to_dq}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '00000';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        StringBuilder::remove(StringBuilder::DEFAULT_NAME)::factory();

        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->modifierSet($modifier_list);
        $this->assertInstanceOf($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|zero_to_dq}';

        $expected   = '"""""';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:0|zero_to_dq|escape}';

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testModify()
    {
        //==============================================
        $modifier_list  = [
            'zero_to_dq'        => ZeroToDqModifier::class,
            'escape'            => EscapeModifier::class,
            'obj_zero_to_dq'    => new ZeroToDqModifier(),
        ];

        StringBuilder::defaultModifierSet($modifier_list);

        //==============================================
        StringBuilder::factory();

        $message    = '00000';

        $modifiers  = [
            'zero_to_dq'    => [],
        ];

        $expected   = '"""""';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '00000';

        $modifiers  = [
            'zero_to_dq'    => [],
            'escape'        => [],
        ];

        $expected   = '&quot;&quot;&quot;&quot;&quot;';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '00000';

        $modifiers  = [
            'zero_to_dq'    => [],
            'escape'        => [Convert::ESCAPE_TYPE_JAVASCRIPT],
        ];

        $expected   = '\x22\x22\x22\x22\x22';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '00000';

        $modifiers  = [
            'obj_zero_to_dq'    => [],
            'escape'            => [Convert::ESCAPE_TYPE_JAVASCRIPT],
        ];

        $expected   = '\x22\x22\x22\x22\x22';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '00000';

        $modifiers  = [
        ];

        $expected   = '00000';
        $actual     = StringBuilder::get()->modify($message, $modifiers);
        $this->assertEquals($expected, $actual);
    }

    public function testRemove01()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('StringBuilderキャッシュに無いキーを指定されました。name:\':default:\'');

        StringBuilder::factory();
        StringBuilder::get()->build('');
        StringBuilder::remove(StringBuilder::DEFAULT_NAME);
        StringBuilder::get();
    }

    public function testRemove02()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('StringBuilderキャッシュに無いキーを指定されました。name:\'a1\'');

        StringBuilder::factory('a1');
        StringBuilder::get('a1')->build('');
        StringBuilder::remove('a1');
        StringBuilder::get('a1');
    }

    public function testRemoveDefaultModifier()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $escape_modifier_set    = $this->getEscapeModifierSet();

        StringBuilder::defaultModifierSet($escape_modifier_set)::factory();

        StringBuilder::removeDefaultModifier('classpath')::factory('removed');

        $message    = '{:html|classpath}';

        //----------------------------------------------
        $expected   = '&lt;a href=&quot;#id&quot;&gt;';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $this->assertNotEquals($escape_modifier_set, StringBuilder::get('removed')->modifierSet());
        $this->assertArrayNotHasKey('classpath', StringBuilder::get('removed')->modifierSet());

        $expected   = '<a href="#id">';
        $actual     = StringBuilder::get('removed')->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testRemoveDefaultValue()
    {
        //----------------------------------------------
        StringBuilder::setDefaultValue('html', '<a href="#id">')::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        StringBuilder::remove(StringBuilder::DEFAULT_NAME);

        //----------------------------------------------
        StringBuilder::removeDefaultValue('html')::remove(StringBuilder::DEFAULT_NAME)::factory();

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf  zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testRemoveModifier()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $escape_modifier_set    = $this->getEscapeModifierSet();

        $stringBuilder    = StringBuilder::factory()->modifierSet($escape_modifier_set);

        $message    = '{:html|classpath}';

        //----------------------------------------------
        $this->assertEquals($escape_modifier_set, $stringBuilder->modifierSet());

        //----------------------------------------------
        $expected   = '&lt;a href=&quot;#id&quot;&gt;';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $stringBuilder->removeModifier('classpath');

        $this->assertNotEquals($escape_modifier_set, $stringBuilder->modifierSet());
        $this->assertArrayNotHasKey('classpath', $stringBuilder->modifierSet());

        $expected   = '<a href="#id">';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testRemoveValue()
    {
        //----------------------------------------------
        StringBuilder::factory()->setValue('html', '<a href="#id">');

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::get()->removeValue('html');

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf  zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultModifier()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $modifier_set   = $this->getEscapeModifierSet();

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';

        //----------------------------------------------
        $stringBuilder    = StringBuilder
        ::setDefaultModifier('closure', $modifier_set['closure'])
        ::setDefaultModifier('instance', $modifier_set['instance'])
        ::setDefaultModifier('classpath', $modifier_set['classpath'])
        ::factory();

        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        //----------------------------------------------
        $message    = '{:html|closure}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:html|instance}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $modifier_set   = $this->getDoNothingModifierSet();

        $expected   = '<a href="#id">';

        //----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $modifier_set   = $this->getEscapeModifierSet();

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';

        //----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = $stringBuilder->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testSetModifier()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $modifier_set   = $this->getEscapeModifierSet();

        $expected   = '&lt;a href=&quot;#id&quot;&gt;';

        //----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);

        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $modifier_set   = $this->getDoNothingModifierSet();

        $expected   = '<a href="#id">';

        //----------------------------------------------
        $name       = 'closure';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|closure}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'instance';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|instance}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $name       = 'classpath';
        $modifier   = $modifier_set[$name];

        $stringBuilder    = StringBuilder::factory()->setModifier($name, $modifier);
        $this->assertInstanceOf(StringBuilder::class, $stringBuilder);

        $message    = '{:html|classpath}';
        $actual     = $stringBuilder->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testSetValue()
    {
        //==============================================
        $expected   = [];
        $actual     = StringBuilder::factory()->values();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->setValue('html', '<a href="#id">');
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        StringBuilder::factory()->setValue('html', '<a href=\'#id\'>');

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);
    }

    public function testSubstitute()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
            'alt'   => '{:html}',
        ];

        $message    = '{:html2}/{:alt2}';

        //==============================================
        $expected   = '';
        $actual     = StringBuilder::factory()->substitute();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = '/';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = null;

        $expected   = StringBuilder::get();
        $actual     = StringBuilder::get()->substitute($substitute);
        $this->assertEquals($expected, $actual);

        $expected   = '{:html2}/{:alt2}';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = '';

        StringBuilder::get()->substitute($substitute);

        $expected   = '/';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = '■';

        StringBuilder::get()->substitute($substitute);

        $expected   = '■/■';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);

        //==============================================
        $substitute = '{:alt}';

        StringBuilder::get()->substitute($substitute);

        $expected   = '<a href="#id">/<a href="#id">';
        $actual     = StringBuilder::get()->build($message, $values);
        $this->assertEquals($expected, $actual);
    }

    public function testValues()
    {
        //==============================================
        $values     = [
            'html'  => '<a href="#id">',
        ];

        $expected   = [];
        $actual     = StringBuilder::factory()->values();
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $expected   = StringBuilder::class;
        $actual     = StringBuilder::factory()->values($values);
        $this->assertInstanceOf($expected, $actual);

        StringBuilder::factory();

        //----------------------------------------------
        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href="#id"> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);

        //----------------------------------------------
        $values     = [
            'html'  => '<a href=\'#id\'>',
        ];

        StringBuilder::factory()->values($values);

        $message    = 'asdf {:html} zxcv';
        $expected   = 'asdf <a href=\'#id\'> zxcv';
        $actual     = StringBuilder::get()->build($message);
        $this->assertEquals($expected, $actual);
    }

    protected function getDoNothingConverterSet(): array
    {
        return [
            'closure'   => function (string $name, string $search, array $values): ?string {
                return null;
            },
            'instance'  => new DoNothingConvertr(),
            'classpath' => DoNothingConvertr::class,
        ];
    }

    protected function getUrlConverterSet(): array
    {
        return [
            'closure'   => function (string $name, string $search, array $values): ?string {
                return UrlConvertr::URL_MAP[$name] ?? null;
            },
            'instance'  => new UrlConvertr(),
            'classpath' => UrlConvertr::class,
        ];
    }

    protected function getDoNothingModifierSet(): array
    {
        return [
            'closure'   => function ($replace, array $parameters = [], array $context = []) {
                return $replace;
            },
            'instance'  => new DoNothingModifier(),
            'classpath' => DoNothingModifier::class,
        ];
    }

    protected function getEscapeModifierSet(): array
    {
        return [
            'closure'   => function ($replace, array $parameters = [], array $context = []) {
                return Convert::htmlEscape($replace);
            },
            'instance'  => new EscapeModifier(),
            'classpath' => EscapeModifier::class,
        ];
    }
}

class DoNothingConvertr implements ConverterInterface
{
    use ConverterTrait;

    /**
     * 現在の変数名を元に値を返します。
     *
     * @param   string      $name   現在の変数名
     * @param   string      $search 変数名の元の文字列
     * @param   array       $values 変数
     * @return  string|null 値
     */
    public static function convert(string $name, string $search, array $values): ?string
    {
        return null;
    }
}

class UrlConvertr implements ConverterInterface
{
    use ConverterTrait;

    public const URL_MAP = [
        'ickx'  => 'https://ickx.jp',
        'effy'  => 'https://effy.info',
    ];

    /**
     * 現在の変数名を元に値を返します。
     *
     * @param   string      $name   現在の変数名
     * @param   string      $search 変数名の元の文字列
     * @param   array       $values 変数
     * @return  string|null 値
     */
    public static function convert(string $name, string $search, array $values): ?string
    {
        return static::URL_MAP[$name] ?? $search;
    }
}

class DoNothingModifier implements ModifierInterface
{
    use ModifierTrait;

    /**
     * 置き換え値を修飾して返します。
     *
     * @param   mixed   $replace    置き換え値
     * @param   array   $parameters パラメータ
     * @param   array   $context    コンテキスト
     * @return  mixed   修飾した置き換え値
     */
    public static function modify($replace, array $parameters = [], array $context = [])
    {
        return $replace;
    }
}

class NgClass
{
    /**
     * 現在の変数名を元に値を返します。
     *
     * @param   string      $name   現在の変数名
     * @param   string      $search 変数名の元の文字列
     * @param   array       $values 変数
     * @return  string|null 値
     */
    public static function convert(string $name, string $search, array $values): ?string
    {
        return null;
    }

    /**
     * 置き換え値を修飾して返します。
     *
     * @param   mixed   $replace    置き換え値
     * @param   array   $parameters パラメータ
     * @param   array   $context    コンテキスト
     * @return  mixed   修飾した置き換え値
     */
    public static function modify($replace, array $parameters = [], array $context = [])
    {
        return $replace;
    }
}


class ZeroToDqModifier implements ModifierInterface
{
    use ModifierTrait;

    /**
     * 置き換え値を修飾して返します。
     *
     * @param   mixed   $replace    置き換え値
     * @param   array   $parameters パラメータ
     * @param   array   $context    コンテキスト
     * @return  mixed   修飾した置き換え値
     */
    public static function modify($replace, array $parameters = [], array $context = [])
    {
        return str_replace(0, '"', $replace);
    }
}
