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

namespace fw3\strings\url\http;

/**
 * HTTP URLを安全かつ容易に表現できるようにします。
 */
class HttpUrl
{
    /**
     * @var string スキーム：HTTP
     */
    public const SCHEME_HTTP       = 'http:';

    /**
     * @var string スキーム：HTTPS
     */
    public const SCHEME_HTTPS      = 'https:';

    /**
     * @var string デフォルトスキーム
     * @static
     */
    public const DEFAULT_SCHEME    = self::SCHEME_HTTPS;

    /**
     * @var string スキーム
     */
    protected string $scheme   = self::DEFAULT_SCHEME;

    /**
     * @var null|string ホスト
     */
    protected ?string $host = null;

    /**
     * @var null|int|string ポート
     */
    protected $port;

    /**
     * @var null|string|array パス
     */
    protected $path;

    /**
     * @var null|string|array 追加パス（定義済みのパスに更にパスを追加する）
     */
    protected $appendPath;

    /**
     * @var string|array クエリ
     */
    protected $query    = [];

    /**
     * @var ?string フラグメント
     */
    protected ?string $fragment = null;

    /**
     * @var string クエリセパレータ
     */
    protected string $querySeparator   = '&';

    /**
     * @var int クエリエンコードタイプ
     */
    protected int $queryEncType     = \PHP_QUERY_RFC3986;

    /**
     * @var bool 相対スキーマとするかどうか
     */
    protected bool $isRelativeScheme = false;

    /**
     * @var bool ホストが'/'で終わっているかどうか
     */
    protected bool $endSlashHost = false;

    /**
     * @var bool パスパートが'/'で終わっているかどうか
     */
    protected bool $endSlashPathPart = false;

    /**
     * factory
     *
     * @param  null|string       $url      URL
     * @param  null|string       $scheme   スキーマ
     * @param  null|string       $host     ホスト名
     * @param  null|string       $path     パスパート
     * @param  null|string|array $query    クエリパラメータ
     * @param  null|string       $fragment フラグメント
     * @param  null|int|string   $port     ポート
     * @return static|self       このインスタンス
     */
    public static function of(
        ?string $url = null,
        ?string $scheme = null,
        ?string $host = null,
        ?string $path = null,
        $query = null,
        ?string $fragment = null,
        $port = null,
    ) {
        return new static(
            $url ,
            $scheme ,
            $host ,
            $path ,
            $query,
            $fragment,
            $port ,
        );
    }

    /**
     * factory
     *
     * @param  null|string       $url      URL
     * @param  null|string       $scheme   スキーマ
     * @param  null|string       $host     ホスト名
     * @param  null|string       $path     パスパート
     * @param  null|string|array $query    クエリパラメータ
     * @param  null|string       $fragment フラグメント
     * @param  null|int|string   $port     ポート
     * @return static|self       このインスタンス
     */
    public function __construct(
        ?string $url = null,
        ?string $scheme = null,
        ?string $host = null,
        ?string $path = null,
        $query = null,
        ?string $fragment = null,
        $port = null,
    ) {
        if ($url !== null) {
            $this->setUrl($url);
        }

        if ($scheme !== null) {
            $this->scheme($scheme);
        }

        if ($host !== null) {
            $this->host($host);
        }

        if ($port !== null) {
            $this->port($port);
        }

        if ($path !== null) {
            $this->path($path);
        } else {
            $this->path = null;
        }

        $this->appendPath   = null;

        if ($query !== null) {
            if (\is_string($query)) {
                $this->query($query);
            } else {
                $this->setQuery($query);
            }
        }

        if ($fragment !== null) {
            $this->fragment($fragment);
        }

        return $this;
    }

