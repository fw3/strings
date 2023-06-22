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

namespace fw3\strings\builder\modifiers\datetime;

use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\strings\builder\modifiers\ModifierTrait;

/**
 * String Builder: strtotime Modifier
 */
class StrtotimeModifier implements ModifierInterface
{
    use ModifierTrait;

    /**
     * 置き換え値を修飾して返します。
     *
     * @param  mixed                       $replace    置き換え値
     * @param  array                       $parameters パラメータ
     * @param  array                       $context    コンテキスト
     * @return 修飾した置き換え値
     */
    public static function modify($replace, array $parameters = [], array $context = [])
    {
        if (!empty($parameters)) {
            $baseTimestamp  = $parameters['baseTimestamp'] ?? $parameters['0'] ?? null;

            return \strtotime($replace, $baseTimestamp);
        }

        return \strtotime($replace);
    }
}
