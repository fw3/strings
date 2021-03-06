<?php
/**    _______       _______
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
 * @copyright   2020 - Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/)
 * @license     http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion     0.0.1
 */

declare(strict_types=1);

namespace fw3\strings\builder\modifiers\strings;

use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\strings\builder\modifiers\ModifierTrait;
use fw3\strings\converter\Convert;

/**
 * String Builder: to string notation Modifier
 */
class ToDebugStringModifier implements ModifierInterface
{
    use ModifierTrait;

    /**
     * 置き換え値を修飾して返します。
     *
     * @param   mixed   $replace    置き換え値
     * @param   array   $parameters パラメータ
     * @param   array   $context    コンテキスト
     * @return  mixed   修飾した置き換え値
     */
    public static function modify($replace, array $parameters = [], array $context = [])
    {
        $depth  = $parameters['depth'] ?? $parameters[0] ?? 0;

        return Convert::toDebugString((string) $replace, $depth);
    }
}
