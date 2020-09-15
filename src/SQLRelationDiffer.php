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
use BronOS\PhpSqlDiff\Diff\RelationDiff;
use BronOS\PhpSqlSchema\Relation\Action\ActionInterface;
use BronOS\PhpSqlSchema\Relation\Action\RestrictActionInterface;
use BronOS\PhpSqlSchema\Relation\ForeignKeyInterface;

/**
 * SQL relation differ.
 * Responsible for comparison of sql relations and finding a diff between of them.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class SQLRelationDiffer implements SQLRelationDifferInterface
{
    /**
     * Finds a diff between passed sql relation's hashes.
     *
     * @param ForeignKeyInterface[] $hash1
     * @param ForeignKeyInterface[] $hash2
     *
     * @return RelationDiff[]
     */
    public function hashDiff(array $hash1, array $hash2): array
    {
        $diffList = [];
        $processed = [];

        foreach ($hash1 as $rel1) {
            $processed[] = $rel1->getName();

            // new
            if (!isset($hash2[$rel1->getName()])) {
                $diffList[] = new RelationDiff(
                    DiffTypeEnum::NEW(),
                    $rel1
                );
                continue;
            }

            // modified
            $diff = $this->diff($rel1, $hash2[$rel1->getName()]);
            if (!is_null($diff)) {
                $diffList[] = $diff;
            }
        }

        // deleted
        foreach ($hash2 as $rel2) {
            if (!in_array($rel2->getName(), $processed)) {
                $diffList[] = new RelationDiff(
                    DiffTypeEnum::DELETED(),
                    null,
                    $rel2
                );
            }
        }

        return $diffList;
    }

    /**
     * Finds a diff between passed sql relations.
     *
     * @param ForeignKeyInterface $relation1
     * @param ForeignKeyInterface $relation2
     *
     * @return RelationDiff|null
     */
    public function diff(ForeignKeyInterface $relation1, ForeignKeyInterface $relation2): ?RelationDiff
    {
        $isName = $relation1->getName() != $relation2->getName();
        $isSourceField = $relation1->getSourceField() != $relation2->getSourceField();
        $isTargetTable = $relation1->getTargetTable() != $relation2->getTargetTable();
        $isTargetField = $relation1->getTargetField() != $relation2->getTargetField();
        $isOnDelete = $this->compareActions($relation1->getOnDeleteAction(), $relation2->getOnDeleteAction());
        $isOnUpdate = $this->compareActions($relation1->getOnUpdateAction(), $relation2->getOnUpdateAction());

        if (!$this->isModified($isName, $isSourceField, $isTargetField, $isTargetTable, $isOnDelete, $isOnUpdate)) {
            return null;
        }

        return new RelationDiff(
            DiffTypeEnum::MODIFIED(),
            $relation1,
            $relation2,
            $isName,
            $isSourceField,
            $isTargetTable,
            $isTargetField,
            $isOnDelete,
            $isOnUpdate
        );
    }

    /**
     * @param bool $isName
     * @param bool $isSourceField
     * @param bool $isTargetField
     * @param bool $isTargetTable
     * @param bool $isOnDelete
     * @param bool $isOnUpdate
     *
     * @return bool
     */
    private function isModified(
        bool $isName,
        bool $isSourceField,
        bool $isTargetField,
        bool $isTargetTable,
        bool $isOnDelete,
        bool $isOnUpdate
    ): bool {
        return in_array(
            true,
            [
                $isName,
                $isSourceField,
                $isTargetField,
                $isTargetTable,
                $isOnDelete,
                $isOnUpdate,
            ]
        );
    }

    /**
     * @param ActionInterface|null $action1
     * @param ActionInterface|null $action2
     *
     * @return bool
     */
    private function compareActions(?ActionInterface $action1, ?ActionInterface $action2): bool
    {
        $a1 = null;
        $a2 = null;

        if (!is_null($action1)) {
            $a1 = $action1->getKeyword() == RestrictActionInterface::SQL_KEYWORD ? null : $action1->getKeyword();
        }

        if (!is_null($action2)) {
            $a2 = $action2->getKeyword() == RestrictActionInterface::SQL_KEYWORD ? null : $action2->getKeyword();
        }

        return $a1 != $a2;
    }
}