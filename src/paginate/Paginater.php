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

namespace fw3\strings\paginate;

use fw3\strings\builder\StringBuilder;
use fw3\strings\paginate\iterators\PaginateIterator;
use fw3\strings\url\http\HttpUrl;

/**
 * ページネーションを容易に実現するためのクラスです。
 */
class Paginater
{
    /**
     * ページ指定タイプ：クエリ
     */
    public const PAGE_TYPE_QUERY = 'query';

    /**
     * ページ指定タイプ：パス
     */
    public const PAGE_TYPE_PATH = 'path';

    /**
     * @var int デフォルトの1ページあたりの表示数
     */
    public const DISP_PER_PAGE = 10;

    /**
     * @var int デフォルトのリスト内の前から見た位置
     */
    public const IN_LIST_POSITION  = 3;

    /**
     * @var int デフォルトのリスト最大数
     */
    public const MAX_LIST_DISP = 5;

    /**
     * @var int 総件数
     */
    protected int $totalCount;

    /**
     * @var int 現在のページ
     */
    protected int $currentPage;

    /**
     * @var int 1ページあたりの件数
     */
    protected int $dispPerPage  = self::DISP_PER_PAGE;

    /**
     * @var int リスト内の前から見た位置
     */
    protected int $inListPosition   = self::IN_LIST_POSITION;

    /**
     * @ar  int     リスト最大数
     */
    protected $maxListDisp      = self::MAX_LIST_DISP;

    /**
     * @var int 利用できる最大ページ数
     */
    protected int $maxPage;

    /**
     * @var int 有効な範囲の現在ページ
     */
    protected int $availablePage;

    /**
     * @var int 開始ページ
     */
    protected int $firstPage    = 1;

    /**
     * @var int 終了ページ
     */
    protected int $lastPage;

    /**
     * @var null|int 次のページ
     */
    protected ?int $nextPage;

    /**
     * @var null|int 前のページ
     */
    protected ?int $previousPage;

    /**
     * @var int リンクリスト開始ページ
     */
    protected int $linkFirstPage;

    /**
     * @var int リンクリスト終了ページ
     */
    protected int $linkLastPage;

    /**
     * @var array リンクリスト用ページ配列
     */
    protected array $linkList;

    /**
     * @var bool 前方ページが丸まっているかどうか
     */
    protected bool $isRoundFirst;

    /**
     * @var bool 後方ページが丸まっているかどうか
     */
    protected bool $isRoundLast;

    /**
     * @var bool 現在のページが開始ページかどうか
     */
    protected bool $isFirstPage;

    /**
     * @var bool 現在のページが最終ページかどうか
     */
    protected bool $isLastPage;

    /**
     * @var int 前方のページ数
     */
    protected int $previousCount;

    /**
     * @var int 後方のページ数
     */
    protected int $nextCount;

    /**
     * @var bool ページャーが有効な状態かどうか
     */
    protected bool $enabled;

    /**
     * @var bool 値を評価済みかどうか
     */
    protected bool $evaluated    = false;

    /**
     * @var null|\Closure|StringBuilder|HttpUrl URLビルダー
     */
    protected $urlBuilder;

    /**
     * @var bool URLビルダが有効かどうか
     */
    protected bool $enabledUrlBuilder    = false;

    /**
     * @var string ページ値を持つキー名
     */
    protected ?string $pageKey  = null;

    /**
     * @var string URLビルダにHttpUrlを使用している場合のページの与え方
     */
    protected string $pageType  = self::PAGE_TYPE_QUERY;

    /**
     * @var array パス変数リスト
     */
    protected array $pathValueList    = [];

    /**
     * @var null|int \ArrayIterator オブジェクトの振る舞いを制御するフラグ
     */
    protected ?int $iteratorFlags = null;

