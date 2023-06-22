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
 * 簡易的なHTMLテキストノード構築ビルダです。
 */
class HtmlTextNode implements Htmlable
{
    use HtmlableTrait;

    /**
     * @var string 値
     */
    protected string $value;

    /**
     * constructor
     *
     * @param  string              $value      テキスト
     * @param  HtmlConfigInterface $htmlConfig コンフィグ
     * @return self|static         このインスタンス
     */
    public static function factory(string $value, ?HtmlConfigInterface $htmlConfig = null)
    {
        return new static($value, $htmlConfig);
    }

    /**
     * constructor
     *
     * @param string              $value      テキスト
     * @param HtmlConfigInterface $htmlConfig コンフィグ
     */
    public function __construct(string $value, ?HtmlConfigInterface $htmlConfig = null)
    {
        $this->value        = $value;
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
        return Html::escape($this->value, $this->htmlConfig);
    }
}
