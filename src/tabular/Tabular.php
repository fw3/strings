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

namespace fw3\strings\tabular;

use fw3\strings\converter\Convert;

/**
 * 文字配列に対する表化を提供します。
 */
class Tabular
{
    // ==============================================
    // constants
    // ==============================================
    /**
     * @var string ビルダキャッシュのデフォルト名
     */
    public const DEFAULT_NAME                  = ':default:';

    /**
     * @var string エンコーディングのデフォルト値
     */
    public const DEFAULT_CHARACTER_ENCODING    = 'UTF-8';

    /**
     * @var int タブ幅のデフォルト値
     */
    public const DEFAULT_TAB_WIDTH = 4;

    /**
     * @var int インデントレベルのデフォルト値
     */
    public const DEFAULT_INDENTE_LEVEL     = 0;

    /**
     * @var int インデント向け基底文字列長：`    public static `
     */
    public const INDENTE_BASE_LENGTH_PUBLIC_STATIC     = 18;

    /**
     * @var int インデント向け基底文字列長：`    protected static `
     */
    public const INDENTE_BASE_LENGTH_PROTECTED_STATIC  = 21;

    /**
     * @var int インデント向け基底文字列長：`    private static `
     */
    public const INDENTE_BASE_LENGTH_PRIVATE_STATIC    = 19;

    /**
     * @var int インデント向け基底文字列長：`     * @var `
     */
    public const INDENTE_BASE_LENGTH_DOC_COMMENT_VAR_TYPE  = 12;

    /**
     * @var int インデント向け基底文字列長：`     * @param   `
     */
    public const INDENTE_BASE_LENGTH_DOC_COMMENT_PARAM = 16;

    /**
     * @var int インデント向け基底文字列長：`     * @return  `
     */
    public const INDENTE_BASE_LENGTH_DOC_COMMENT_RETURN    = 16;

    /**
     * @var int インデント向け基底文字列長：`    const `
     */
    public const INDENTE_BASE_LENGTH_CONST = 10;

    /**
     * @var int インデント向け基底文字列長：`    public const `
     */
    public const INDENTE_BASE_LENGTH_PUBLIC_CONST = 17;

    /**
     * @var int インデント向け基底文字列長：`    protected const `
     */
    public const INDENTE_BASE_LENGTH_PROTECTED_CONST   = 20;

    /**
     * @var int インデント向け基底文字列長：`    protected const `
     */
    public const INDENTE_BASE_LENGTH_PRIVATE_CONST = 18;

    /**
     * @var string インデントで使用する文字
     */
    public const INDENTE_CHAR  = ' ';

    /**
     * @var array インデント向け基底文字列長マップ
     */
    public const INDENTE_BASE_LENGTH_MAP  = [
        'public static'         => self::INDENTE_BASE_LENGTH_PUBLIC_STATIC,
        'protected static'      => self::INDENTE_BASE_LENGTH_PROTECTED_STATIC,
        'private static'        => self::INDENTE_BASE_LENGTH_PRIVATE_STATIC,
        'doc comment var type'  => self::INDENTE_BASE_LENGTH_DOC_COMMENT_VAR_TYPE,
        'doc comment param'     => self::INDENTE_BASE_LENGTH_DOC_COMMENT_PARAM,
        'doc comment return'    => self::INDENTE_BASE_LENGTH_DOC_COMMENT_RETURN,
        'const'                 => self::INDENTE_BASE_LENGTH_CONST,
        'public const'          => self::INDENTE_BASE_LENGTH_PUBLIC_CONST,
        'protected const'       => self::INDENTE_BASE_LENGTH_PROTECTED_CONST,
        'private const'         => self::INDENTE_BASE_LENGTH_PRIVATE_CONST,
    ];

    // ==============================================
    // static properties
    // ==============================================
    /**
     * @var array インスタンスキャッシュ
     */
    protected static array $instanceCache  = [];

    /**
     * @var string クラスデフォルトのエンコーディング
     */
    protected static string $defaultCharacterEncoding   = self::DEFAULT_CHARACTER_ENCODING;

    /**
     * @var int クラスデフォルトのタブ幅のデフォルト値
     */
    protected static int $defaultTabWidth   = self::DEFAULT_TAB_WIDTH;

