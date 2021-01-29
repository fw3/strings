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

namespace fw3\strings\builder\modifiers\datetime;

use fw3\strings\builder\modifiers\ModifierInterface;
use fw3\strings\builder\modifiers\ModifierTrait;
use InvalidArgumentException;

/**
 * String Builder: Date Modifier
 */
class DateModifier implements ModifierInterface
{
    use ModifierTrait;

    /**
     * @const   string  デフォルトフォーマット
     */
    public  const DEFAULT_FORMAT    = 'Y/m/d H:i:s';

    /**
     * 置き換え値を修飾して返します。
     *
     * @param   mixed   $replace    置き換え値
     * @param   array   $parameters パラメータ
     * @param   array   $context
     * @return  mixed   修飾した置き換え値
     */
    public static function modify($replace, array $parameters = [], array $context = [])
    {
        $format = $parameters['format'] ?? $parameters[0] ?? static::DEFAULT_FORMAT;
        if (!is_string($format)) {
            throw new InvalidArgumentException('date関数の第一引数 format: が文字列ではありません。');
        }

        return date($format, (int) $replace);
    }
}
