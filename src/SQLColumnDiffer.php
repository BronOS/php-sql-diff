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

namespace BronOS\PhpSqlDiff;


use BronOS\PhpSqlDiff\Diff\ColumnDiff;
use BronOS\PhpSqlDiff\Diff\DiffTypeEnum;
use BronOS\PhpSqlSchema\Column\Attribute\AutoincrementColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\BinaryColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\CharsetColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\CollateColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\DecimalSizeColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\DefaultTimestampColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\FloatSizeColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\OnUpdateTimestampColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\OptionsColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\SizeColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\UnsignedColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Attribute\ZerofillColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\Bool\BoolColumnInterface;
use BronOS\PhpSqlSchema\Column\ColumnInterface;
use BronOS\PhpSqlSchema\Column\Numeric\TinyIntColumnInterface;

/**
 * SQL column differ.
 * Responsible for comparison of sql columns and finding a diff between of them.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class SQLColumnDiffer implements SQLColumnDifferInterface
{
    /**
     * Finds a diff between passed sql columns.
     *
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return ColumnDiff|null
     */
    public function diff(ColumnInterface $column1, ColumnInterface $column2): ?ColumnDiff
    {
        $isName = $column1->getName() != $column2->getName();
        $isType = $this->isType($column1, $column2);
        $isNullable = $column1->isNullable() != $column2->isNullable();
        $isDefault = $column1->getDefault() != $column2->getDefault();
        $isComment = $column1->getComment() != $column2->getComment();
        $isAutoincrement = $this->isAutoincrement($column1, $column2);
        $isBinary = $this->isBinary($column1, $column2);
        $isCharset = $this->isCharset($column1, $column2);
        $isCollate = $this->isCollate($column1, $column2);
        $isPrecision = $this->isPrecision($column1, $column2);
        $isScale = $this->isScale($column1, $column2);
        $isDefaultTimestamp = $this->isDefaultTimestamp($column1, $column2);
        $isOnUpdateTimestamp = $this->isOnUpdateTimestamp($column1, $column2);
        $isOptions = $this->isOptions($column1, $column2);
        $isSize = $this->isSize($column1, $column2);
        $isUnsigned = $this->isUnsigned($column1, $column2);
        $isZerofill = $this->isZerofill($column1, $column2);

        if ($isName
            || $isType
            || $isNullable
            || $isDefault
            || $isComment
            || $isAutoincrement
            || $isBinary
            || $isCharset
            || $isCollate
            || $isPrecision
            || $isScale
            || $isDefaultTimestamp
            || $isOnUpdateTimestamp
            || $isOptions
            || $isSize
            || $isUnsigned
            || $isZerofill
        ) {
            return new ColumnDiff(
                DiffTypeEnum::MODIFIED(),
                $column1,
                $column2,
                $isName,
                $isType,
                $isNullable,
                $isDefault,
                $isComment,
                $isAutoincrement,
                $isBinary,
                $isCharset,
                $isCollate,
                $isPrecision,
                $isScale,
                $isDefaultTimestamp,
                $isOnUpdateTimestamp,
                $isOptions,
                $isSize,
                $isUnsigned,
                $isZerofill
            );
        }

        return null;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isType(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        if ($this->compareBool($column1, $column2)) {
            return false;
        }

        return $column1->getType() !== $column2->getType();
    }

    /**
     * @param TinyIntColumnInterface $column
     *
     * @return bool
     */
    private function isBool(TinyIntColumnInterface $column): bool
    {
        return $column->getSize() == 1
            && !$column->isZerofill()
            && !$column->isUnsigned()
            && !$column->isAutoincrement();
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function compareBool(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        if (($column1 instanceof BoolColumnInterface
                && $column2 instanceof TinyIntColumnInterface
                && $this->isBool($column2))
            || ($column2 instanceof BoolColumnInterface
                && $column1 instanceof TinyIntColumnInterface
                && $this->isBool($column1))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isAutoincrement(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        if ($this->compareBool($column1, $column2)) {
            return false;
        }

        $res1 = null;
        $res2 = null;

        if ($column1 instanceof AutoincrementColumnAttributeInterface) {
            $res1 = $column1->isAutoincrement();
        }

        if ($column2 instanceof AutoincrementColumnAttributeInterface) {
            $res2 = $column2->isAutoincrement();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isBinary(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof BinaryColumnAttributeInterface) {
            $res1 = $column1->isBinary();
        }

        if ($column2 instanceof BinaryColumnAttributeInterface) {
            $res2 = $column2->isBinary();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isCharset(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof CharsetColumnAttributeInterface) {
            $res1 = $column1->getCharset();
        }

        if ($column2 instanceof CharsetColumnAttributeInterface) {
            $res2 = $column2->getCharset();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isCollate(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof CollateColumnAttributeInterface) {
            $res1 = $column1->getCollate();
        }

        if ($column2 instanceof CollateColumnAttributeInterface) {
            $res2 = $column2->getCollate();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isSize(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        if ($this->compareBool($column1, $column2)) {
            return false;
        }

        $res1 = null;
        $res2 = null;

        if ($column1 instanceof SizeColumnAttributeInterface) {
            $res1 = $column1->getSize();
        }

        if ($column2 instanceof SizeColumnAttributeInterface) {
            $res2 = $column2->getSize();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isDefaultTimestamp(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof DefaultTimestampColumnAttributeInterface) {
            $res1 = $column1->isDefaultTimestamp();
        }

        if ($column2 instanceof DefaultTimestampColumnAttributeInterface) {
            $res2 = $column2->isDefaultTimestamp();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isOnUpdateTimestamp(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof OnUpdateTimestampColumnAttributeInterface) {
            $res1 = $column1->isOnUpdateTimestamp();
        }

        if ($column2 instanceof OnUpdateTimestampColumnAttributeInterface) {
            $res2 = $column2->isOnUpdateTimestamp();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isUnsigned(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        if ($this->compareBool($column1, $column2)) {
            return false;
        }

        $res1 = null;
        $res2 = null;

        if ($column1 instanceof UnsignedColumnAttributeInterface) {
            $res1 = $column1->isUnsigned();
        }

        if ($column2 instanceof UnsignedColumnAttributeInterface) {
            $res2 = $column2->isUnsigned();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isZerofill(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        if ($this->compareBool($column1, $column2)) {
            return false;
        }

        $res1 = null;
        $res2 = null;

        if ($column1 instanceof ZerofillColumnAttributeInterface) {
            $res1 = $column1->isZerofill();
        }

        if ($column2 instanceof ZerofillColumnAttributeInterface) {
            $res2 = $column2->isZerofill();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isOptions(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof OptionsColumnAttributeInterface) {
            $res1 = $column1->getOptions();
        }

        if ($column2 instanceof OptionsColumnAttributeInterface) {
            $res2 = $column2->getOptions();
        }

        if (!is_array($res1) || !is_array($res2)) {
            return $res1 !== $res2;
        }

        return count(array_diff($res1, $res2)) != 0;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isPrecision(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof DecimalSizeColumnAttributeInterface
            || $column1 instanceof FloatSizeColumnAttributeInterface
        ) {
            $res1 = $column1->getPrecision();
        }

        if ($column2 instanceof DecimalSizeColumnAttributeInterface
            || $column2 instanceof FloatSizeColumnAttributeInterface
        ) {
            $res2 = $column2->getPrecision();
        }

        return $res1 !== $res2;
    }

    /**
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     *
     * @return bool
     */
    private function isScale(ColumnInterface $column1, ColumnInterface $column2): bool
    {
        $res1 = null;
        $res2 = null;

        if ($column1 instanceof DecimalSizeColumnAttributeInterface
            || $column1 instanceof FloatSizeColumnAttributeInterface
        ) {
            $res1 = $column1->getScale();
        }

        if ($column2 instanceof DecimalSizeColumnAttributeInterface
            || $column2 instanceof FloatSizeColumnAttributeInterface
        ) {
            $res2 = $column2->getScale();
        }

        return $res1 !== $res2;
    }

    /**
     * Finds a diff between passed sql column's hashes.
     *
     * @param ColumnInterface[] $hash1
     * @param ColumnInterface[] $hash2
     *
     * @return ColumnDiff[]
     */
    public function hashDiff(array $hash1, array $hash2): array
    {
        $diffList = [];
        $processed = [];

        foreach ($hash1 as $idx1) {
            $processed[] = $idx1->getName();

            // new
            if (!isset($hash2[$idx1->getName()])) {
                $diffList[] = new ColumnDiff(
                    DiffTypeEnum::NEW(),
                    $idx1
                );
                continue;
            }

            // modified
            $diff = $this->diff($idx1, $hash2[$idx1->getName()]);
            if (!is_null($diff)) {
                $diffList[] = $diff;
            }
        }

        // deleted
        foreach ($hash2 as $idx2) {
            if (!in_array($idx2->getName(), $processed)) {
                $diffList[] = new ColumnDiff(
                    DiffTypeEnum::DELETED(),
                    null,
                    $idx2
                );
            }
        }

        return $diffList;
    }
}