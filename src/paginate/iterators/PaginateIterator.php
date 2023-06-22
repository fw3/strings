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

namespace fw3\strings\paginate\iterators;

use fw3\strings\paginate\Paginater;

/**
 * ページネイトイテレータ
 */
class PaginateIterator extends \ArrayIterator
{
    /**
     * @var Paginater ページネータ
     */
    protected Paginater $paginater;

    /**
     * @var bool ページネータのURLビルダが有効かどうか
     */
    protected bool $enabledUrlBuilder    = false;

    /**
     * factory
     *
     * @param  Paginater    $paginater ページネータ
     * @param  array|object $array     反復処理をする配列あるいはオブジェクト
     * @param  int          $flags     \ArrayIterator オブジェクトの振る舞いを制御するフラグ
     * @return self|static  このインスタンス
     */
    public static function of(Paginater $paginater, $array = [], int $flags = 0)
    {
        return (new static($array, $flags))->setPaginater($paginater);
    }

    /**
     * ページネータを設定します。
     *
     * @param  Paginater   $paginater ページネータ
     * @return static|self このインスタンス
     */
    public function setPaginater(Paginater $paginater)
    {
        $this->paginater            = $paginater;
        $this->enabledUrlBuilder    = $paginater->enabledUrlBuilder();

        return $this;
    }

    /**
     * foreach時に呼び出されます。
     *
     * {@inheritDoc}
     * @see \ArrayIterator::current()
     */
    public function current()
    {
        return $this->enabledUrlBuilder ? $this->paginater->buildUrl(parent::current()) : parent::current();
    }
}
