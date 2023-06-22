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

namespace fw3\tests\strings\url\http;

use fw3\strings\url\http\HttpUrl;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class HttpUrlTest extends TestCase
{
    /**
     * @test
     */
    public function of(): void
    {
        $this->assertInstanceOf(HttpUrl::class, HttpUrl::of());

        $this->assertSame('https://ickx.jp', HttpUrl::of('https://ickx.jp')->build());
        $this->assertSame('http:', HttpUrl::of('https://ickx.jp', 'http:')->scheme());
        $this->assertSame('effy.info', HttpUrl::of('https://ickx.jp', null, 'effy.info')->host());
        $this->assertSame('/product', HttpUrl::of('https://ickx.jp', null, null, '/product')->path());
        $this->assertSame('a=b', HttpUrl::of('https://ickx.jp', null, null, null, 'a=b')->query());
        $this->assertSame(['a' => 'b'], HttpUrl::of('https://ickx.jp', null, null, null, ['a' => 'b'])->query());
        $this->assertSame('ickx', HttpUrl::of('https://ickx.jp', null, null, null, null, 'ickx')->fragment());
        $this->assertSame(80, HttpUrl::of('https://ickx.jp', null, null, null, null, null, 80)->port());

        $this->assertSame('http://effy.info/product?a%3Db#ickx', HttpUrl::of('https://ickx.jp', 'http:', 'effy.info', '/product', 'a=b', 'ickx', '80')->build());
        $this->assertSame('http://effy.info/product?a=b#ickx', HttpUrl::of('https://ickx.jp', 'http:', 'effy.info', '/product', ['a' => 'b'], 'ickx', '80')->build());
    }

    /**
     * @test
     */
    public function setUrl(): void
    {
        $httpUrl = HttpUrl::of();

        $this->assertInstanceOf(HttpUrl::class, $httpUrl->setUrl('https://ickx.jp'));
        $this->assertSame('https://ickx.jp', $httpUrl->build());

        $this->assertSame('https://ickx.jp/path/?a=b#frag', $httpUrl->setUrl('https://ickx.jp/path/?a=b#frag')->build());
    }

    /**
     * @test
     */
    public function with(): void
    {
        $httpUrl = HttpUrl::of();
        $httpUrl->setUrl('https://ickx.jp');

        $withHttpUrl    = $httpUrl->with();

        $this->assertInstanceOf(HttpUrl::class, $withHttpUrl);
        $this->assertEquals($httpUrl, $withHttpUrl);
        $this->assertNotSame($httpUrl, $withHttpUrl);

        $this->assertSame($httpUrl->build(), $withHttpUrl->build());
    }

    /**
     * @test
     */
    public function withUrl(): void
    {
        $httpUrl = HttpUrl::of();
        $httpUrl->setUrl('https://ickx.jp');

        $withHttpUrl    = $httpUrl->withUrl('https://effy.info');

        $this->assertInstanceOf(HttpUrl::class, $withHttpUrl);
        $this->assertNotEquals($httpUrl, $withHttpUrl);
        $this->assertNotSame($httpUrl, $withHttpUrl);

        $this->assertSame('https://effy.info', $withHttpUrl->build());
    }

    /**
     * @test
     */
    public function withScheme(): void
    {
        $httpUrl = HttpUrl::of();
        $httpUrl->setUrl('https://ickx.jp');

        $withHttpUrl    = $httpUrl->withScheme(HttpUrl::SCHEME_HTTP);

        $this->assertInstanceOf(HttpUrl::class, $withHttpUrl);
        $this->assertNotEquals($httpUrl, $withHttpUrl);
        $this->assertNotSame($httpUrl, $withHttpUrl);

        $this->assertSame('http://ickx.jp', $withHttpUrl->build());
    }

    /**
     * @test
     */
    public function isRelativeScheme(): void
    {
        $this->assertFalse(HttpUrl::of('https://ickx.jp')->enabledRelativeScheme());
        $this->assertSame('//ickx.jp', HttpUrl::of('https://ickx.jp')->enabledRelativeScheme(true)->build());
        $this->assertSame('https://ickx.jp', HttpUrl::of('https://ickx.jp')->enabledRelativeScheme(false)->build());
    }

    /**
     * @test
     */
    public function appendPath(): void
    {
        $this->assertSame('https://ickx.jp/product', HttpUrl::of('https://ickx.jp')->appendPath('product')->build());
        $this->assertSame('https://ickx.jp/developer/product', HttpUrl::of('https://ickx.jp')->appendPath('product')->path('developer')->build());
    }

    /**
     * @test
     */
    public function query(): void
    {
        $this->assertSame('https://ickx.jp?a%3Db', HttpUrl::of('https://ickx.jp')->query('a=b')->build());
        $this->assertSame('https://ickx.jp?a=b', HttpUrl::of('https://ickx.jp')->query(['a' => 'b'])->build());

        $this->assertSame('https://ickx.jp?a=b', HttpUrl::of('https://ickx.jp')->setQuery('a', 'b')->build());
        $this->assertSame('https://ickx.jp?a=b', HttpUrl::of('https://ickx.jp')->setQuery(['a' => 'b'])->build());

        $this->assertSame('https://ickx.jp?a=b', HttpUrl::of('https://ickx.jp')->setRawQuery('a=b')->build());
    }
}
