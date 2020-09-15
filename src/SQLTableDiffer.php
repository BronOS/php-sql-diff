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


use BronOS\PhpSqlDiff\Diff\DiffTypeEnum;
use BronOS\PhpSqlDiff\Diff\TableDiff;
use BronOS\PhpSqlSchema\SQLTableSchemaInterface;

/**
 * SQL table schema differ.
 * Responsible for comparison of sql table schemas and finding a diff between of them.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class SQLTableDiffer implements SQLTableDifferInterface
{
    private SQLColumnDifferInterface $columnDiffer;
    private SQLRelationDifferInterface $relationDiffer;
    private SQLIndexDifferInterface $indexDiffer;

    /**
     * SQLTableDiffer constructor.
     *
     * @param SQLColumnDifferInterface   $columnDiffer
     * @param SQLRelationDifferInterface $relationDiffer
     * @param SQLIndexDifferInterface    $indexDiffer
     */
    public function __construct(
        SQLColumnDifferInterface $columnDiffer,
        SQLRelationDifferInterface $relationDiffer,
        SQLIndexDifferInterface $indexDiffer
    ) {
        $this->columnDiffer = $columnDiffer;
        $this->relationDiffer = $relationDiffer;
        $this->indexDiffer = $indexDiffer;
    }

    /**
     * Finds a diff between passed sql table schemas.
     *
     * @param SQLTableSchemaInterface $schema1
     * @param SQLTableSchemaInterface $schema2
     *
     * @return TableDiff|null
     */
    public function diff(SQLTableSchemaInterface $schema1, SQLTableSchemaInterface $schema2): ?TableDiff
    {
        $isName = $schema1->getName() != $schema2->getName();
        $isEngine = $schema1->getEngine() != $schema2->getEngine();
        $isCharset = $schema1->getDefaultCharset() != $schema2->getDefaultCharset();
        $isCollation = $schema1->getCollate() != $schema2->getCollate();
        $columns = $this->columnDiffer->hashDiff($schema1->getColumns(), $schema2->getColumns());
        $indexes = $this->indexDiffer->hashDiff($schema1, $schema2);
        $relations = $this->relationDiffer->hashDiff($schema1->getRelations(), $schema2->getRelations());

        if (!$isName
            && !$isEngine
            && !$isCharset
            && !$isCollation
            && count($columns) == 0
            && count($indexes) == 0
            && count($relations) == 0
        ) {
            return null;
        }

        return new TableDiff(
            DiffTypeEnum::MODIFIED(),
            $schema1,
            $schema2,
            $isName,
            $isCharset,
            $isCollation,
            $columns,
            $indexes,
            $relations
        );
    }

    /**
     * Finds a diff between passed sql table's hashes.
     *
     * @param SQLTableSchemaInterface[] $hash1
     * @param SQLTableSchemaInterface[] $hash2
     *
     * @return TableDiff[]
     */
    public function hashDiff(array $hash1, array $hash2): array
    {
        $diffList = [];
        $processed = [];

        foreach ($hash1 as $tbl1) {
            $processed[] = $tbl1->getName();

            // new
            if (!isset($hash2[$tbl1->getName()])) {
                $diffList[] = new TableDiff(
                    DiffTypeEnum::NEW(),
                    $tbl1
                );
                continue;
            }

            // modified
            $diff = $this->diff($tbl1, $hash2[$tbl1->getName()]);
            if (!is_null($diff)) {
                $diffList[] = $diff;
            }
        }

        // deleted
        foreach ($hash2 as $tbl2) {
            if (!in_array($tbl2->getName(), $processed)) {
                $diffList[] = new TableDiff(
                    DiffTypeEnum::DELETED(),
                    null,
                    $tbl2
                );
            }
        }

        return $diffList;
    }
}