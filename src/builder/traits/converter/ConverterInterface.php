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

namespace fw3\strings\builder\traits\converter;

/**
 * コンバータインターフェース
 */
interface ConverterInterface
{
    /**
     * 現在の変数名を元に値を返します。
     *
     * @param   string      $name   現在の変数名
     * @param   string      $search 変数名の元の文字列
     * @param   array       $values 変数
     * @return  string|null 値
     */
    public static function convert(string $name, string $search, array $values): ?string;

    /**
     * 現在の変数名を元に値を返します。
     *
     * @param   string      $name   現在の変数名
     * @param   string      $search 変数名の元の文字列
     * @param   array       $values 変数
     * @return  string|null 値
     */
    public function __invoke(string $name, string $search, array $values): ?string;
}
