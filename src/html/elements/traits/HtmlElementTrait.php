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

namespace fw3\strings\html\elements\traits;

use fw3\strings\html\Html;
use fw3\strings\html\HtmlAttribute;
use fw3\strings\html\HtmlTextNode;
use fw3\strings\html\traits\Htmlable;
use fw3\strings\html\traits\HtmlableTrait;

/**
 * 簡易的なHTML要素構築ビルダ特性です。
 */
trait HtmlElementTrait
{
    use HtmlableTrait;

    /**
     * @var string 要素名
     */
    protected string $elementName;

    /**
     * @var array 属性
     */
    protected array $attributes   = [];

    /**
     * @var array 子要素
     */
    protected array $children     = [];

    /**
     * 属性が存在するかどうかを返します。
     *
     * @param  string $attribute_name 属性名
     * @return bool   属性が存在するかどうか
     */
    public function hasAttr(string $attribute_name): bool
    {
        return \array_key_exists($attribute_name, $this->attributes);
    }

    /**
     * 属性を設定・取得します。
     *
     * @param  string                   $attribute_name 属性名
     * @param  null|string|array        $value          属性値
     * @return null|string|array|static 属性値またはこのインスタンス
     */
    public function attr(string $attribute_name, $value = null)
    {
        if ($value === null && !\is_array($attribute_name) && \func_num_args() === 1) {
            if ($attribute_name instanceof HtmlAttribute) {
                $this->attributes[$attribute_name->getName()]   = $attribute_name;

                return $this;
            }

            return $this->hasAttr($attribute_name) ? $this->attributes[$attribute_name] : null;
        }

        if (\is_array($attribute_name)) {
            foreach ($attribute_name as $name => $value) {
                $this->attributes[$name]    = Html::attribute($name, $value);
            }

            return $this;
        }

        $this->attributes[$attribute_name]  = Html::attribute($attribute_name, $value);

        return $this;
    }

    /**
     * 属性を設定・取得します。
     *
     * @param  string                   $attribute_name 属性名
     * @param  null|string|array        $value          属性値
     * @return null|string|array|static 属性値またはこのインスタンス
     */
    public function attribute(string $attribute_name, $value = null)
    {
        if ($value === null && \func_num_args() === 1) {
            return $this->attr($attribute_name);
        }

        $this->attr($attribute_name, $value);

        return $this;
    }

    /**
     * CSS Classを設定・取得します。
     *
     * @param  string|array             $class CSS Class
     * @return null|string|array|static 属性値またはこのインスタンス
     */
    public function cssClass($class = [])
    {
        if ($class === [] && \func_num_args() === 1) {
            return $this->attr('class');
        }

        if ($class instanceof HtmlAttribute) {
            $class = $class->value();
        }

        $this->attr('class', $class);

        return $this;
    }

    /**
     * CSSクラスを追加します。
     *
     * @param  string|array $class 追加するCSSクラス
     * @return self|static  このインスタンス
     */
    public function appendClass($class)
    {
        if ($this->hasAttr('class')) {
            $beforeClass    = $this->attr('class');
            $before_class   = $beforeClass->value();
            $before_class   = \is_array($before_class) ? $before_class : [$before_class];

            $classes    = [];

            foreach (\is_array($class) ? $class : [$class] as $tmp_class) {
                if ($tmp_class instanceof HtmlAttribute) {
                    $tmp_class  = $tmp_class->value();
                }

                $classes[]  = $tmp_class;
            }

            $this->cssClass(\array_merge($before_class, $classes));

            return $this;
        }

        if ($class instanceof HtmlAttribute) {
            $class = $class->value();
        }

        $this->cssClass($class);

        return $this;
    }

    /**
     * CSS Styleを設定・取得します。
     *
     * @param  string|array             $style CSS Style
     * @return null|string|array|static 属性値またはこのインスタンス
     */
    public function style($style = [])
    {
        if ($style === [] && \func_num_args() === 1) {
            return $this->attr('style');
        }

        if ($style instanceof HtmlAttribute) {
            $style  = $style->value();
        }

        $this->attr('style', $style);

        return $this;
    }