    /**
     * factory
     *
     * @param  int                                 $total_count      総件数
     * @param  int                                 $current_page     現在のページ
     * @param  int                                 $disp_per_page    1ページあたりの件数
     * @param  null|\Closure|StringBuilder|HttpUrl $urlBuilder       URLビルダー
     * @param  int                                 $in_list_position リスト内の前から見た位置
     * @param  int                                 $max_list_disp    リスト最大数
     * @return self|static                         このインスタンス
     */
    public static function of(
        int $total_count = 0,
        int $current_page = 1,
        int $disp_per_page = self::DISP_PER_PAGE,
        $urlBuilder = null,
        int $in_list_position = self::IN_LIST_POSITION,
        int $max_list_disp = self::MAX_LIST_DISP,
    ) {
        $instance   = new self();

        $instance->setTotalCount($total_count);
        $instance->setCurrentPage($current_page);
        $instance->setDispPerPage($disp_per_page);

        if ($urlBuilder !== null) {
            $instance->setUrlBuilder($urlBuilder);
        }
        $instance->setInListPosition($in_list_position);
        $instance->setMaxListDisp($max_list_disp);

        $instance->evalValue();

        return $instance;
    }

    /**
     * constract
     */
    protected function __construct()
    {
    }

    /**
     * 総ページ数を設定します。
     *
     * @param  int         $total_count 総ページ数
     * @return self|static このインスタンス
     */
    public function setTotalCount(int $total_count)
    {
        $this->evaluated    = false;
        $this->totalCount   = $total_count;

        return $this;
    }

    /**
     * 現在のページを設定します。
     *
     * @parma   int     $current_page   現在のページ
     * @return self|static このインスタンス
     */
    public function setCurrentPage($current_page)
    {
        if ($current_page < 1) {
            throw new \Exception(\sprintf('最小ページ数以下のページ番号を指定されました。current_page:%d', $current_page));
        }

        $this->evaluated    = false;
        $this->currentPage  = $current_page;

        return $this;
    }

    /**
     * リスト内の前から見た位置を設定します。
     *
     * @param  int         $in_list_position リスト内の前から見た位置
     * @return self|static このインスタンス
     */
    public function setInListPosition(int $in_list_position)
    {
        $this->evaluated        = false;
        $this->inListPosition   = $in_list_position;

        return $this;
    }

    /**
     *利用できる最大ページ数を設定します。
     *
     * @param  int         $max_list_disp 利用できる最大ページ数
     * @return self|static このインスタンス
     */
    public function setMaxListDisp(int $max_list_disp)
    {
        $this->evaluated    = false;
        $this->maxListDisp  = $max_list_disp;

        return $this;
    }

    /**
     * 1ページあたりの件数を設定します。
     *
     * @param  int         $disp_per_page 1ページあたりの件数
     * @return self|static このインスタンス
     */
    public function setDispPerPage(int $disp_per_page)
    {
        $this->evaluated    = false;
        $this->dispPerPage  = $disp_per_page;

        return $this;
    }

    /**
     * URLビルダを設定します。
     *
     * @param  \Closure|HttpUrl|StringBuilder|string $urlBuilder URLビルダ
     * @return self|static                           このインスタンス
     */
    public function setUrlBuilder($urlBuilder)
    {
        $this->enabledUrlBuilder    = $urlBuilder instanceof \Closure
         || $urlBuilder instanceof HttpUrl || \is_subclass_of($urlBuilder, HttpUrl::class)
         || $urlBuilder instanceof StringBuilder || \is_subclass_of($urlBuilder, StringBuilder::class)
         || \is_string($urlBuilder);

        if ($this->enabledUrlBuilder) {
            $this->urlBuilder   = $urlBuilder;
        }

        return $this;
    }

    /**
     * URLビルダが有効化どうかを返します。
     *
     * @return bool URLビルダが有効化どうか
     */
    public function enabledUrlBuilder(): bool
    {
        return $this->enabledUrlBuilder;
    }