    /**
     * @var int クラスデフォルトのインデントレベル
     */
    protected static int $defaultIndentLevel    = self::DEFAULT_INDENTE_LEVEL;

    /**
     * @var array クラスデフォルトのヘッダ
     */
    protected static array $defaultHeader = [];

    /**
     * @var array クラスデフォルトのタブ化対象データ
     */
    protected static array $defaultRows   = [];

    /**
     * @var bool クラスデフォルトの行末スペーストリムを行うかどうか
     */
    protected static bool $defaultTrimEolSpace   = false;

    // ==============================================
    // properties
    // ==============================================
    /**
     * @var null|string Tabularキャッシュ名
     */
    protected ?string $cacheName    = null;

    /**
     * @var null|string Tabular名
     */
    protected ?string $name = null;

    /**
     * @var null|string エンコーディング
     */
    protected ?string $characterEncoding;

    /**
     * @var null|int タブ幅
     */
    protected ?int $tabWidth;

    /**
     * @var null|int インデントレベル
     */
    protected ?int $indentLevel;

    /**
     * @var array ヘッダ
     */
    protected array $header   = [];

    /**
     * @var array タブ化対象データ
     */
    protected array $rows     = [];

    /**
     * @var null|int ベースとなるインデント量
     */
    protected ?int $baseIndente  = null;

    /**
     * @var null|array 列単位での最大幅マップ
     */
    protected ?array $preBuildeMaxWidthMap     = null;

    /**
     * @var bool 列の全てがnullだった場合に列をスキップするかどうか
     */
    protected bool $nullColumnSkip   = false;

    /**
     * @var null|array 全てがnullでない列マップ
     */
    protected ?array $notNullColumnMap = null;

    /**
     * @var null|array セル単位での最大幅マップ
     */
    protected ?array $preBuildeCellMaxWidthMap = null;

    /**
     * @var bool 行末スペーストリムを行うかどうか
     */
    protected bool $trimEolSpace = false;

    /**
     * factory
     *
     * @param   string||array|null  $name           ビルダ名
     * @param  null|int    $tab_width    タブ幅
     * @param  null|int    $indent_level インデントレベル
     * @param  null|string $encoding     エンコーディング
     * @return self|static このインスタンス
     */
    public static function factory($name = self::DEFAULT_NAME, ?int $tab_width = null, ?int $indent_level = null, ?string $encoding = null)
    {
        $cache_name = \is_array($name) ? \implode('::', $name) : $name;

        if (!isset(static::$instanceCache[$cache_name])) {
            static::$instanceCache[$cache_name] = new static($cache_name, $tab_width, $indent_level, $encoding);
            static::$instanceCache[$cache_name]->setName($name);
        }

        return static::$instanceCache[$cache_name];
    }

    /**
     * インスタンスをキャッシュしない使い捨てファクトリです。
     *
     * @param  null|int    $tab_width    タブ幅
     * @param  null|int    $indent_level インデントレベル
     * @param  null|string $encoding     エンコーディング
     * @return self|static このインスタンス
     */
    public static function disposableFactory(?int $tab_width = null, ?int $indent_level = null, ?string $encoding = null)
    {
        return new static(null, $tab_width, $indent_level, $encoding);
    }

    // ==============================================
    // static methods
    // ==============================================
    /**
     * 指定されたビルダ名に紐づくビルダインスタンスを返します。
     *
     * @param   string||array|null  $name   ビルダ名
     * @return self|static このインスタンス
     */
    public static function get($name = self::DEFAULT_NAME)
    {
        $cache_name = \is_array($name) ? \implode('::', $name) : $name;

        if (!isset(static::$instanceCache[$cache_name])) {
            throw new \OutOfBoundsException(\sprintf('Tabularキャッシュに無いキーを指定されました。name:%s', Convert::toDebugString($name, 2)));
        }

        return static::$instanceCache[$cache_name];
    }

    /**
     * 指定されたビルダキャッシュ名に紐づくビルダキャッシュを削除します。
     *
     * @param  string $name ビルダ名
     * @return string このクラスパス
     */
    public static function remove(string $name): string
    {
        $cache_name = \is_array($name) ? \implode('::', $name) : $name;

        unset(static::$instanceCache[$cache_name]);

        return static::class;
    }

