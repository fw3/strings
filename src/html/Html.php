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

namespace fw3\strings\html;

use fw3\strings\converter\Convert;
use fw3\strings\html\config\HtmlConfig;
use fw3\strings\html\config\HtmlConfigInterface;
use fw3\strings\html\traits\Htmlable;

/**
 * 簡易的なHTML構築ビルダファクトリです。
 */
abstract class Html
{
    /**
     * @var HtmlConfigInterface 簡易的なHTML構築ビルダ設定
     */
    protected static HtmlConfigInterface $htmlConfig    = null;

    /**
     * 要素を返します。
     *
     * @param  string              $element_name 要素名
     * @param  array               $children     子要素
     * @param  array               $attributes   属性
     * @param  HtmlConfigInterface $htmlConfig   コンフィグ
     * @return HtmlElement         要素
     */
    public static function element(string $element_name, array $children = [], array $attributes = [], ?HtmlConfigInterface $htmlConfig = null): HtmlElement
    {
        return HtmlElement::factory($element_name, $children, $attributes, $htmlConfig ?? self::htmlConfig());
    }

    /**
     * 属性を返します。
     *
     * @param  string              $attribute_name 属性名
     * @param  mixed               $value          属性値
     * @param  HtmlConfigInterface $htmlConfig     コンフィグ
     * @return HtmlAttribute       属性
     */
    public static function attribute(string $attribute_name, $value = null, ?HtmlConfigInterface $htmlConfig = null): HtmlAttribute
    {
        return HtmlAttribute::factory($attribute_name, $value, $htmlConfig ?? self::htmlConfig());
    }

    /**
     * データ属性を返します。
     *
     * @param  string              $data_name  データ属性名
     * @param  mixed               $value      属性値
     * @param  HtmlConfigInterface $htmlConfig コンフィグ
     * @return HtmlAttribute       属性
     */
    public static function data(string $data_name, $value = null, ?HtmlConfigInterface $htmlConfig = null): HtmlAttribute
    {
        return HtmlAttribute::factory(\sprintf('data-%s', $data_name), $value, $htmlConfig ?? self::htmlConfig());
    }

    /**
     * テキストノードを返します。
     *
     * @param  string              $value      テキスト
     * @param  HtmlConfigInterface $htmlConfig コンフィグ
     * @return HtmlTextNode        テキストノード
     */
    public static function textNode(string $value, ?HtmlConfigInterface $htmlConfig = null): HtmlTextNode
    {
        return HtmlTextNode::factory($value, $htmlConfig ?? self::htmlConfig());
    }

    /**
     * 簡易的なHTML構築ビルダ設定を取得・設定します。
     *
     * @return HtmlConfigInterface|string 簡易的なHTML構築ビルダ設定またはこのクラスパス
     */
    public static function htmlConfig($htmlConfig = null)
    {
        if ($htmlConfig === null && \func_num_args() === 0) {
            if (self::$htmlConfig === null) {
                self::$htmlConfig   = HtmlConfig::factory();
            }

            return self::$htmlConfig;
        }

        if (!($htmlConfig instanceof HtmlConfigInterface)) {
            throw new \Exception(\sprintf('利用できない簡易的なHTML構築ビルダ設定を指定されました。escape_type:%s', Convert::toDebugString($htmlConfig)));
        }

        self::$htmlConfig   = $htmlConfig;

        return static::class;
    }

    /**
     * エスケープを実施します。
     *
     * @param  mixed                      $value       値
     * @param  string|HtmlConfigInterface $escape_type エスケープタイプ
     * @param  null|string                $encoding    エンコーディング
     * @return string                     エスケープ済みの値
     */
    public static function escape($value, $escape_type = null, ?string $encoding = null): string
    {
        if ($value instanceof Htmlable) {
            return $value->toHtml();
        }

        if ($escape_type instanceof HtmlConfigInterface) {
            $encoding       = $escape_type->encoding();
            $escape_type    = $escape_type->escapetype();
        } else {
            $encoding       = $encoding    ?? self::htmlConfig()->encoding();
            $escape_type    = $escape_type ?? self::htmlConfig()->escapetype();
        }

        return Convert::escape($value, $escape_type, [], $encoding);
    }

    /**
     * constructor
     */
    private function __construct()
    {
    }

    /**
     * 要素を返します。
     *
     * @param  string      $element_name 要素名
     * @param  array       $args         引数
     * @return HtmlElement 要素
     */
    public static function __callStatic(string $element_name, array $args): HtmlElement
    {
        $children   = $args[0] ?? [];
        $attributes = $args[1] ?? [];
        $htmlConfig = $args[2] ?? null;

        return HtmlElement::factory($element_name, $children, $attributes, $htmlConfig ?? self::htmlConfig());
    }
}