    /**
     * ページ値を持つキー名を設定します。
     *
     * @param  string      $page_key ページ値を持つキー名
     * @return self|static このインスタンス
     */
    public function setPageKey(string $page_key)
    {
        $this->pageKey  = $page_key;

        return $this;
    }

    /**
     * URLビルダにHttpUrlを使用している場合のページの与え方を設定します。
     *
     * @param  string      $page_type URLビルダにHttpUrlを使用している場合のページの与え方
     * @return self|static このインスタンス
     */
    public function setPageType(string $page_type)
    {
        $this->pageType = $page_type;

        return $this;
    }

    /**
     * パス変数を纏めて設定します。
     *
     * @param  array       $path_value_list パス変数
     * @return self|static このインスタンス
     */
    public function setPathValueList(array $path_value_list)
    {
        $this->pathValueList    = $path_value_list;

        return $this;
    }

    /**
     * パス変数を追加します。
     *
     * @param  string      $key   キー
     * @param  string      $value 値
     * @return self|static このインスタンス
     */
    public function appendPathValueList(string $key, string $value)
    {
        $this->pathValueList[$key]  = $value;

        return $this;
    }

    /**
     * パス変数を纏めて追加します。
     *
     * @param  array       $path_value_list パス設定
     * @return self|static このインスタンス
     */
    public function appendsPathValueList(array $path_value_list)
    {
        foreach ($path_value_list as $key => $value) {
            $this->pathValueList[$key]  = $value;
        }

        return $this;
    }

    /**
     * \ArrayIterator オブジェクトの振る舞いを制御するフラグを設定します。
     *
     * @param  null|int    $flags \ArrayIterator オブジェクトの振る舞いを制御するフラグ
     * @return self|static このインスタンス
     */
    public function setIteratorFlags(?int $flags)
    {
        $this->iteratorFlags    = $flags;

        return $this;
    }