    // ==============================================
    // static property accessors
    // ==============================================
    /**
     * デフォルトの設定を纏めて設定・取得します。
     *
     * @param  null|array   $default_settings デフォルトの設定
     * @return string|array このクラスパスまたはデフォルトの設定
     */
    public static function defaultSettings(?array $default_settings = null)
    {
        if (!\is_array($default_settings)) {
            return [
                'header'                => static::defaultHeader(),
                'rows'                  => static::defaultRows(),
                'tab_width'             => static::defaultTabWidth(),
                'indent_level'          => static::defaultindentLevel(),
                'character_encodingg'   => static::defaultCharacterEncoding(),
                'trim_eol_space'        => static::defaultTrimEolSpace(),
            ];
        }

        if (isset($default_settings['header'])) {
            static::defaultHeader($default_settings['header']);
        }

        if (isset($default_settings['rows'])) {
            static::defaultRows($default_settings['rows']);
        }

        if (isset($default_settings['tab_width'])) {
            static::defaultTabWidth($default_settings['tab_width']);
        }

        if (isset($default_settings['indent_level'])) {
            static::defaultindentLevel($default_settings['indent_level']);
        }

        if (isset($default_settings['character_encodingg'])) {
            static::defaultCharacterEncoding($default_settings['character_encodingg']);
        }

        if (isset($default_settings['trim_eol_space'])) {
            static::defaultTrimEolSpace($default_settings['trim_eol_space']);
        }

        return static::class;
    }

    /**
     * クラスデフォルトのヘッダを設定・取得します。
     *
     * @param  null|array|\Closure   $header クラスデフォルトのヘッダ
     * @return string|array|\Closure このクラスパスまたはクラスデフォルトのヘッダ
     */
    public static function defaultHeader($header = null)
    {
        if ($header === null) {
            return static::$defaultHeader;
        }

        if (!\is_array($header) && !($header instanceof \Closure)) {
            throw new \Exception(\sprintf('利用できない値を指定されました。header:%s', Convert::toDebugString($header, 2)));
        }

        static::$defaultHeader  = $header;

        return static::class;
    }

    /**
     * クラスデフォルトの行を設定・取得します。
     *
     * @param  null|array|\Closure   $rows クラスデフォルトの行
     * @return string|array|\Closure このクラスパスまたはクラスデフォルトの行
     */
    public static function defaultRows($rows = null)
    {
        if ($rows === null) {
            return static::$defaultRows;
        }

        if (!\is_array($rows) && !($rows instanceof \Closure)) {
            throw new \Exception(\sprintf('利用できない値を指定されました。rows:%s', Convert::toDebugString($rows, 2)));
        }

        static::$defaultRows    = $rows;

        return static::class;
    }

    /**
     * クラスデフォルトのタブ幅を設定・取得します。
     *
     * @param  null|int|string $tab_width クラスデフォルトのタブ幅
     * @return string|int      このクラスパスまたはクラスデフォルトのタブ幅
     */
    public static function defaultTabWidth($tab_width = null)
    {
        if ($tab_width === null) {
            return static::$defaultTabWidth;
        }

        if (!\is_int($tab_width) && !(\is_string($tab_width) && \filter_var($tab_width, \FILTER_VALIDATE_INT))) {
            throw new \Exception(\sprintf('利用できない値を指定されました。tab_width:%s', Convert::toDebugString($tab_width, 2)));
        }

        static::$defaultTabWidth = $tab_width;

        return static::class;
    }

    /**
     * クラスデフォルトのインデントレベルを設定・取得します。
     *
     * @return string|int このクラスパスまたはクラスデフォルトのインデントレベル
     */
    public static function defaultindentLevel($indent_level = null)
    {
        if ($indent_level === null) {
            return static::$defaultIndentLevel;
        }

        if (!\is_int($indent_level) && !(\is_string($indent_level) && \filter_var($indent_level, \FILTER_VALIDATE_INT))) {
            throw new \Exception(\sprintf('利用できない値を指定されました。indent_level:%s', Convert::toDebugString($indent_level, 2)));
        }

        static::$defaultIndentLevel = $indent_level;

        return static::class;
    }

