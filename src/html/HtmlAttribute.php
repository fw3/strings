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

use fw3\strings\html\config\HtmlConfigInterface;
use fw3\strings\html\traits\Htmlable;
use fw3\strings\html\traits\HtmlableTrait;

/**
 * 簡易的なHTML構築ビルダです。
 */
class HtmlAttribute implements Htmlable
{
    use HtmlableTrait;

    /**
     * @var string 属性名
     */
    protected string $attributeName;

    /**
     * @var null|string 属性値
     */
    protected ?string $value    = null;

    /**
     * constructor
     *
     * @param  string              $attribute_name 属性名
     * @param  null|string         $value          属税値
     * @param  HtmlConfigInterface $htmlConfig     コンフィグ
     * @return HtmlAttribute       このインスタンス
     */
    public static function factory(string $attribute_name, ?string $value = null, ?HtmlConfigInterface $htmlConfig = null): HtmlAttribute
    {
        return new static($attribute_name, $value, $htmlConfig);
    }

    /**
     * constructor
     *
     * @param string              $attribute_name 属性名
     * @param null|string         $value          属税値
     * @param HtmlConfigInterface $htmlConfig     コンフィグ
     */
    public function __construct(string $attribute_name, ?string $value = null, ?HtmlConfigInterface $htmlConfig = null)
    {
        if ($value instanceof HtmlAttribute) {
            $value  = $value->value();
        }

        $this->attributeName    = $attribute_name;
        $this->value            = $value;
        $this->htmlConfig       = $htmlConfig === null ? Html::htmlConfig() : $htmlConfig;
    }

    /**
     * 属性名を返します。
     *
     * @return string 属性名
     */
    public function getName(): string
    {
        return $this->attributeName;
    }

    /**
     * 属性値を設定・取得します。
     *
     * @param  null|mixed        $value 属性値
     * @return null|mixed|static 属性値またはこのインスタンス
     */
    public function value($value = null)
    {
        if ($value === null && \func_num_args() === 0) {
            return $this->value;
        }

        if ($value instanceof HtmlAttribute) {
            $value  = $value->value();
        }

        $this->value    = $value;

        return $this;
    }

    /**
     * 現在の状態を元にHTML文字列を構築し返します。
     *
     * @param  int    $indent_lv インデントレベル
     * @return string 構築したHTML文字列
     */
    public function toHtml(int $indent_lv = 0): string
    {
        $htmlConfig = $this->htmlConfig();

        $attribute_name = $this->attributeName;
        $attribute_name = Html::escape($attribute_name, HtmlConfigInterface::ESCAPE_TYPE_HTML, $htmlConfig->encoding());

        $value  = $this->value;

        if ($value === null) {
            return Html::escape($attribute_name, HtmlConfigInterface::ESCAPE_TYPE_HTML, $htmlConfig->encoding());
        }

        if (\is_array($value)) {
            if ($this->attributeName === 'style') {
                foreach ($value as $idx => $style) {
                    $value[$idx]    = \trim($style, ' ;');
                }
                $value  = \sprintf('%s;', \implode('; ', $value));
            } else {
                $value  = \implode(' ', $value);
            }
        }

        return \sprintf(
            '%s="%s"',
            $attribute_name,
            Html::escape($value, HtmlConfigInterface::ESCAPE_TYPE_HTML, $htmlConfig->encoding()),
        );
    }

    /**
     * 属性を返します。
     *
     * @param  string        $attribute_name 属性名
     * @param  array         $args           引数
     * @return HtmlAttribute 属性
     */
    public static function __callStatic(string $attribute_name, array $args): HtmlAttribute
    {
        $value      = $args[0] ?? null;
        $htmlConfig = $args[1] ?? null;

        return new static($attribute_name, $value, $htmlConfig);
    }
}
