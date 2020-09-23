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


use BronOS\PhpSqlDiff\Diff\DatabaseDiff;
use BronOS\PhpSqlDiff\Diff\TableDiff;
use BronOS\PhpSqlSchema\SQLDatabaseSchemaInterface;
use BronOS\PhpSqlSchema\SQLTableSchemaInterface;

/**
 * SQL database schema differ.
 * Responsible for comparison of sql database schemas and finding a diff between of them.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
interface SQLDatabaseDifferInterface
{
    /**
     * Finds a diff between passed sql database schemas.
     *
     * @param SQLDatabaseSchemaInterface $schema1
     * @param SQLDatabaseSchemaInterface $schema2
     * @param string                     $defaultEngine
     * @param string                     $defaultCharset
     * @param string                     $defaultCollation
     *
     * @return DatabaseDiff|null
     */
    public function diff(
        SQLDatabaseSchemaInterface $schema1,
        SQLDatabaseSchemaInterface $schema2,
        string $defaultEngine,
        string $defaultCharset,
        string $defaultCollation
    ): ?DatabaseDiff;
}