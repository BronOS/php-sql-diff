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
use BronOS\PhpSqlDiff\Diff\IndexDiff;
use BronOS\PhpSqlSchema\Column\Attribute\AutoincrementColumnAttributeInterface;
use BronOS\PhpSqlSchema\Exception\ColumnNotFoundException;
use BronOS\PhpSqlSchema\Exception\IndexNotFoundException;
use BronOS\PhpSqlSchema\Exception\RelationNotFoundException;
use BronOS\PhpSqlSchema\Index\IndexInterface;
use BronOS\PhpSqlSchema\Index\PrimaryKey;
use BronOS\PhpSqlSchema\Relation\ForeignKeyInterface;
use BronOS\PhpSqlSchema\SQLTableSchemaInterface;

/**
 * SQL table schema differ.
 * Responsible for comparison of sql indexes and finding a diff between of them.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class SQLIndexDiffer implements SQLIndexDifferInterface
{
    /**
     * Finds a diff between passed sql indexes.
     *
     * @param IndexInterface $index1
     * @param IndexInterface $index2
     *
     * @return IndexDiff|null
     */
    public function diff(IndexInterface $index1, IndexInterface $index2): ?IndexDiff
    {
        $isName = $index1->getName() != $index2->getName();
        $isType = $index1->getType() != $index2->getType();
        $isFields = count(array_diff($index1->getFields(), $index2->getFields())) != 0;

        if (!$isName && !$isType && !$isFields) {
            return null;
        }

        return new IndexDiff(
            DiffTypeEnum::MODIFIED(),
            $index1,
            $index2,
            $isName,
            $isType,
            $isFields
        );
    }

    /**
     * @param IndexInterface          $idx
     * @param SQLTableSchemaInterface $schema
     *
     * @return bool
     */
    private function isAutoincrement(IndexInterface $idx, SQLTableSchemaInterface $schema): bool
    {
        if ($idx instanceof PrimaryKey && count($idx->getFields()) == 1) {
            try {
                if ($schema->getColumn($idx->getFields()[0]) instanceof AutoincrementColumnAttributeInterface) {
                    return true;
                }
            } catch (ColumnNotFoundException $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * @param IndexInterface          $idx
     * @param SQLTableSchemaInterface $schema
     *
     * @return bool
     */
    private function isRelation(IndexInterface $idx, SQLTableSchemaInterface $schema): bool
    {
        try {
            $schema->getRelation($idx->getName());
            return true;
        } catch (RelationNotFoundException $e) {
            return false;
        }
    }

    /**
     * Finds a diff between passed sql index's hashes.
     *
     * @param SQLTableSchemaInterface $schema1
     * @param SQLTableSchemaInterface $schema2
     *
     * @return IndexDiff[]
     */
    public function hashDiff(SQLTableSchemaInterface $schema1, SQLTableSchemaInterface $schema2): array
    {
        $diffList = [];
        $processed = [];

        foreach ($schema1->getIndexes() as $idx1) {
            $processed[] = $idx1->getName();

            try {
                // try to find same index in schema2
                $idx2 = $schema2->getIndex($idx1->getName());
            } catch (IndexNotFoundException $e) {
                // special case: autoincrement
                // special case: foreign key is declared but index for it is not
                if (!$this->isAutoincrement($idx1, $schema1) && !$this->isRelation($idx1, $schema1)) {
                    // new
                    $diffList[] = new IndexDiff(
                        DiffTypeEnum::NEW(),
                        $idx1
                    );
                }

                continue;
            }

            // modified
            $diff = $this->diff($idx1, $idx2);
            if (!is_null($diff)) {
                $diffList[] = $diff;
            }
        }

        // deleted
        foreach ($schema2->getIndexes() as $idx2) {
            if (!in_array($idx2->getName(), $processed)) {
                // special case: autoincrement
                // special case: foreign key is declared but index for it is not
                if (!$this->isAutoincrement($idx2, $schema2) && !$this->isRelation($idx2, $schema2)) {
                    $diffList[] = new IndexDiff(
                        DiffTypeEnum::DELETED(),
                        null,
                        $idx2
                    );
                }
            }
        }

        return $diffList;
    }
}