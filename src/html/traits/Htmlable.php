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

namespace fw3\strings\html\traits;

use fw3\strings\html\config\HtmlConfigInterface;

/**
 * 簡易的なHTML構築ビルダインターフェースです。
 */
interface Htmlable
{
    /**
     * 簡易的なHTML構築ビルダ設定を取得・設定します。
     *
     * @return HtmlConfigInterface|static 簡易的なHTML構築ビルダ設定またはこのインスタンス
     */
    public function htmlConfig($htmlConfig = null);

    /**
     * 現在の状態を元にHTML文字列を構築し返します。
     *
     * @param  int    $indent_lv インデントレベル
     * @return string 構築したHTML文字列
     */
    public function toHtml(int $indent_lv = 0): string;
}