    /**
     * クラスデフォルトのエンコーディングを設定・取得します。
     *
     * @param  null|string $character_encoding クラスデフォルトのエンコーディング
     * @return null|string このクラスパスまたはクラスデフォルトのエンコーディング
     */
    public static function defaultCharacterEncoding(?string $character_encoding = null): ?string
    {
        if ($character_encoding === null && \func_num_args() === 0) {
            return static::$defaultCharacterEncoding;
        }

        if ($character_encoding === null) {
            static::$defaultCharacterEncoding = $character_encoding;

            return static::class;
        }

        if (!\in_array($character_encoding, \mb_list_encodings(), true)) {
            throw new \InvalidArgumentException(\sprintf('現在のシステムで利用できないエンコーディングを指定されました。character_encoding:%s', Convert::toDebugString($character_encoding)));
        }

        static::$defaultCharacterEncoding = $character_encoding;

        return static::class;
    }

    /**
     * クラスデフォルトの行末スペーストリムを行うかどうかを設定・取得します。
     *
     * @param  null|bool $trim_eol_space クラスデフォルトの行末スペーストリムを行うかどうか
     * @return null|bool このクラスパスまたはクラスデフォルトの行末スペーストリムを行うかどうか
     */
    public static function defaultTrimEolSpace(?bool $trim_eol_space = false): ?bool
    {
        if ($trim_eol_space === false && \func_num_args() === 0) {
            return static::$defaultTrimEolSpace;
        }

        if (!\is_bool($trim_eol_space)) {
            throw new \InvalidArgumentException(\sprintf('利用できない値を指定されました。trim_eol_space:%s', Convert::toDebugString($trim_eol_space, 2)));
        }

        static::$defaultTrimEolSpace    = $trim_eol_space;

        return static::class;
    }

    // ==============================================
    // factory methods
    // ==============================================
    /**
     * construct
     *
     * @param null|string $cache_name   ビルダキャッシュ名
     * @param null|int    $tab_width    タブ幅
     * @param null|int    $indent_level インデントレベル
     * @param null|string $encoding     エンコーディング
     */
    protected function __construct(?string $cache_name, ?int $tab_width = null, ?int $indent_level = null, ?string $encoding = null)
    {
        $this->cacheName    = $cache_name;

        $this->tabWidth($tab_width ?? static::$defaultTabWidth);
        $this->indentLevel($indent_level ?? static::$defaultIndentLevel);

        $this->header(static::$defaultHeader);
        $this->rows(static::$defaultRows);

        $this->characterEncoding    = $encoding ?? (
            static::$defaultCharacterEncoding ?? \mb_internal_encoding()
        );

        $this->trimEolSpace(static::$defaultTrimEolSpace);
    }

    // ==============================================
    // property accessors
    // ==============================================
    /**
     * 設定を纏めて設定・取得します。
     *
     * @param  null|array   $settings 設定
     * @return static|array このインスタンスまたは設定
     */
    public function settings(?array $settings = null)
    {
        if (!\is_array($settings)) {
            return [
                'header'                => $this->header(),
                'rows'                  => $this->rows(),
                'tab_width'             => $this->tabWidth(),
                'indent_level'          => $this->indentLevel(),
                'character_encodingg'   => $this->characterEncoding(),
                'trim_eol_space'        => $this->trimEolSpace(),
            ];
        }

        if (isset($settings['header'])) {
            $this->header($settings['header']);
        }

        if (isset($settings['rows'])) {
            $this->rows($settings['rows']);
        }

        if (isset($settings['tab_width'])) {
            $this->tabWidth($settings['tab_width']);
        }

        if (isset($settings['indent_level'])) {
            $this->indentLevel($settings['indent_level']);
        }

        if (isset($settings['character_encodingg'])) {
            $this->characterEncoding($settings['character_encodingg']);
        }

        if (isset($settings['trim_eol_space'])) {
            $this->trimEolSpace($settings['trim_eol_space']);
        }

        return $this;
    }

    /**
     * ビルダキャッシュ名を返します。
     *
     * @return string ビルダキャッシュ名
     */
    public function getCacheName(): string
    {
        return $this->cacheName;
    }

    /**
     * ビルダ名を返します。
     *
     * @return string ビルダ名
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 行を追加します。
     *
     * @param  array       $row 行
     * @return self|static このインスタンス
     */
    public function addRow(array $row)
    {
        $this->initPreBuilding();

        $this->rows[]   = $row;

        return $this;
    }

