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
 * 簡易的なHTML構築ビルダ設定です。
 */
class HtmlConfig implements HtmlConfigInterface
{
    /**
     * @var array エスケープタイプリスト
     */
    public const ESCAPE_TYPE_LIST   = [
        self::ESCAPE_TYPE_HTML          => self::ESCAPE_TYPE_HTML,
        self::ESCAPE_TYPE_JAVASCRIPT    => self::ESCAPE_TYPE_JAVASCRIPT,
        self::ESCAPE_TYPE_JS            => self::ESCAPE_TYPE_JS,
        self::ESCAPE_TYPE_CSS           => self::ESCAPE_TYPE_CSS,
    ];

    /**
     * @var string エスケープタイプ
     */
    protected string $escapeType   = self::DEFAULT_ESCAPE_TYPE;

    /**
     * @var string エンコーディング
     */
    protected string $encoding = self::DEFAULT_ENCODING;

    /**
     * ファクトリ
     *
     * @param  array       $options オプション
     * @return self|static このインスタンス
     */
    public static function factory(array $options = [])
    {
        return new static($options);
    }

    /**
     * ファクトリ
     *
     * @param  array       $options オプション
     * @return self|static このインスタンス
     */
    public function __construct(array $options = [])
    {
        if (isset($options['escape_type'])) {
            $this->escapeType   = $options['escape_type'];
        }

        if (isset($options['encoding'])) {
            $this->encoding = $options['encoding'];
        }
    }

    /**
     * エスケープタイプを取得・設定します。
     *
     * @param  null|string   $escape_type エスケープタイプ
     * @return string|static エンコーディングまたはこのインスタンス
     */
    public function escapeType(?string $escape_type = null)
    {
        if ($escape_type === null && \func_num_args() === 0) {
            return $this->escapeType;
        }

        if (!isset(static::ESCAPE_TYPE_LIST[$escape_type])) {
            throw new \Exception(\sprintf('利用できないエスケープタイプを指定されました。escape_type:%s', Convert::toDebugString($escape_type)));
        }

        $this->escapeType   = $escape_type;

        return $this;
    }

    /**
     * エンコーディングを取得・設定します。
     *
     * @param  null|string   $encoding エンコーディング
     * @return string|static エンコーディングまたはこのインスタンス
     */
    public function encoding(?string $encoding = null)
    {
        static $mb_list_encodings;

        if (!isset($mb_list_encodings)) {
            $mb_list_encodings  = \mb_list_encodings();
        }

        if ($encoding === null && \func_num_args() === 0) {
            return $this->encoding;
        }

        if (!\in_array($encoding, $mb_list_encodings, true)) {
            throw new \Exception(\sprintf('利用できないエンコーディングを指定されました。encoding:%s', Convert::toDebugString($encoding)));
        }

        $this->encoding = $encoding;

        return $this;
    }
}
