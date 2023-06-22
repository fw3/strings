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
use fw3\strings\html\elements\traits\HtmlElementInterface;
use fw3\strings\html\elements\traits\HtmlElementTrait;
use fw3\strings\html\traits\Htmlable;

/**
 * 簡易的なHTML要素構築ビルダです。
 */
class HtmlElement implements HtmlElementInterface
{
    use HtmlElementTrait;

    /**
     * factory
     *
     * @param  string      $element_name 要素名
     * @param  array       $attributes   属性
     * @param  array       $children     子要素
     * @return self|static このインスタンス
     */
    public static function factory(string $element_name, array $children = [], array $attributes = [], $htmlConfig = null)
    {
        return new static($element_name, $children, $attributes, $htmlConfig);
    }

    /**
     * constructor
     *
     * @param  string      $element_name 要素名
     * @param  array       $attributes   属性
     * @param  array       $children     子要素
     * @return self|static このインスタンス
     */
    public function __construct(string $element_name, array $children = [], array $attributes = [], $htmlConfig = null)
    {
        $this->elementName  = $element_name;
        $this->children     = \is_array($children) ? $children : [$children];
        $this->attributes   = $attributes;
        $this->htmlConfig   = $htmlConfig ?? Html::htmlConfig();
    }

    /**
     * 現在の状態を元にHTML文字列を構築し返します。
     *
     * @param  int    $indent_lv インデントレベル
     * @return string 構築したHTML文字列
     */
    public function toHtml(int $indent_lv = 0): string
    {
        $indent = \str_repeat(' ', $indent_lv * 4);

        $element_name   = Html::escape($this->elementName, $this->htmlConfig);

        $attributes = [
            '',
        ];

        foreach ($this->attributes as $attribute_name => $value) {
            if (!($value instanceof HtmlAttribute)) {
                if ($value === null) {
                    $value  = Html::attribute($attribute_name);
                } else {
                    $value  = Html::attribute($attribute_name, $value);
                }
            }

            $attributes[]   = $value->toHtml();
        }
        $attribute  = \implode(' ', $attributes);

        if (!empty($this->children)) {
            $children   = [];

            if ($this->elementName === 'script') {
                return \sprintf(
                    '%s<script%s>%s%s%s</%script>',
                    $indent,
                    $attribute,
                    "\n",
                    Html::escape((string) $this->children, HtmlConfigInterface::ESCAPE_TYPE_JS, HtmlConfigInterface::ENCODING_FOR_JS),
                    "\n",
                );
            }

            if (\count($this->children) === 1) {
                $use_lf = false;

                foreach ($this->children as $child) {
                    if (!($child instanceof Htmlable)) {
                        $child  = Html::textNode($child);
                    }

                    $use_lf = !($child instanceof HtmlTextNode);
                }

                return \sprintf(
                    '%s<%s%s>%s%s%s%s</%s>',
                    $indent,
                    $element_name,
                    $attribute,
                    $use_lf ? "\n" : '',
                    $child->toHtml($indent_lv + 1),
                    $use_lf ? "\n" : '',
                    $use_lf ? $indent : '',
                    $element_name,
                );
            }

            $before_element_html    = null;

            foreach ($this->children as $child) {
                if (!($child instanceof Htmlable)) {
                    $child  = Html::textNode($child);
                }

                if ($child instanceof HtmlTextNode) {
                    $children[]             = Html::textNode($child)->toHtml($indent_lv + 1);
                    $before_element_html    = false;
                } else {
                    if ($before_element_html !== false) {
                        $children[] = "\n";
                    }
                    $children[]             = $child->toHtml($indent_lv + 1);
                    $before_element_html    = true;
                }
            }

            $children[] = "\n";

            return \sprintf(
                '%s<%s%s>%s%s</%s>',
                $indent,
                $element_name,
                $attribute,
                \implode('', $children),
                $indent,
                $element_name,
            );
        }

        return \sprintf('%s<%s%s>', $indent, $element_name, $attribute);
    }

    /**
     * sugar factory
     *
     * @param  string      $element_name 要素名
     * @param  array       $args         引数
     * @return self|static このインスタンス
     */
    public static function __callStatic(string $element_name, array $args)
    {
        $children   = $args[0] ?? [];
        $attributes = $args[1] ?? [];
        $htmlConfig = $args[2] ?? null;

        return new static($element_name, $children, $attributes, $htmlConfig);
    }
}
