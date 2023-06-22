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

namespace fw3\strings\builder\modifiers;

/**
 * 修飾子インターフェース特性
 */
trait ModifierTrait
{
    /**
     * 置き換え値を修飾して返します。
     *
     * @param  mixed                       $replace    置き換え値
     * @param  array                       $parameters パラメータ
     * @param  array                       $context    コンテキスト
     * @return 修飾した置き換え値
     */
    public function __invoke($replace, array $parameters = [], array $context = [])
    {
        return static::modify($replace, $parameters, $context);
    }
}