    /**
     * 複数行を追加します。
     *
     * @param  array       $rows 複数行
     * @return self|static このインスタンス
     */
    public function addRows(array $rows)
    {
        $this->initPreBuilding();

        foreach ($rows as $row) {
            if (!\is_array($row)) {
                throw new \Exception(\sprintf('次元の足りない行を指定されました。row:%s', Convert::toDebugString($row, 2)));
            }

            $this->rows[]   = $row;
        }

        return $this;
    }

    /**
     * ヘッダを設定・取得します。
     *
     * @param  null|array|\Closure   $header ヘッダ
     * @return static|array|\Closure このインスタンスまたはヘッダ
     */
    public function header($header = null)
    {
        if ($header === null) {
            return $this->header;
        }

        if (!\is_array($header) && !($header instanceof \Closure)) {
            throw new \Exception(\sprintf('利用できない値を指定されました。header:%s', Convert::toDebugString($header, 2)));
        }

        $this->initPreBuilding();

        $this->header  = $header;

        return $this;
    }

    /**
     * 行を設定・取得します。
     *
     * @param  null|array|\Closure   $rows 行
     * @return static|array|\Closure このインスタンスまたは行
     */
    public function rows($rows = null)
    {
        if ($rows === null) {
            return $this->rows;
        }

        if (!\is_array($rows) && !($rows instanceof \Closure)) {
            throw new \Exception(\sprintf('利用できない値を指定されました。rows:%s', Convert::toDebugString($rows, 2)));
        }

        $this->initPreBuilding();

        $this->rows    = $rows;

        return $this;
    }

    /**
     * 空の状態かどうかを返します。
     *
     * @return bool 空の状態かどうか
     */
    public function isEmpty(): bool
    {
        return $this->isHeaderEmpty() && $this->isRowEmpty();
    }

    /**
     * ヘッダが空かどうかを返します。
     *
     * @return bool ヘッダが空かどうか
     */
    public function isHeaderEmpty(): bool
    {
        return empty($this->header);
    }

    /**
     * 行が空かどうかを返します。
     *
     * @return bool 行が空かどうか
     */
    public function isRowEmpty(): bool
    {
        return empty($this->rows);
    }

    /**
     * タブ幅を設定・取得します。
     *
     * @param  null|int|string   $tab_width タブ幅
     * @return static|int|string このインスタンスまたはタブ幅
     */
    public function tabWidth($tab_width = null)
    {
        if ($tab_width === null) {
            return $this->tabWidth;
        }

        if (!\is_int($tab_width) && !(\is_string($tab_width) && \filter_var($tab_width, \FILTER_VALIDATE_INT))) {
            throw new \Exception(\sprintf('利用できない値を指定されました。tab_width:%s', Convert::toDebugString($tab_width, 2)));
        }

        $this->initPreBuilding();

        $this->tabWidth = $tab_width;

        return $this;
    }

    /**
     * インデントレベルを設定・取得します。
     *
     * @return static|int|string このインスタンスまたはインデントレベル
     */
    public function indentLevel($indent_level = null)
    {
        if ($indent_level === null) {
            return $this->indentLevel;
        }

        if (!\is_int($indent_level) && !(\is_string($indent_level) && \filter_var($indent_level, \FILTER_VALIDATE_INT))) {
            throw new \Exception(\sprintf('利用できない値を指定されました。indent_level:%s', Convert::toDebugString($indent_level, 2)));
        }

        $this->initPreBuilding();

        $this->indentLevel = $indent_level;

        return $this;
    }

    /**
     * エンコーディングを設定・取得します。
     *
     * @param  null|string        $character_encoding エンコーディング
     * @return null|static|string このインスタンスまたはエンコーディング
     */
    public function characterEncoding(?string $character_encoding = null)
    {
        if ($character_encoding === null && \func_num_args() === 0) {
            return $this->characterEncoding;
        }

        if ($character_encoding === null) {
            $this->characterEncoding = $character_encoding;

            return $this;
        }

        if (!\in_array($character_encoding, \mb_list_encodings(), true)) {
            throw new \InvalidArgumentException(\sprintf('現在のシステムで利用できないエンコーディングを指定されました。character_encoding:%s', Convert::toDebugString($character_encoding)));
        }

        $this->initPreBuilding();

        $this->characterEncoding = $character_encoding;

        return $this;
    }

