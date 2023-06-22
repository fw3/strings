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

namespace fw3\strings\html\config;

use fw3\strings\converter\Convert;

/**
 * 簡易的なHTML構築ビルダ設定インターフェースです。
 */
interface HtmlConfigInterface
{
    /**
     * @var string エスケープタイプ：HTML
     */
    public const ESCAPE_TYPE_HTML          = Convert::ESCAPE_TYPE_HTML;

    /**
     * @var string エスケープタイプ：JavaScript
     */
    public const ESCAPE_TYPE_JAVASCRIPT    = Convert::ESCAPE_TYPE_JAVASCRIPT;

    /**
     * @var string エスケープタイプ：JavaScript (省略形)
     */
    public const ESCAPE_TYPE_JS            = Convert::ESCAPE_TYPE_JS;

    /**
     * @var string エスケープタイプ：CSS
     */
    public const ESCAPE_TYPE_CSS           = Convert::ESCAPE_TYPE_CSS;

    /**
     * @var string エスケープタイプ
     */
    public const DEFAULT_ESCAPE_TYPE   = self::ESCAPE_TYPE_HTML;

    /**
     * @var string JS向けエンコーディング
     */
    public const ENCODING_FOR_JS   = 'UTF-8';

    /**
     * @var string エンコーディング
     */
    public const DEFAULT_ENCODING  = 'UTF-8';

    /**
     * ファクトリ
     *
     * @param  array       $options オプション
     * @return self|static このインスタンス
     */
    public static function factory(array $options = []);

    /**
     * エスケープタイプを取得・設定します。
     *
     * @return string エスケープタイプまたはこのクラスパス
     */
    public function escapeType($escape_type = null): string;

    /**
     * エンコーディングを取得・設定します。
     *
     * @param  null|string $encoding エンコーディング
     * @return string      エンコーディングまたはこのクラスパス
     */
    public function encoding(?string $encoding = null): string;
}