    /**
     * URLをセットします。
     *
     * @param  string           $url 絶対URL
     * @return self|static|bool URL設定済みのこのインスタンス、絶対URL出ない場合はfalse
     */
    public function setUrl(string $url)
    {
        foreach (($parsed_url = \parse_url($url)) !== false ? $parsed_url : [] as $key => $value) {
            if ($key === 'query') {
                \parse_str($value, $value);
                $this->$key = $value;
            } else {
                $this->$key = $value;
            }
        }

        $path   = $this->host === null ? $url : \mb_substr($url, \mb_strpos($url, $this->host) + \mb_strlen($this->host));

        $this->endSlashHost = \mb_substr($path, 0, 1) === '/';

        $path   = \ltrim($path, '/');

        if ($path !== '') {
            if (empty($this->query)) {
                if ($this->fragment === null) {
                    $this->endSlashPathPart = \mb_substr($path, -1) === '/';
                } else {
                    $this->endSlashPathPart = \mb_substr($path, \mb_strpos($path, '#') - 1, 1) === '/';
                }
            } else {
                $this->endSlashPathPart = \mb_substr($path, \mb_strpos($path, '?') - 1, 1) === '/';
            }
        }

        return $this;
    }

    /**
     * 新しいインスタンスを返します。
     *
     * @return self|static 新しいインスタンス
     */
    public function with()
    {
        return clone $this;
    }

    /**
     * 新しいインスタンスにURLをセットして返します。
     *
     * @param  string            $url URL
     * @return self|static|false URL設定済みのシャローコピーされたインスタンス
     */
    public function withUrl(string $url)
    {
        $new = clone $this;
        $new->setUrl($url);

        return $new;
    }

    /**
     * 新しいインスタンスにスキームをセットして返します。
     *
     * @param  string            $scheme スキーム
     * @return self|static|false スキーム設定済みのシャローコピーされたインスタンス
     */
    public function withScheme(string $scheme)
    {
        $new = clone $this;
        $new->scheme($scheme);

        return $new;
    }

    /**
     * 相対スキーマにするかどうかを設定・取得します。
     *
     * @param  bool             $is_relative_scheme 相対スキーマにするかどうか
     * @return self|static|bool このインスタンスまたは相対スキーマにするかどうか
     */
    public function enabledRelativeScheme(?bool $is_relative_scheme = null)
    {
        if ($is_relative_scheme === null) {
            return $this->isRelativeScheme;
        }
        $this->isRelativeScheme = $is_relative_scheme;

        return $this;
    }