    /**
     * クラスデフォルトの行末スペーストリムを行うかどうかを設定・取得します。
     *
     * @param  null|bool        $trim_eol_space 行末スペーストリムを行うかどうか
     * @return null|static|bool このクラスパスまたは行末スペーストリムを行うかどうか
     */
    public function trimEolSpace(?bool $trim_eol_space = false)
    {
        if ($trim_eol_space === false && \func_num_args() === 0) {
            return $this->trimEolSpace;
        }

        if (!\is_bool($trim_eol_space)) {
            throw new \InvalidArgumentException(\sprintf('利用できない値を指定されました。trim_eol_space:%s', Convert::toDebugString($trim_eol_space, 2)));
        }

        $this->trimEolSpace = $trim_eol_space;

        return $this;
    }

    /**
     * 列の全てがnullだった場合に列をスキップするかどうかを設定・取得します。
     *
     * @param  bool        $null_column_skip 列の全てがnullだった場合に列をスキップするかどうか
     * @return static|bool このインスタンスまたは列の全てがnullだった場合に列をスキップするかどうか
     */
    public function nullColumnSkip(bool $null_column_skip = false)
    {
        if ($null_column_skip === false && \func_num_args() === 0) {
            return $this->nullColumnSkip;
        }

        $this->nullColumnSkip   = $null_column_skip;

        return $this;
    }

    // ==============================================
    // supporter
    // ==============================================
    /**
     * 文字列幅を取得します。
     *
     * @param   string  幅を取得したい文字列
     * @return int 文字列幅
     */
    public function stringWidth($string): int
    {
        $convert_charrcter_encoding = $this->characterEncoding !== 'UTF-8';

        if ($convert_charrcter_encoding) {
            $string = \mb_convert_encoding($string, 'UTF-8', $this->characterEncoding);
        }

        $string_width = 0;

        for ($string_length = \mb_strlen($string, 'UTF-8'), $i = 0;$i < $string_length;++$i) {
            $char   = \mb_substr($string, $i, 1, 'UTF-8');

            if ($char !== \mb_convert_encoding($char, 'UTF-8', 'UTF-8')) {
                $char_code  = 0xFFFD;
            } else {
                $ret        = \mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
                $char_code  = \hexdec(\bin2hex($ret));
            }

            $width = 0;

            if (0x0000 <= $char_code && $char_code <= 0x0019) {
            } elseif (0x0020 <= $char_code && $char_code <= 0x1FFF) {
                $width  = 1;
            } elseif (0x2000 <= $char_code && $char_code <= 0xFF60) {
                $width  = 2;
            } elseif (0xFF61 <= $char_code && $char_code <= 0xFF9F) {
                $width  = 1;
            } elseif (0xFFA0 <= $char_code) {
                $width  = 2;
            }

            $string_width += $width;
        }

        return $string_width;
    }

    // ==============================================
    // builder
    // ==============================================
    /**
     * セル内の最大文字幅を返します。
     *
     * @return array セル内の最大文字幅
     */
    public function buildMaxWidthMap(): array
    {
        $max_width_map      = [];
        $not_null_col_map   = [];

        foreach (\array_values($this->header) as $idx => $node) {
            if ($node !== null) {
                $not_null_col_map[$idx] = $idx;
            }

            $max_width_map[$idx]    = $this->stringWidth($node);
        }

        $rows   = $this->rows;
        \reset($rows);

        if (empty($rows)) {
            return $max_width_map;
        }

        if (empty($max_width_map)) {
            foreach (\array_values(\current($rows)) as $idx => $node) {
                if ($node !== null) {
                    $not_null_col_map[$idx] = $idx;
                }

                $node_width = $this->stringWidth($node);

                if (isset($max_width_map[$idx])) {
                    $max_width_map[$idx] > $node_width ?: $max_width_map[$idx] = $node_width;
                } else {
                    $max_width_map[$idx]    = 0;
                }
            }
        }

        foreach ($rows as $row) {
            foreach (\array_values($row) as $idx => $node) {
                if ($node !== null) {
                    $not_null_col_map[$idx] = $idx;
                }

                $node_width                                                = $this->stringWidth($node);
                $max_width_map[$idx] > $node_width ?: $max_width_map[$idx] = $node_width;
            }
        }

        $this->notNullColumnMap     = $not_null_col_map;
        $this->preBuildeMaxWidthMap = $max_width_map;

        return $max_width_map;
    }

