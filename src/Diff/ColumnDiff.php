<?php

/**
 * Php Sql Diff
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace BronOS\PhpSqlDiff\Diff;



use BronOS\PhpSqlSchema\Column\ColumnInterface;

/**
 * A representation of diff between sql columns.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class ColumnDiff extends AbstractDiff
{
    private ?ColumnInterface $sourceObject;
    private ?ColumnInterface $targetObject;
    private bool $isName;
    private bool $isType;
    private bool $isNullable;
    private bool $isDefault;
    private bool $isComment;
    private bool $isAutoincrement;
    private bool $isBinary;
    private bool $isCharset;
    private bool $isCollate;
    private bool $isPrecision;
    private bool $isScale;
    private bool $isDefaultTimestamp;
    private bool $isOnUpdateTimestamp;
    private bool $isOptions;
    private bool $isSize;
    private bool $isUnsigned;
    private bool $isZerofill;

    /**
     * ColumnDiff constructor.
     *
     * @param DiffTypeEnum         $diffType
     * @param ColumnInterface|null $sourceObject
     * @param ColumnInterface|null $targetObject
     * @param bool                 $isName
     * @param bool                 $isType
     * @param bool                 $isNullable
     * @param bool                 $isDefault
     * @param bool                 $isComment
     * @param bool                 $isAutoincrement
     * @param bool                 $isBinary
     * @param bool                 $isCharset
     * @param bool                 $isCollate
     * @param bool                 $isPrecision
     * @param bool                 $isScale
     * @param bool                 $isDefaultTimestamp
     * @param bool                 $isOnUpdateTimestamp
     * @param bool                 $isOptions
     * @param bool                 $isSize
     * @param bool                 $isUnsigned
     * @param bool                 $isZerofill
     */
    public function __construct(
        DiffTypeEnum $diffType,
        ?ColumnInterface $sourceObject = null,
        ?ColumnInterface $targetObject = null,
        bool $isName = false,
        bool $isType = false,
        bool $isNullable = false,
        bool $isDefault = false,
        bool $isComment = false,
        bool $isAutoincrement = false,
        bool $isBinary = false,
        bool $isCharset = false,
        bool $isCollate = false,
        bool $isPrecision = false,
        bool $isScale = false,
        bool $isDefaultTimestamp = false,
        bool $isOnUpdateTimestamp = false,
        bool $isOptions = false,
        bool $isSize = false,
        bool $isUnsigned = false,
        bool $isZerofill = false
    ) {
        parent::__construct($diffType);
        $this->sourceObject = $sourceObject;
        $this->targetObject = $targetObject;
        $this->isName = $isName;
        $this->isType = $isType;
        $this->isNullable = $isNullable;
        $this->isDefault = $isDefault;
        $this->isComment = $isComment;
        $this->isAutoincrement = $isAutoincrement;
        $this->isBinary = $isBinary;
        $this->isCharset = $isCharset;
        $this->isCollate = $isCollate;
        $this->isPrecision = $isPrecision;
        $this->isScale = $isScale;
        $this->isDefaultTimestamp = $isDefaultTimestamp;
        $this->isOnUpdateTimestamp = $isOnUpdateTimestamp;
        $this->isOptions = $isOptions;
        $this->isSize = $isSize;
        $this->isUnsigned = $isUnsigned;
        $this->isZerofill = $isZerofill;
    }

    /**
     * @return ColumnInterface|null
     */
    public function getSourceObject(): ?ColumnInterface
    {
        return $this->sourceObject;
    }

    /**
     * @return ColumnInterface|null
     */
    public function getTargetObject(): ?ColumnInterface
    {
        return $this->targetObject;
    }

    /**
     * @return bool
     */
    public function isName(): bool
    {
        return $this->isName;
    }

    /**
     * @return bool
     */
    public function isType(): bool
    {
        return $this->isType;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @return bool
     */
    public function isComment(): bool
    {
        return $this->isComment;
    }

    /**
     * @return bool
     */
    public function isAutoincrement(): bool
    {
        return $this->isAutoincrement;
    }

    /**
     * @return bool
     */
    public function isBinary(): bool
    {
        return $this->isBinary;
    }

    /**
     * @return bool
     */
    public function isCharset(): bool
    {
        return $this->isCharset;
    }

    /**
     * @return bool
     */
    public function isCollate(): bool
    {
        return $this->isCollate;
    }

    /**
     * @return bool
     */
    public function isPrecision(): bool
    {
        return $this->isPrecision;
    }

    /**
     * @return bool
     */
    public function isScale(): bool
    {
        return $this->isScale;
    }

    /**
     * @return bool
     */
    public function isDefaultTimestamp(): bool
    {
        return $this->isDefaultTimestamp;
    }

    /**
     * @return bool
     */
    public function isOnUpdateTimestamp(): bool
    {
        return $this->isOnUpdateTimestamp;
    }

    /**
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->isOptions;
    }

    /**
     * @return bool
     */
    public function isSize(): bool
    {
        return $this->isSize;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->isUnsigned;
    }

    /**
     * @return bool
     */
    public function isZerofill(): bool
    {
        return $this->isZerofill;
    }
}