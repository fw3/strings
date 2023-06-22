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

use fw3\strings\converter\Convert;
use fw3\strings\html\config\HtmlConfigInterface;

/**
 * 簡易的なHTML構築ビルダ特性です。
 */
trait HtmlableTrait
{
    /**
     * @var null|HtmlConfigInterface 簡易的なHTML構築ビルダ設定
     */
    protected ?HtmlConfigInterface $htmlConfig   = null;

    /**
     * 簡易的なHTML構築ビルダ設定を取得・設定します。
     *
     * @return HtmlConfigInterface|static 簡易的なHTML構築ビルダ設定またはこのインスタンス
     */
    public function htmlConfig($htmlConfig = null)
    {
        if ($htmlConfig === null && \func_num_args() === 0) {
            return $this->htmlConfig;
        }

        if (!($htmlConfig instanceof HtmlConfigInterface)) {
            throw new \Exception(\sprintf('利用できない簡易的なHTML構築ビルダ設定を指定されました。escape_format:%s', Convert::toDebugString($htmlConfig)));
        }

        $this->htmlConfig   = $htmlConfig;

        return $this;
    }
}
