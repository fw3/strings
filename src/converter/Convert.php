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

namespace fw3\strings\converter;

use fw3\strings\tabular\Tabular;

/**
 * 変数の文字列変換メソッド群です。
 */
class Convert
{
    // ==============================================
    // constants
    // ==============================================
    // escape
    // ----------------------------------------------
    /**
     * @var string エスケープタイプ：HTML
     */
    public const ESCAPE_TYPE_HTML       = 'html';

    /**
     * @var string エスケープタイプ：JavaScript
     */
    public const ESCAPE_TYPE_JAVASCRIPT = 'javascript';

    /**
     * @var string エスケープタイプ：JavaScript (省略形)
     */
    public const ESCAPE_TYPE_JS         = 'javascript';

    /**
     * @var string エスケープタイプ：CSS
     */
    public const ESCAPE_TYPE_CSS           = 'css';

    /**
     * @var string エスケープタイプ：シェル引数
     */
    public const ESCAPE_TYPE_SHELL         = 'shell';

    /**
     * @var int 基底となるエスケープフラグ
     */
    public const BASE_ESCAPE_FLAGS      =  \ENT_QUOTES;

    /**
     * @var int HTML関連のエスケープフラグ
     */
    public const HTML_ESCAPE_FLAGS  = [
        \ENT_HTML401,
        \ENT_HTML5,
        \ENT_XHTML,
        \ENT_XML1,
    ];

    /**
     * @var array デフォルトでの文字エンコーディング検出順序
     */
    public const DETECT_ENCODING_ORDER  = [
        'eucJP-win',
        'CP932',
        'SJIS-win',
        'JIS',
        'ISO-2022-JP',
        'UTF-8',
        'ASCII',
    ];

    /**
     * @var string JavaScript用エンコーディング
     */
    public const JAVASCRIPT_ENCODING    = 'UTF-8';

    /**
     * @var int toDebugStringにおけるデフォルトインデントレベル
     */
    public const TO_DEBUG_STRING_DEFAULT_INDENT_LEVEL  = 0;

    /**
     * @var int toDebugStringにおけるデフォルトインデント幅
     */
    public const TO_DEBUG_STRING_DEFAULT_INDENT_WIDTH  = 4;

    /**
     * @var array エスケープタイプマップ
     */
    public static array $ESCAPE_TYPE_MAP  = [
        self::ESCAPE_TYPE_HTML          => self::ESCAPE_TYPE_HTML,
        self::ESCAPE_TYPE_JAVASCRIPT    => self::ESCAPE_TYPE_JAVASCRIPT,
        self::ESCAPE_TYPE_CSS           => self::ESCAPE_TYPE_CSS,
        self::ESCAPE_TYPE_SHELL         => self::ESCAPE_TYPE_SHELL,
    ];