    /**
     * CSSクラスを追加します。
     *
     * @param  string|array $style 追加するCSS Style
     * @return self|static  このインスタンス
     */
    public function appendStyle($style)
    {
        if ($this->hasAttr('style')) {
            $beforeStyle    = $this->attr('style');
            $before_style   = $beforeStyle->value();
            $before_style   = \is_array($before_style) ? $before_style : [$before_style];

            $styles = [];

            foreach (\is_array($style) ? $style : [$style] as $tmp_style) {
                if ($tmp_style instanceof HtmlAttribute) {
                    $tmp_style  = $tmp_style->value();
                }

                $styles[]  = $tmp_style;
            }

            $this->style(\array_merge($before_style, $styles));

            return $this;
        }

        if ($style instanceof HtmlAttribute) {
            $style  = $style->value();
        }

        $this->style($style);

        return $this;
    }

    /**
     * データ属性が存在するかどうかを返します。
     *
     * @param  string $data_name データ属性名
     * @return bool   データ属性が存在するかどうか
     */
    public function hasData(string $data_name): bool
    {
        $data_name  = \sprintf('data-%s', $data_name);

        return \array_key_exists($data_name, $this->attributes);
    }

    /**
     * データ属性を設定・取得します。
     *
     * @param  string                   $data_name データ属性名
     * @param  null|string|array        $value     属性値
     * @return null|string|array|static 属性値またはこのインスタンス
     */
    public function data(string $data_name, $value = null)
    {
        $data_name  = \sprintf('data-%s', $data_name);

        if ($value === null && \func_num_args() === 1) {
            return $this->attr($data_name);
        }

        $this->attr($data_name, $value);

        return $this;
    }

    /**
     * コンテキストを設定・取得します。
     *
     * 設定済みの子要素が存在する場合、全て削除の上コンテキストに置き換えられます。
     *
     * @param  string       $context コンテキスト
     * @return array|static コンテキストまたはこのインスタンス
     */
    public function context(?string $context = null)
    {
        if ($context === null && \func_num_args() === 0) {
            return $this->children;
        }

        if (\is_string($context)) {
            $context    = Html::textNode($context, $this->htmlConfig());
        }

        $this->children = [$context];

        return $this;
    }

    /**
     * 子要素を設定・取得します。
     *
     * @param  array       $children 子要素
     * @return self|static このインスタンス
     */
    public function children(?array $children = null)
    {
        $func_num_args  = \func_num_args();

        if ($children === null && $func_num_args === 0) {
            return $this->children;
        }

        if ($func_num_args === 1) {
            $func_args  = \is_array($children) ? $children : [$children];
        } else {
            $func_args  = \func_get_args();
        }

        $this->children = [];
        $this->appendChildNode($func_args);

        return $this;
    }

    /**
     * 子要素を追加します。
     *
     * @param  Htmlable    $child_node 子要素
     * @return self|static このインスタンス
     */
    public function appendChildNode(Htmlable $child_node)
    {
        $child_node = \func_num_args() === 1 ? $child_node : \func_get_args();

        foreach (\is_array($child_node) ? $child_node : [$child_node] as $child_node) {
            if (!($child_node instanceof Htmlable)) {
                $child_node = Html::textNode($child_node, $this->htmlConfig());
            }

            $this->children[]   = $child_node;
        }

        return $this;
    }

    /**
     * 子要素を追加します。
     *
     * @param  Htmlable    $child_node 子要素
     * @return self|static このインスタンス
     */
    public function appendNode(Htmlable $child_node)
    {
        return $this->appendChildNode(\func_num_args() === 1 ? $child_node : \func_get_args());
    }

    /**
     * テキストノードを追加します。
     *
     * @param  HtmlTextNode|string $context 子要素
     * @return self|static         このインスタンス
     */
    public function appendTextNode($context)
    {
        return $this->appendChildNode(\func_num_args() === 1 ? $context : \func_get_args());
    }

    /**
     * コンテキストを追加します。
     *
     * @param  HtmlTextNode|string $context 子要素
     * @return self|static         このインスタンス
     */
    public function appendContext($context)
    {
        return $this->appendChildNode(\func_num_args() === 1 ? $context : \func_get_args());
    }

    /**
     * 属性を設定します。
     *
     * @param  string      $attribute_name 属性名
     * @param  mixed       $value          属性値
     * @return self|static このインスタンス
     */
    public function __call(string $attribute_name, $value = null)
    {
        if ($value === null && \func_num_args() === 1) {
            $this->attr($attribute_name);
        }

        $this->attr($attribute_name, $value);

        return $this;
    }
}
