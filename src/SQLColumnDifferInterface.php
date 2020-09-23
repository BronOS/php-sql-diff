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
use BronOS\PhpSqlSchema\Column\ColumnInterface;

/**
 * SQL column differ.
 * Responsible for comparison of sql columns and finding a diff between of them.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
interface SQLColumnDifferInterface
{
    /**
     * Finds a diff between passed sql columns.
     *
     * @param ColumnInterface $column1
     * @param ColumnInterface $column2
     * @param string          $defaultCharset
     * @param string          $defaultCollation
     *
     * @return ColumnDiff|null
     */
    public function diff(
        ColumnInterface $column1,
        ColumnInterface $column2,
        string $defaultCharset,
        string $defaultCollation
    ): ?ColumnDiff;

    /**
     * Finds a diff between passed sql column's hashes.
     *
     * @param ColumnInterface[] $hash1
     * @param ColumnInterface[] $hash2
     * @param string            $defaultCharset
     * @param string            $defaultCollation
     *
     * @return ColumnDiff[]
     */
    public function hashDiff(
        array $hash1,
        array $hash2,
        string $defaultCharset,
        string $defaultCollation
    ): array;
}