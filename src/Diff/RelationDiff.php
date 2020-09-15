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


use BronOS\PhpSqlSchema\Relation\ForeignKeyInterface;

/**
 * A representation of diff between sql relations/foreign keys.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class RelationDiff extends AbstractDiff
{
    private ?ForeignKeyInterface $sourceObject;
    private ?ForeignKeyInterface $targetObject;
    private bool $isName;
    private bool $isSourceField;
    private bool $isTargetTable;
    private bool $isTargetField;
    private bool $isOnDeleteAction;
    private bool $isOnUpdateAction;

    /**
     * SQLRelationDiff constructor.
     *
     * @param DiffTypeEnum             $diffType
     * @param ForeignKeyInterface|null $sourceObject
     * @param ForeignKeyInterface|null $targetObject
     * @param bool                     $isName
     * @param bool                     $isSourceField
     * @param bool                     $isTargetTable
     * @param bool                     $isTargetField
     * @param bool                     $isOnDeleteAction
     * @param bool                     $isOnUpdateAction
     */
    public function __construct(
        DiffTypeEnum $diffType,
        ?ForeignKeyInterface $sourceObject = null,
        ?ForeignKeyInterface $targetObject = null,
        bool $isName = false,
        bool $isSourceField = false,
        bool $isTargetTable = false,
        bool $isTargetField = false,
        bool $isOnDeleteAction = false,
        bool $isOnUpdateAction = false
    ) {
        parent::__construct($diffType);

        $this->sourceObject = $sourceObject;
        $this->targetObject = $targetObject;
        $this->isName = $isName;
        $this->isSourceField = $isSourceField;
        $this->isTargetTable = $isTargetTable;
        $this->isTargetField = $isTargetField;
        $this->isOnDeleteAction = $isOnDeleteAction;
        $this->isOnUpdateAction = $isOnUpdateAction;
    }

    /**
     * Returns source value if any.
     *
     * @return ForeignKeyInterface|null
     */
    public function getSourceObject(): ?ForeignKeyInterface
    {
        return $this->sourceObject;
    }

    /**
     * Returns target object if any.
     *
     * @return ForeignKeyInterface|null
     */
    public function getTargetObject(): ?ForeignKeyInterface
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
    public function isSourceField(): bool
    {
        return $this->isSourceField;
    }

    /**
     * @return bool
     */
    public function isTargetTable(): bool
    {
        return $this->isTargetTable;
    }

    /**
     * @return bool
     */
    public function isTargetField(): bool
    {
        return $this->isTargetField;
    }

    /**
     * @return bool
     */
    public function isOnDeleteAction(): bool
    {
        return $this->isOnDeleteAction;
    }

    /**
     * @return bool
     */
    public function isOnUpdateAction(): bool
    {
        return $this->isOnUpdateAction;
    }
}