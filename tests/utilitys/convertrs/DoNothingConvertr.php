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

namespace fw3\tests\strings\utilitys\convertrs;

use fw3\strings\builder\traits\converter\ConverterInterface;
use fw3\strings\builder\traits\converter\ConverterTrait;

class DoNothingConvertr implements ConverterInterface
{
    use ConverterTrait;

    /**
     * 現在の変数名を元に値を返します。
     *
     * @param  string      $name   現在の変数名
     * @param  string      $search 変数名の元の文字列
     * @param  array       $values 変数
     * @return null|string 値
     */
    public static function convert(string $name, string $search, array $values): ?string
    {
        return null;
    }
}