    // ==============================================
    // methods
    // ==============================================
    // case conversion
    // ----------------------------------------------
    /**
     * 文字列をスネークケースに変換します。
     *
     * @param  string            $subject   スネークケースに変換する文字列
     * @param  bool              $trim      変換後に先頭の"_"をトリムするかどうか trueの場合はトリムする
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            スネークケースに変換された文字列
     */
    public static function toSnakeCase(string $subject, bool $trim = true, $separator = [' ', '-']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '_', $subject);
        }

        $subject    = \preg_replace(\mb_internal_encoding() === 'UTF-8' ? '/_*([A-Z])/u' : '/_*([A-Z])/', '_${1}', $subject);

        return $trim ? \ltrim($subject, '_') : $subject;
    }

    /**
     * 文字列をアッパースネークケースに変換します。
     *
     * @param  string            $subject   アッパースネークケースに変換する文字列
     * @param  bool              $trim      変換後に先頭の"_"をトリムするかどうか trueの場合はトリムする
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            アッパースネークケースに変換された文字列
     */
    public static function toUpperSnakeCase(string $subject, bool $trim = true, $separator = [' ', '-']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '_', $subject);
        }

        $subject    = \preg_replace(\mb_internal_encoding() === 'UTF-8' ? '/_*([A-Z])/u' : '/_*([A-Z])/', '_${1}', $subject);

        return \strtoupper($trim ? \ltrim($subject, '_') : $subject);
    }

    /**
     * 文字列をロウアースネークケースに変換します。
     *
     * @param  string            $subject   スネークケースに変換する文字列
     * @param  bool              $trim      変換後に先頭の"_"をトリムするかどうか trueの場合はトリムする
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            ロウアースネークケースに変換された文字列
     */
    public static function toLowerSnakeCase(string $subject, bool $trim = true, $separator = [' ', '-']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '_', $subject);
        }

        $subject    = \preg_replace(\mb_internal_encoding() === 'UTF-8' ? '/_*([A-Z])/u' : '/_*([A-Z])/', '_${1}', $subject);

        return \strtolower($trim ? \ltrim($subject, '_') : $subject);
    }

    /**
     * 文字列をチェインケースに変換します。
     *
     * @param  string            $subject   チェインケースに変換する文字列
     * @param  bool              $trim      変換後に先頭の"-"をトリムするかどうか trueの場合はトリムする
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            チェインケースに変換された文字列
     */
    public static function toChainCase(string $subject, bool $trim = true, $separator = [' ', '_']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '-', $subject);
        }

        $subject    = \preg_replace(\mb_internal_encoding() === 'UTF-8' ? '/\-*([A-Z])/u' : '/\-*([A-Z])/', '-${1}', $subject);

        return $trim ? \ltrim($subject, '-') : $subject;
    }

    /**
     * 文字列をアッパーチェインケースに変換します。
     *
     * @param  string            $subject   チェインケースに変換する文字列
     * @param  bool              $trim      変換後に先頭の"-"をトリムするかどうか trueの場合はトリムする
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            アッパーチェインケースに変換された文字列
     */
    public static function toUpperChainCase(string $subject, bool $trim = true, $separator = [' ', '_']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '-', $subject);
        }

        $subject    = \preg_replace(\mb_internal_encoding() === 'UTF-8' ? '/\-*([A-Z])/u' : '/\-*([A-Z])/', '-${1}', $subject);

        return \strtoupper($trim ? \ltrim($subject, '-') : $subject);
    }

    /**
     * 文字列をロウアーチェインケースに変換します。
     *
     * @param  string            $subject   チェインケースに変換する文字列
     * @param  bool              $trim      変換後に先頭の"-"をトリムするかどうか trueの場合はトリムする
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            ロウアーチェインケースに変換された文字列
     */
    public static function toLowerChainCase(string $subject, bool $trim = true, $separator = [' ', '_']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '-', $subject);
        }

        $subject    = \preg_replace(\mb_internal_encoding() === 'UTF-8' ? '/\-*([A-Z])/u' : '/\-*([A-Z])/', '-${1}', $subject);

        return \strtolower($trim ? \ltrim($subject, '-') : $subject);
    }

    /**
     * 文字列をキャメルケースに変換します。
     *
     * @param  string            $subject   キャメルケースに変換する文字列
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            キャメルケースに変換された文字列
     */
    public static function toCamelCase(string $subject, $separator = [' ', '-']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '_', $subject);
        }

        $subject    = \ltrim(\strtr($subject, ['_' => ' ']), ' ');

        return \strtr(\mb_substr($subject, 0, 1) . \mb_substr(\ucwords($subject), 1), [' ' => '']);
    }

    /**
     * 文字列をスネークケースからアッパーキャメルケースに変換します。
     *
     * @param  string            $subject   アッパーキャメルケースに変換する文字列
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            アッパーキャメルケースに変換された文字列
     */
    public static function toUpperCamelCase(string $subject, $separator = [' ', '-']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '_', $subject);
        }

        return \ucfirst(\strtr(\ucwords(\strtr($subject, ['_' => ' '])), [' ' => '']));
    }

    /**
     * 文字列をスネークケースからロウアーキャメルケースに変換します。
     *
     * @param  string            $subject   ロウアーキャメルケースに変換する文字列
     * @param  null|string|array $separator 単語の閾に用いる文字
     * @return string            ロウアーキャメルケースに変換された文字列
     */
    public static function toLowerCamelCase(string $subject, $separator = [' ', '-']): string
    {
        if ($separator !== null) {
            $subject    = \str_replace($separator, '_', $subject);
        }

        return \lcfirst(\strtr(\ucwords(\strtr($subject, ['_' => ' '])), [' ' => '']));
    }

    // ----------------------------------------------
    // escape
    // ----------------------------------------------
    /**
     * 利用可能なエスケープタイプか検証します。
     *
     * @param  string $escape_type 検証するエスケープタイプ
     * @return bool   利用可能なエスケープタイプかどうか
     */
    public static function validEscapeType(string $escape_type): bool
    {
        return isset(static::$ESCAPE_TYPE_MAP[$escape_type]);
    }

    /**
     * 文字列のエスケープを行います。
     *
     * @param  string      $value    エスケープする文字列
     * @param  string      $type     エスケープタイプ
     * @param  array       $options  オプション
     * @param  null|string $encoding エンコーディング
     * @return string      エスケープされた文字列
     */
    public static function escape(string $value, string $type = self::ESCAPE_TYPE_HTML, array $options = [], ?string $encoding = null): string
    {
        if ($type === static::ESCAPE_TYPE_HTML) {
            return static::htmlEscape($value, $options, $encoding);
        }

        if ($type === static::ESCAPE_TYPE_JAVASCRIPT) {
            return static::jsEscape($value, $options, $encoding);
        }

        if ($type === static::ESCAPE_TYPE_JS) {
            return static::jsEscape($value, $options, $encoding);
        }

        if ($type === static::ESCAPE_TYPE_CSS) {
            return static::cssEscape($value, $options, $encoding);
        }

        if ($type === static::ESCAPE_TYPE_SHELL) {
            return static::shellEscape($value, $options, $encoding);
        }

        return $value;
    }

    /**
     * HTML文字列のエスケープを行います。
     *
     * @param  string      $value    エスケープするHTML文字列
     * @param  array       $options  オプション
     *                               [
     *                               'flags' => array htmlspecialcharsに与えるフラグ
     *                               ]
     * @param  null|string $encoding エンコーディング
     * @return string      エスケープされたHTML文字列
     */
    public static function htmlEscape(string $value, array $options = [], ?string $encoding = null): string
    {
        $encoding   = $encoding ?? \mb_internal_encoding();

        // PHP8.1での誤った修正により`SJIS-win`は削除されました。
        // 過去実装でも極力そのまま動作させるために、内部的にはCP932を設定したものとみなし、処理を続行させます。
        if (\version_compare(\PHP_VERSION, '8.1')) {
            if ($encoding === 'SJIS-win') {
                $encoding = 'CP932';
            }
        }

        if (!\mb_check_encoding($value, $encoding)) {
            throw new \InvalidArgumentException(\sprintf('不正なエンコーディングが検出されました。encoding:%s, value_encoding:%s', Convert::toDebugString($encoding ?? \mb_internal_encoding()), Convert::toDebugString(\mb_detect_encoding($value, static::DETECT_ENCODING_ORDER, true))));
        }

        $flags  = static::BASE_ESCAPE_FLAGS;

        foreach (isset($options['flags']) ? (array) $options['flags'] : [] as $flag) {
            $flags  |= $flag;
        }

        $enable_html_escape_flag    = false;

        foreach (static::HTML_ESCAPE_FLAGS as $html_flag) {
            if ($enable_html_escape_flag = (0 !== $flags & $html_flag)) {
                break;
            }
        }

        if (!$enable_html_escape_flag) {
            $flags  |= \ENT_HTML5;
        }

        return \htmlspecialchars($value, $flags, $encoding);
    }

    /**
     * JavaScript文字列のエスケープを行います。
     *
     * @param  string $value   エスケープするJavaScript文字列
     * @param  array  $options オプション
     * @return string エスケープされたJavaScript文字列
     * @see https://blog.ohgaki.net/javascript-string-escape
     */
    public static function jsEscape(string $value, array $options = []): string
    {
        if (!\mb_check_encoding($value, self::JAVASCRIPT_ENCODING)) {
            throw new \InvalidArgumentException(\sprintf('不正なエンコーディングが検出されました。JavaScriptエスケープ対象の文字列はUTF-8である必要があります。value_encoding:%s', Convert::toDebugString(\mb_detect_encoding($value, static::DETECT_ENCODING_ORDER, true))));
        }

        $map = [
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 0, 0, // 49
            0, 0, 0, 0, 0, 0, 0, 0, 1, 1,
            1, 1, 1, 1, 1, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 1, 1, 1, 1, 1, 1, 0, 0, 0, // 99
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1, // 149
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1, // 199
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
            1, 1, 1, 1, 1, 1, 1, 1, 1, 1, // 249
            1, 1, 1, 1, 1, 1, 1, // 255
        ];

        // 文字エンコーディングはUTF-8
        $mblen   = \mb_strlen($value, self::JAVASCRIPT_ENCODING);
        $utf32   = \mb_convert_encoding($value, 'UTF-32', self::JAVASCRIPT_ENCODING);
        $convmap = [0x0, 0xFFFFFF, 0, 0xFFFFFF];

        for ($i = 0, $encoded = ''; $i < $mblen; ++$i) {
            // Unicodeの仕様上、最初のバイトは無視してもOK
            $c =  (\ord($utf32[$i * 4 + 1]) << 16) + (\ord($utf32[$i * 4 + 2]) << 8) + \ord($utf32[$i * 4 + 3]);

            if ($c < 256 && $map[$c]) {
                if ($c < 0x10) {
                    $encoded .= '\\x0' . \base_convert((string) $c, 10, 16);
                } else {
                    $encoded .= '\\x' . \base_convert((string) $c, 10, 16);
                }
            } elseif ($c === 0x2028) {
                $encoded .= '\\u2028';
            } elseif ($c === 0x2029) {
                $encoded .= '\\u2029';
            } else {
                $encoded .= \mb_decode_numericentity('&#' . $c . ';', $convmap, self::JAVASCRIPT_ENCODING);
            }
        }

        return $encoded;
    }

    /**
     * CSS文字列のエスケープを行います。
     *
     * @param  string $value   エスケープするCSS文字列
     * @param  array  $options オプション
     * @return string エスケープされたCSS文字列
     * @see https://blog.ohgaki.net/css%E3%81%AE%E3%82%A8%E3%82%B9%E3%82%B1%E3%83%BC%E3%83%97%E6%96%B9%E6%B3%95
     */
    public static function cssEscape(string $value, array $options = []): string
    {
        if (\is_numeric($value)) {
            return $value;
        }

        return \preg_replace_callback('/[^0-9a-z]/iSu', [static::class, 'cssEscapeConverter'], $value);
    }

    // ----------------------------------------------
    // json
    // ----------------------------------------------
    /**
     * HTML上のJavaScriptとして評価される中で安全なJSON文字列を返します。
     *
     * @param  mixed  $value JSON化する値
     * @param  int    $depth 最大の深さを設定します。正の数でなければいけません。
     * @return string JSON化された値
     */
    public static function toJson($value, int $depth = 512): string
    {
        return \json_encode($value, \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT, $depth);
    }

    // ----------------------------------------------
    // shell
    // ----------------------------------------------
    /**
     * シェル引数のエスケープを行います。
     *
     * @param  string      $value    エスケープするシェル引数
     * @param  array       $options  オプション
     * @param  null|string $encoding エンコーディング
     * @return string      エスケープされたHTML文字列
     */
    public static function shellEscape(string $value, array $options = [], ?string $encoding = null): string
    {
        $encoding   = $encoding ?? \mb_internal_encoding();

        // PHP8.1での誤った修正により`SJIS-win`は削除されました。
        // 過去実装でも極力そのまま動作させるために、内部的にはCP932を設定したものとみなし、処理を続行させます。
        if (\version_compare(\PHP_VERSION, '8.1')) {
            if ($encoding === 'SJIS-win') {
                $encoding = 'CP932';
            }
        }

        if (!\mb_check_encoding($value, $encoding)) {
            throw new \InvalidArgumentException(\sprintf('不正なエンコーディングが検出されました。encoding:%s, value_encoding:%s', Convert::toDebugString($encoding ?? \mb_internal_encoding()), Convert::toDebugString(\mb_detect_encoding($value, static::$DETECT_ENCODING_ORDER, true))));
        }

        return \escapeshellarg($value);
    }

    // ----------------------------------------------
    // variable
    // ----------------------------------------------
    /**
     * 変数に関する情報を文字列にして返します。
     *
     * @param  mixed           $var     変数に関する情報を文字列にしたい変数
     * @param  int             $depth   変数に関する情報を文字列にする階層の深さ
     * @param  null|array|bool $options オプション
     *                                  [
     *                                  'prettify'      => bool     出力結果をprettifyするかどうか
     *                                  'indent_level'  => int      prettify時の開始インデントレベル
     *                                  'indent_width'  => int      prettify時のインデント幅
     *                                  'object_detail' => bool     オブジェクト詳細情報に対してのみの表示制御
     *                                  'loaded_object' => object   現時点までに読み込んだことがあるobject
     *                                  ]
     * @return string          変数に関する情報
     */
    public static function toDebugString($var, int $depth = 0, $options = []): string
    {
        if (\is_array($options)) {
            if (!isset($options['prettify'])) {
                $options['prettify']    = isset($options['indent_level']) || isset($options['indent_width']);
            }

            if (!isset($options['indent_level'])) {
                $options['indent_level']    = $options['prettify'] ? static::TO_DEBUG_STRING_DEFAULT_INDENT_LEVEL : null;
            }

            if (!isset($options['indent_width'])) {
                $options['indent_width']    = $options['prettify'] ? static::TO_DEBUG_STRING_DEFAULT_INDENT_WIDTH : null;
            }
        } elseif (\is_bool($options) && $options) {
            $options    = [
                'prettify'      => true,
                'indent_level'  => static::TO_DEBUG_STRING_DEFAULT_INDENT_LEVEL,
                'indent_width'  => static::TO_DEBUG_STRING_DEFAULT_INDENT_WIDTH,
            ];
        } else {
            $options    = [
                'prettify'      => false,
                'indent_level'  => null,
                'indent_width'  => null,
            ];
        }

        if (!isset($options['object_detail'])) {
            $options['object_detail']   = true;
        }

        if (!isset($options['loaded_object'])) {
            $options['loaded_object']   = (object) ['loaded' => []];
        }

        switch (\gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'integer':
                return (string) $var;
            case 'double':
                if (false === \mb_strpos((string) $var, '.')) {
                    return \sprintf('%s.0', $var);
                }

                return (string) $var;
            case 'string':
                return \sprintf('\'%s\'', $var);
            case 'array':
                if ($depth < 1) {
                    return 'Array';
                }
                --$depth;

                if ($options['prettify']) {
                    $next_options   = $options;

                    $tabular        = Tabular::disposableFactory($next_options['indent_width'])->trimEolSpace(true);

                    $indent  = \str_repeat(' ', $next_options['indent_width'] * $next_options['indent_level']);

                    ++$next_options['indent_level'];

                    foreach ($var as $key => $value) {
                        $tabular->addRow([
                            $indent,
                            static::toDebugString($key),
                            \sprintf('=> %s,', static::toDebugString($value, $depth, $next_options)),
                        ]);
                    }

                    return \sprintf('[%s%s%s%s]', "\n", \implode("\n", $tabular->build()), "\n", $indent);
                }
                $ret = [];

                foreach ($var as $key => $value) {
                    $ret[] = \sprintf('%s => %s', static::toDebugString($key), static::toDebugString($value, $depth, $options));
                }

                return \sprintf('[%s]', \implode(', ', $ret));

            case 'object':
                $object_status = \sprintf('object(%s)#%d', $var::class, \spl_object_id($var));

                if ($depth < 1 || !$options['object_detail']) {
                    return $object_status;
                }

                if (isset($options['loaded_object']->loaded[$object_status])) {
                    return \sprintf('%s [displayed]', $object_status);
                }
                $options['loaded_object']->loaded[$object_status]   = $object_status;

                --$depth;

                $ro = new \ReflectionObject($var);

                $tmp_properties = [];

                foreach ($ro->getProperties() as $property) {
                    $state                               = $property->isStatic() ? 'static' : 'dynamic';
                    $modifier                            = $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : ($property->isPrivate() ? 'private' : 'unknown modifier'));
                    $tmp_properties[$state][$modifier][] = $property;
                }

                if ($options['prettify']) {
                    $next_options   = $options;

                    $staticTabular  = Tabular::disposableFactory($next_options['indent_width'])->trimEolSpace(true);
                    $dynamicTabular = Tabular::disposableFactory($next_options['indent_width'])->trimEolSpace(true);

                    $indent  = \str_repeat(' ', $next_options['indent_width'] * $next_options['indent_level']);

                    ++$next_options['indent_level'];

                    foreach (['static', 'dynamic'] as $state) {
                        $is_static  = $state === 'static';

                        foreach (['public', 'protected', 'private', 'unknown modifier'] as $modifier) {
                            foreach ($tmp_properties[$state][$modifier] ?? [] as $property) {
                                $property->setAccessible(true);

                                if ($is_static) {
                                    $staticTabular->addRow([
                                        $indent,
                                        'static',
                                        $modifier,
                                        \sprintf('$%s', $property->getName()),
                                        \sprintf('= %s,', static::toDebugString($property->getValue($var), $depth, $next_options)),
                                    ]);
                                } else {
                                    $dynamicTabular->addRow([
                                        $indent,
                                        $modifier,
                                        \sprintf('$%s', $property->getName()),
                                        \sprintf('= %s,', static::toDebugString($property->getValue($var), $depth, $next_options)),
                                    ]);
                                }
                            }
                        }
                    }

                    $rows   = [];

                    foreach ($staticTabular->build() as $tab_row) {
                        $rows[] = $tab_row;
                    }

                    foreach ($dynamicTabular->build() as $tab_row) {
                        $rows[] = $tab_row;
                    }

                    return \sprintf('%s {%s%s%s%s}', $object_status, "\n", \implode("\n", $rows), "\n", $indent);
                }
                $properties = [];

                foreach (['static', 'dynamic'] as $state) {
                    $state_text = $state === 'static' ? ' static' : '';

                    foreach (['public', 'protected', 'private', 'unknown modifier'] as $modifier) {
                        foreach ($tmp_properties[$state][$modifier] ?? [] as $property) {
                            $property->setAccessible(true);
                            $properties[] = \sprintf('%s%s %s = %s', $modifier, $state_text, \sprintf('$%s', $property->getName()), static::toDebugString($property->getValue($var), $depth, $options));
                        }
                    }
                }

                return \sprintf('%s {%s}', $object_status, \implode(', ', $properties));

            case 'resource':
                return \sprintf('%s %s', \get_resource_type($var), $var);
            case 'resource (closed)':
                return \sprintf('resource (closed) %s', $var);
            case 'NULL':
                return 'NULL';
            case 'unknown type':
            default:
                return 'unknown type';
        }
    }

    /**
     * 変数に関する情報をHTML出力用のビルダーとして返します。
     *
     * @param  mixed            $var 変数に関する情報を文字列にしたい変数
     * @return DebugHtmlBuilder HTML出力用のビルダー
     */
    public static function toDebugHtml($var): DebugHtmlBuilder
    {
        if (\func_num_args() === 1) {
            $instance   = DebugHtmlBuilder::factory($var)->setStartBacktraceDepth(2);
        } else {
            $instance   = \call_user_func_array([DebugHtmlBuilder::class, 'factory'], \func_get_args())->setStartBacktraceDepth(3);
        }

        return $instance;
    }

    /**
     * バイトサイズを単位付きのバイトサイズに変換します。
     *
     * @param  string|int $size      バイトサイズ
     * @param  int        $precision 小数点以下の桁数
     * @param  array      $suffixes  サフィックスマップ
     * @return string     単位付きのバイトサイズ
     */
    public static function toUnitByteSize($size, int $precision = 2, array $suffixes = []): string
    {
        $base           = \log($size) / \log(1024);
        $suffixes       = \array_merge(['B', 'KB', 'MB', 'GB', 'TB'], $suffixes);
        $floored_base   = \floor($base);

        return isset($suffixes[$floored_base])
         ? \sprintf('%s%s', \round(\pow(1024, $base - \floor($base)), $precision), $suffixes[$floored_base])
         : \sprintf('%sB', \number_format($size));
    }

    /**
     * 変数をJavaScript表現として返します。
     *
     * @param  mixed  $var JavaScript表現にしたい変数
     * @return string JavaScript表現にした変数
     */
    public static function toJsExpression($var): string
    {
        $type = \gettype($var);

        switch ($type) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'integer':
                return (string) $var;
            case 'double':
                if (false === \mb_strpos((string) $var, '.')) {
                    return \sprintf('%s.0', $var);
                }

                return (string) $var;
            case 'string':
                return \sprintf('\'%s\'', static::jsEscape($var));
            case 'array':
            case 'object':
                return static::toJson($var);
            case 'resource':
                return \sprintf('\'%s\'', static::jsEscape(\sprintf('%s %s', \get_resource_type($var), $var)));
            case 'resource (closed)':
                return \sprintf('\'%s\'', static::jsEscape(\sprintf('resource (closed) %s', $var)));
            case 'NULL':
                return 'null';
            case 'unknown type':
            default:
                return \sprintf('\'%s\'', static::jsEscape('unknown type'));
        }
    }

    /**
     * CSSエスケープ用正規表現処理結果コールバック処理
     *
     * @param  array  $matchers マッチ済みの値
     * @return string 変換後の文字
     */
    protected static function cssEscapeConverter(array $matchers): string
    {
        if ($matchers[0] === "\0") {
            return '\\00FFFD';
        }

        $unpacked_char  = \unpack('Nc', \mb_convert_encoding($matchers[0], 'UTF-32BE'));

        return \sprintf(
            '\\%06X',
            $unpacked_char['c'],
        );
    }
}