    /**
     * ページャー情報を配列として返します。
     *
     * @return array ページャー情報
     */
    public function toArray(): array
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return [
            'first'             => $this->firstPage,
            'last'              => $this->lastPage,
            'current'           => $this->currentPage,
            'next'              => $this->nextPage,
            'previous'          => $this->previousPage,
            'link_list'         => $this->linkList,
            'round_first'       => $this->isRoundFirst,
            'round_last'        => $this->isRoundLast,
            'is_first'          => $this->isFirstPage,
            'is_last'           => $this->isLastPage,
            'enable'            => $this->enabled,
            'total'             => $this->totalCount,
            'previous_count'    => $this->previousCount,
            'next_count'        => $this->nextCount,
        ];
    }

    /**
     * 開始ページを返します。
     *
     * @return int 開始ページ
     */
    public function getFirstPage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->firstPage;
    }

    /**
     * 終了ページを返します。
     *
     * @return int 終了ページ
     */
    public function getLastPage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->lastPage;
    }

    /**
     * 現在のページを返します。
     *
     * @return int 現在のページ
     */
    public function getCurrentPage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->currentPage;
    }

    /**
     * 次のページを返します。
     *
     * @return int 次のページ
     */
    public function getNextPage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->nextPage;
    }

    /**
     * 前のページを返します。
     *
     * @return int 前のページ
     */
    public function getPreviousPage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->previousPage;
    }

    /**
     * リンクリストを返します。
     *
     * @return array リンクリスト
     */
    public function getLinkList(): array
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->linkList;
    }

    /**
     * 前方ページが丸まっているかどうかを返します。
     *
     * @return bool 前方ページが丸まっているかどうか
     */
    public function isRoundFirst(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->isRoundFirst;
    }

    /**
     * 前方ページが丸まっているかどうかを返します。
     *
     * @return bool 前方ページが丸まっているかどうか
     */
    public function isRoundLast(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->isRoundLast;
    }

    /**
     * 最初のページがlink listに含まれているかどうかを返します。
     *
     * @return bool 最初のページがlink listに含まれているかどうか
     */
    public function inLinkListFirstPage(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return isset($this->linkList[$this->firstPage]);
    }

    /**
     * 最後のページがlink listに含まれているかどうかを返します。
     *
     * @return bool 最後のページがlink listに含まれているかどうか
     */
    public function inLinkListLastPage(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return isset($this->linkList[$this->lastPage]);
    }

    /**
     * 現在のページが開始ページかどうかを返します。
     *
     * @return bool 現在のページが開始ページかどうか
     */
    public function isFirstPage(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->isFirstPage;
    }

    /**
     * 現在のページが最終ページかどうかを返します。
     *
     * @return bool 現在のページが最終ページかどうか
     */
    public function isLastPage(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->isLastPage;
    }

    /**
     * 開始ページリンクを表示しても良いかどうかを返します。
     *
     * @return bool 開始ページリンクを表示しても良いかどうか
     */
    public function enabledFirstPageLink(): bool
    {
        return !$this->isFirstPage() && $this->previousPage > $this->firstPage && $this->linkFirstPage > $this->firstPage;
    }

    /**
     * 前へページリンクを表示しても良いかどうかを返します。
     *
     * @return bool 前へページリンクを表示しても良いかどうか
     */
    public function enabledPreviousPageLink(): bool
    {
        return !$this->isFirstPage();
    }

    /**
     * 前へ方向の省略表記を表示しても良いかどうかを返します。
     *
     * @return bool 次へ方向の省略表記を表示しても良いかどうか
     */
    public function enabledPreviousRound(): bool
    {
        return $this->isRoundFirst();
    }

    /**
     * 次へ方向の省略表記を表示しても良いかどうかを返します。
     *
     * @return bool 次へ方向の省略表記を表示しても良いかどうか
     */
    public function enabledNextRound(): bool
    {
        return $this->isRoundLast();
    }

    /**
     * 次へページリンクを表示しても良いかどうかを返します。
     *
     * @return bool 次へページリンクを表示しても良いかどうか
     */
    public function enabledNextPageLink(): bool
    {
        return !$this->isLastPage();
    }

    /**
     * 最終ページリンクを表示しても良いかどうかを返します。
     *
     * @return bool 最終ページリンクを表示しても良いかどうか
     */
    public function enabledLastPageLink(): bool
    {
        return !$this->isLastPage() && $this->nextPage < $this->lastPage && $this->linkLastPage < $this->lastPage;
    }

    /**
     * 総ページ数を返します。
     *
     * @return int 総ページ数
     */
    public function getTotalCount(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->totalCount;
    }

    /**
     * 前方のページ数を返します。
     *
     * @return int 前方のページ数
     */
    public function getPreviousCount(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->previousCount;
    }

    /**
     * 後方のページ数を返します。
     *
     * @return int 後方のページ数
     */
    public function getNextCount(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->nextCount;
    }

    /**
     * 利用できる最大ページ数を返します。
     *
     * @return int 利用できる最大ページ数
     */
    public function getMaxPage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->maxPage;
    }

    /**
     * 有効な範囲の現在ページを返します。
     *
     * @return int 有効な範囲の現在ページを返します
     */
    public function getAvailablePage(): int
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->availablePage;
    }

    /**
     * ページャーが有効な状態かどうかを返します。
     *
     * @return bool ページャーが有効な状態かどうか
     */
    public function enabled(): bool
    {
        if (!$this->evaluated) {
            $this->evalValue();
        }

        return $this->enabled;
    }

    /**
     * ビルダーが設定されていた場合にページャーURLをビルドして返します。
     *
     * @param  mixed              $page_no ページ番号
     * @param  mixed              $key     キー
     * @return ページャーURL
     */
    public function buildUrl($page_no, $key = null)
    {
        if (!$this->enabledUrlBuilder) {
            return $page_no;
        }

        $key    = $key ?? $this->pageKey ?? 'page';

        $urlBuilder = $this->urlBuilder;

        if ($this->urlBuilder instanceof \Closure) {
            return $urlBuilder($page_no, $key);
        }

        if ($urlBuilder instanceof StringBuilder) {
            return $urlBuilder->build([$key => $page_no]);
        }

        if ($this->pageType === static::PAGE_TYPE_PATH) {
            return $urlBuilder->with()->appendPath(\sprintf('%s/%s', $key, $page_no))->build();
        }

        return $urlBuilder->with()->setQuery($key, $page_no)->build();
    }

    /**
     * 指定されたページが現在のページかどうかを返します。
     *
     * @param  int|string $page_no ページ
     * @return bool       指定されたページが現在のページかどうか
     */
    public function isCurrentPage($page_no): bool
    {
        return $this->currentPage === $page_no;
    }

    /**
     * 最初のページのURLを返します。
     *
     * @return string 最初のページのURL
     */
    public function buildFirstPageUrl(): string
    {
        return $this->buildUrl($this->firstPage);
    }

    /**
     * 前のページのURLを返します。
     *
     * @return string 前のページのURL
     */
    public function buildPreviousPageUrl(): string
    {
        return $this->buildUrl($this->previousPage);
    }

    /**
     * 現在のページのURLを返します。
     *
     * @return string 現在のページのURL
     */
    public function buildCurrentPageUrl(): string
    {
        return $this->buildUrl($this->currentPage);
    }

    /**
     * 次のページのURLを返します。
     *
     * @return string 最後のページのURL
     */
    public function buildNextPageUrl(): string
    {
        return $this->buildUrl($this->nextPage);
    }

    /**
     * 最後のページのURLを返します。
     *
     * @return string 最後のページのURL
     */
    public function buildLastPageUrl(): string
    {
        return $this->buildUrl($this->lastPage);
    }

    /**
     * リンクリストジェネレータ
     *
     * @param  string     $key ページキー
     * @return \Generator ジェネレータ
     */
    public function linkListGenerator(?string $key = null): \Generator
    {
        foreach ($this->linkList as $page_no) {
            yield $this->buildUrl($page_no, $key);
        }
    }

    /**
     * イテレータを返します。
     *
     * @param  int          $flags \ArrayIterator オブジェクトの振る舞いを制御するフラグ
     * @return \Traversable イテレータ
     */
    public function getIterator(int $flags = 0): \Traversable
    {
        return PaginateIterator::of($this, $this->linkList, $this->iteratorFlags ?? $flags);
    }

    /**
     * 値を評価します。
     */
    protected function evalValue(): void
    {
        // ==============================================
        // ページャーが有効な状態かどうか
        // ==============================================
        $this->enabled          = $this->totalCount > $this->dispPerPage;

        // ==============================================
        // ページャー無効
        // ==============================================
        if (!$this->enabled) {
            $this->maxPage          = 1;
            $this->availablePage    = 1;
            $this->currentPage      = 1;
            $this->linkLastPage     = 1;

            // ----------------------------------------------
            // リンクリスト
            // ----------------------------------------------
            $this->linkList = [];

            // ----------------------------------------------
            // 最終ページ
            // ----------------------------------------------
            $this->lastPage     = $this->maxPage;

            // ----------------------------------------------
            // 次のページ
            // ----------------------------------------------
            $this->nextPage     = null;

            // ----------------------------------------------
            // 前のページ
            // ----------------------------------------------
            $this->previousPage = null;

            // ----------------------------------------------
            // 前方ページが丸まっているかどうか
            // ----------------------------------------------
            $this->isRoundFirst = false;

            // 後方ページが丸まっているかどうか
            // ----------------------------------------------
            $this->isRoundLast  = false;
            // ----------------------------------------------

            // ----------------------------------------------
            // 現在のページが開始ページかどうか
            // ----------------------------------------------
            $this->isFirstPage  = true;

            // ----------------------------------------------
            // 現在のページが最終ページかどうか
            // ----------------------------------------------
            $this->isLastPage   = true;

            // ----------------------------------------------
            // 前方のページ数
            // ----------------------------------------------
            $this->previousCount    = 0;

            // ----------------------------------------------
            // 後方のページ数
            // ----------------------------------------------
            $this->nextCount        = 0;

            // ----------------------------------------------
            // 値評価済み
            // ----------------------------------------------
            $this->evaluated    = true;

            return;
        }

        // ==============================================
        // 利用できる最大ページ数
        // ==============================================
        $this->maxPage          = (int) \ceil($this->totalCount / $this->dispPerPage);

        if ($this->maxPage < $this->currentPage) {
            throw new \Exception(\sprintf('最大ページ数以上のページ番号を指定されました。page:%d, max_page:%s', $this->currentPage, $this->maxPage));
        }

        // ==============================================
        // 有効な範囲の現在ページ
        // ==============================================
        $this->availablePage    = $this->currentPage <= $this->maxPage ? $this->currentPage : $this->maxPage;

        // ==============================================
        // ページ数の構成
        // ==============================================
        // リンクリスト用ページ番号の構築
        // ----------------------------------------------
        // リンクリスト開始ページ
        // ----------------------------------------------
        if ($this->currentPage < $this->inListPosition) {
            $link_first_page    = 1;
        } else {
            $odd                = $this->maxListDisp % 2 === 1;
            $link_first_page    = $this->currentPage - $this->inListPosition + ($odd ? 1 : +1);

            if ($link_first_page + $this->maxListDisp > $this->maxPage) {
                $link_first_page = $this->maxPage - $this->maxListDisp + 1;
            }
        }

        if ($link_first_page < 1) {
            $link_first_page = 1;
        }

        $this->linkFirstPage    = $link_first_page;

        // ----------------------------------------------
        // リンクリスト終了ページ
        // ----------------------------------------------
        $link_last_page = $link_first_page + $this->maxListDisp - 1;

        if ($link_last_page > $this->maxPage) {
            $link_last_page = $this->maxPage;
        }

        $this->linkLastPage  = $link_last_page;

        // ----------------------------------------------
        // リンクリスト
        // ----------------------------------------------
        $link_list      = \range($this->linkFirstPage, $this->linkLastPage);
        $this->linkList = \array_combine($link_list, $link_list);

        // ----------------------------------------------
        // 最終ページ
        // ----------------------------------------------
        $this->lastPage  = $this->maxPage;

        // ----------------------------------------------
        // 次のページ
        // ----------------------------------------------
        $this->nextPage = $this->currentPage < $this->maxPage ? $this->currentPage + 1 : null;

        // ----------------------------------------------
        // 前のページ
        // ----------------------------------------------
        $this->previousPage = $this->currentPage > 1 ? $this->currentPage - 1 : null;

        // ----------------------------------------------
        // 前方ページが丸まっているかどうか
        // ----------------------------------------------
        $this->isRoundFirst = $this->linkFirstPage > 2;

        // ----------------------------------------------
        // 後方ページが丸まっているかどうか
        // ----------------------------------------------
        $this->isRoundLast  = $this->linkLastPage < $this->maxPage - 1;

        // ----------------------------------------------
        // 現在のページが開始ページかどうか
        // ----------------------------------------------
        $this->isFirstPage  = $this->currentPage === 1;

        // ----------------------------------------------
        // 現在のページが最終ページかどうか
        // ----------------------------------------------
        $this->isLastPage   = $this->currentPage === $this->maxPage;

        // ----------------------------------------------
        // 前方のページ数
        // ----------------------------------------------
        $this->previousCount    = $this->maxPage === 1 ? 1 : ($this->currentPage - 1) * $this->dispPerPage + 1;

        // ----------------------------------------------
        // 後方のページ数
        // ----------------------------------------------
        $this->nextCount        = $this->maxPage === $this->currentPage ? $this->totalCount : $this->currentPage * $this->dispPerPage;

        // ----------------------------------------------
        // 値評価済み
        // ----------------------------------------------
        $this->evaluated    = true;
    }
}
