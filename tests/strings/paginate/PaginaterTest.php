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

namespace fw3\tests\strings\paginate;

use fw3\strings\paginate\Paginater;
use fw3\strings\url\http\HttpUrl;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PaginaterTest extends TestCase
{
    /**
     * @test
     */
    public function of(): void
    {
        $this->assertInstanceOf(Paginater::class, Paginater::of());

        $this->assertSame([
            'first'          => 1,
            'last'           => 1,
            'current'        => 1,
            'next'           => null,
            'previous'       => null,
            'link_list'      => [],
            'round_first'    => false,
            'round_last'     => false,
            'is_first'       => true,
            'is_last'        => true,
            'enable'         => false,
            'total'          => 0,
            'previous_count' => 0,
            'next_count'     => 0,
        ], Paginater::of()->toArray());

        $this->assertSame([
            'first'          => 1,
            'last'           => 2,
            'current'        => 1,
            'next'           => 2,
            'previous'       => null,
            'link_list'      => [
                1 => 1,
                2 => 2,
            ],
            'round_first'    => false,
            'round_last'     => false,
            'is_first'       => true,
            'is_last'        => false,
            'enable'         => true,
            'total'          => 11,
            'previous_count' => 1,
            'next_count'     => 10,
        ], Paginater::of(11)->toArray());
    }

    /**
     * @test
     */
    public function setUrlBuilder(): void
    {
        $paginater = Paginater::of(3)->setUrlBuilder(HttpUrl::of('https://ickx.jp'));
        $this->assertSame('https://ickx.jp?page=1', $paginater->buildUrl(1, 'page'));

        $paginater->setPageType(Paginater::PAGE_TYPE_PATH);
        $this->assertSame('https://ickx.jp/page/1', $paginater->buildUrl(1, 'page'));
    }
}