    /**
     * スキームを設定・取得します。
     *
     * @param  null|string             $scheme スキーム
     * @return null|self|static|string このインスタンスまたはスキーム
     */
    public function scheme(?string $scheme = null)
    {
        if ($scheme === null && \func_num_args() === 0) {
            return $this->scheme;
        }
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * ホストを設定・取得します。
     *
     * @param  null|string             $host ホスト
     * @return null|self|static|string このインスタンスまたはホスト
     */
    public function host(?string $host = null)
    {
        if ($host === null && \func_num_args() === 0) {
            return $this->host;
        }
        $this->host = $host;

        return $this;
    }

    /**
     * ポートを設定・取得します。
     *
     * @param  null|int|string         $port ポート
     * @return null|self|static|string このインスタンスまたはポート
     */
    public function port($port = null)
    {
        if ($port === null && \func_num_args() === 0) {
            return $this->port;
        }
        $this->port = $port;

        return $this;
    }

    /**
     * パスを設定・取得します。
     *
     * @param  null|string|array             $path パス
     * @return null|self|static|string|array このインスタンスまたはパス
     */
    public function path($path = null)
    {
        if ($path === null && \func_num_args() === 0) {
            return $this->path;
        }
        $this->path = $path;

        return $this;
    }

    /**
     * 追加のパスを設定・取得します。
     *
     * @param  null|string|array             $append_path 追加のパス
     * @return null|self|static|string|array このインスタンスまたは追加のパス
     */
    public function appendPath($append_path = null)
    {
        if ($append_path === null && \func_num_args() === 0) {
            return $this->appendPath;
        }
        $this->appendPath   = $append_path;

        return $this;
    }

    /**
     * クエリを設定・取得します。
     *
     * @param  null|string|array       $query クエリ
     * @return null|self|static|string このインスタンスまたはクエリ
     */
    public function query($query = null)
    {
        if ($query === null && \func_num_args() === 0) {
            return $this->query;
        }

        $this->query = $query;

        return $this;
    }

    /**
     * クエリを設定します。
     *
     * @param  string|array $name  名前
     * @param  null|mixed   $value 値
     * @return self|static  このインスタンス
     */
    public function setQuery($name, $value = null)
    {
        if (!\is_array($this->query)) {
            $query  = null;
            \parse_str($this->query, $query);
            $this->query    = $query;
        }

        foreach (\is_array($name) ? $name : [$name => $value] as $key => $value) {
            $this->query[$key] = $value;
        }

        return $this;
    }

    /**
     * 生クエリを設定します。
     *
     * @param  string      $query 生クエリ
     * @return self|static このインスタンス
     */
    public function setRawQuery(string $query)
    {
        \parse_str($query, $query);

        return $this->query($query);
    }

    /**
     * クエリセパレータを設定・取得します。
     *
     * @param  null|string             $query_separator クエリセパレータ
     * @return null|self|static|string このインスタンスまたはクエリセパレータ
     */
    public function querySeparator(?string $query_separator = null)
    {
        if ($query_separator === null && \func_num_args() === 0) {
            return $this->querySeparator;
        }
        $this->querySeparator = $query_separator;

        return $this;
    }

    /**
     * クエリエンコードタイプを設定・取得します。
     *
     * @param  null|int                $query_enc_type クエリエンコードタイプ
     * @return null|self|static|string このインスタンスまたはクエリエンコードタイプ
     */
    public function queryEncType(?int $query_enc_type = null)
    {
        if ($query_enc_type === null && \func_num_args() === 0) {
            return $this->queryEncType;
        }
        $this->queryEncType = $query_enc_type;

        return $this;
    }

    /**
     * フラグメントを設定・取得します。
     *
     * @param  null|string             $fragment フラグメント
     * @return null|self|static|string このインスタンスまたはフラグメント
     */
    public function fragment(?string $fragment = null)
    {
        if ($fragment === null && \func_num_args() === 0) {
            return $this->fragment;
        }
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * URLを構築し返します。
     *
     * @return string URL
     */
    public function build(): string
    {
        return $this->buildUrl();
    }

    /**
     * URLを構築し返します。
     *
     * @return string URL
     */
    public function buildUrl(): string
    {
        return \sprintf(
            '%s%s%s',
            ($path = $this->buildPath()) === '' ? $this->buildFqdnWithScheme() : \sprintf('%s%s', \rtrim($this->buildFqdnWithScheme(), '/'), $path),
            \is_string($query = $this->buildQuery())       && $query          !== '' ? \sprintf('?%s', $query) : '',
            \is_string($fragment = $this->buildFragment()) && $fragment       !== '' ? \sprintf('#%s', $fragment) : '',
        );
    }

    /**
     * 絶対URLを構築し返します。
     *
     * @return string URL
     */
    public function buildAbsoluteUrl(): string
    {
        return \sprintf(
            '%s%s%s',
            ($path = $this->buildPath()) === '' ? $this->buildAbsoluteFqdnWithScheme() : \sprintf('%s%s', \rtrim($this->buildAbsoluteFqdnWithScheme(), '/'), $path),
            \is_string($query = $this->buildQuery())       && $query          !== '' ? \sprintf('?%s', $query) : '',
            \is_string($fragment = $this->buildFragment()) && $fragment       !== '' ? \sprintf('#%s', $fragment) : '',
        );
    }

    /**
     * FQDNを構築して返します。
     *
     * @return string FQDN
     */
    public function buildFqdn(): string
    {
        return $this->host;
    }

    /**
     * Scheme付きのFQDNを構築して返します。
     *
     * @return string Scheme付きのFQDN
     */
    public function buildFqdnWithScheme(): string
    {
        return \sprintf(
            '%s//%s%s',
            $this->isRelativeScheme ? '' : \sprintf('%s:', \trim($this->scheme, ':')),
            $this->buildFqdn(),
            $this->endSlashHost ? '/' : '',
        );
    }

    /**
     * Scheme付きの絶対FQDNを構築して返します。
     *
     * @return string Scheme付きの絶対FQDN
     */
    public function buildAbsoluteFqdnWithScheme(): string
    {
        return \sprintf(
            '%s://%s%s',
            \trim($this->scheme, ':'),
            $this->buildFqdn(),
            $this->endSlashHost ? '/' : '',
        );
    }

    /**
     * 相対参照FQDNを構築して返します。
     *
     * @return string 相対参照FQDN
     */
    public function buildFqdnWithRelativeScheme(): string
    {
        return \sprintf('//%s%s', $this->buildFqdn(), $this->endSlashHost ? '/' : '');
    }

    /**
     * URLのパスパートを構築して返します。
     *
     * @return string URLのパスパート
     */
    public function buildPath(): string
    {
        $path   = [''];

        foreach (\is_array($this->path) ? $this->path : (array) \explode('/', $this->path ?? '') as $node) {
            if ($node === '') {
                continue;
            }

            if (null === $node) {
                continue;
            }

            $path[] = $node;
        }

        foreach (\is_array($this->appendPath) ? $this->appendPath : (array) \explode('/', $this->appendPath ?? '') as $node) {
            if ($node === '') {
                continue;
            }

            if (null === $node) {
                continue;
            }

            $path[] = $node;
        }

        if ($this->endSlashPathPart) {
            $path[] = '';
        }

        return \implode('/', $path);
    }

    /**
     * URLのクエリパートを構築して返します。
     *
     * @return string URLのクエリパート
     */
    public function buildQuery(): string
    {
        if (\is_array($this->query)) {
            return empty($this->query) ? '' : \http_build_query($this->query, '', $this->querySeparator, $this->queryEncType);
        }

        if ($this->query === '' || $this->query === null) {
            return '';
        }

        return \substr(\http_build_query([0 => $this->query], '', $this->querySeparator, $this->queryEncType), 2);
    }

    /**
     * URLのフラグメントパートを構築して返します。
     *
     * @return null|string URLのフラグメントパート
     */
    public function buildFragment(): ?string
    {
        return $this->fragment;
    }

    /**
     * このURLのスキームがHTTPまたはHTTPSであるか判定します。
     *
     * @return bool スキームがHTTPまたはHTTPSであればtrue, そうでなければfalse
     */
    public function isHttpScheme(): bool
    {
        return \in_array(\strtolower($this->scheme()), [static::SCHEME_HTTP, static::SCHEME_HTTPS], true);
    }

    /**
     * このURLのスキームがHTTPSであるか判定します。
     *
     * @return bool スキームがHTTPまたはHTTPSであればtrue, そうでなければfalse
     */
    public function isHttps(): bool
    {
        return \strtolower($this->scheme()) === static::SCHEME_HTTPS;
    }

    /**
     * URLを構築し返します。
     *
     * 極力__invokeまたは__toStringを使用してください。
     *
     * @return string URL
     * @see     static|:__invoke
     * @see     static|:__toString
     */
    public function toString(): string
    {
        return $this->buildUrl();
    }

    /**
     * URLを構築し返します。
     *
     * @return string URL
     */
    public function __invoke(): string
    {
        return $this->buildUrl();
    }

    /**
     * URLを構築し返します。
     *
     * @return string URL
     */
    public function __toString(): string
    {
        return $this->buildUrl();
    }
}