    /**
     * インデントを加味したセル幅マップを構築し返します。
     *
     * @return array インデントを加味したセル幅マップ
     */
    public function buildCellWidthMap(): array
    {
        if ($this->preBuildeMaxWidthMap === null) {
            $this->buildMaxWidthMap();
        }
        $max_width_map  = $this->preBuildeMaxWidthMap;

        $base_indente   = 0;

        if (\is_int($this->baseIndente)) {
            $base_indente   = $this->baseIndente;
        } elseif (\is_string($this->baseIndente) && isset(static::INDENTE_BASE_LENGTH_MAP[$this->baseIndente])) {
            $base_indente   = static::INDENTE_BASE_LENGTH_MAP[$this->baseIndente];
        }

        $tab_width  = $this->tabWidth;
        $base_width = $base_indente + $this->indentLevel * $tab_width;

        $cell_max_width_map = [];

        if (\is_array($max_width_map)) {
            foreach ($max_width_map as $idx => $cell_in_max_width) {
                $cell_max_width_map[$idx] = 0 === ($indente = ($base_width + $cell_in_max_width) % $tab_width) ? $cell_in_max_width + $tab_width : $cell_in_max_width + $tab_width - $indente;
            }
        }

        $this->preBuildeCellMaxWidthMap = $cell_max_width_map;

        return $cell_max_width_map;
    }

    /**
     * フィル用のリパート文字列を作成し返します。
     *
     * @param  string $string 元の文字列
     * @param  int    $idx    列番号
     * @param  string $repart リパート文字
     * @return string フィル用のリパート文字列
     */
    public function buildRepart(string $string, int $idx, string $repart = self::INDENTE_CHAR): string
    {
        if ($this->preBuildeCellMaxWidthMap === null) {
            $this->buildCellWidthMap();
        }
        $cell_max_width_map  = $this->preBuildeCellMaxWidthMap;

        return \str_repeat($repart, $cell_max_width_map[$idx] - $this->stringWidth($string));
    }

    /**
     * ビルドします。
     *
     * @return array ビルド後の文字配列スタック
     */
    public function build(): array
    {
        if ($this->preBuildeMaxWidthMap === null) {
            $this->buildMaxWidthMap();
        }

        $stack  = [];

        $base_indente   = 0;

        if (\is_int($this->baseIndente)) {
            $base_indente   = \str_repeat(static::INDENTE_CHAR, ($this->indentLevel * $this->tabWidth) + $this->baseIndente);
        } elseif (\is_string($this->baseIndente) && isset(static::INDENTE_BASE_LENGTH_MAP[$this->baseIndente])) {
            $base_indente   = \str_repeat(static::INDENTE_CHAR, static::INDENTE_BASE_LENGTH_MAP[$this->baseIndente]);
        } else {
            $base_indente   = \str_repeat(static::INDENTE_CHAR, $this->indentLevel * $this->tabWidth);
        }

        foreach ($this->header as $idx => $cell) {
            $header     = \sprintf('%s%s', $cell, $this->buildRepart($cell, $idx));
            $stack[]    = \sprintf('%s%s', $base_indente, $this->trimEolSpace ? \rtrim($header, static::INDENTE_CHAR) : $header);
        }

        foreach ($this->rows as $row) {
            $message    = [];

            foreach (\array_values($row) as $idx => $cell) {
                if ($this->nullColumnSkip && !isset($this->notNullColumnMap[$idx])) {
                    continue;
                }
                $message[]  = \sprintf('%s%s', $cell, $this->buildRepart($cell, $idx));
            }
            $message    = \implode('', $message);
            $stack[]    = \sprintf('%s%s', $base_indente, $this->trimEolSpace ? \rtrim($message, static::INDENTE_CHAR) : $message);
        }

        return $stack;
    }

    /**
     * 事前ビルド状態を初期化します。
     *
     * @void
     */
    protected function initPreBuilding(): void
    {
        $this->preBuildeMaxWidthMap     = null;
        $this->preBuildeCellMaxWidthMap = null;
        $this->notNullColumnMap         = null;
    }

    /**
     * ビルダ名を設定します。
     *
     * @param   string  ビルダ名
     * @return self|static このインスタンス
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
