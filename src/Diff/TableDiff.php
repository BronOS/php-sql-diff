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


use BronOS\PhpSqlSchema\SQLTableSchemaInterface;

/**
 * A representation of diff between sql tables.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class TableDiff extends AbstractDiff
{
    private ?SQLTableSchemaInterface $sourceObject;
    private ?SQLTableSchemaInterface $targetObject;
    private bool $isName;
    private bool $isCharset;
    private bool $isCollate;
    private array $columns = [];
    private array $indexes = [];
    private array $relations = [];
    private bool $isEngine;

    /**
     * TableDiff constructor.
     *
     * @param DiffTypeEnum                 $diffType
     * @param SQLTableSchemaInterface|null $sourceObject
     * @param SQLTableSchemaInterface|null $targetObject
     * @param bool                         $isName
     * @param bool                         $isEngine
     * @param bool                         $isCharset
     * @param bool                         $isCollate
     * @param array                        $columns
     * @param array                        $indexes
     * @param array                        $relations
     */
    public function __construct(
        DiffTypeEnum $diffType,
        ?SQLTableSchemaInterface $sourceObject = null,
        ?SQLTableSchemaInterface $targetObject = null,
        bool $isName = false,
        bool $isEngine = false,
        bool $isCharset = false,
        bool $isCollate = false,
        array $columns = [],
        array $indexes = [],
        array $relations = []
    ) {
        parent::__construct($diffType);

        $this->sourceObject = $sourceObject;
        $this->targetObject = $targetObject;
        $this->isName = $isName;
        $this->isEngine = $isEngine;
        $this->isCharset = $isCharset;
        $this->isCollate = $isCollate;
        $this->columns = $columns;
        $this->indexes = $indexes;
        $this->relations = $relations;
    }

    /**
     * @return SQLTableSchemaInterface|null
     */
    public function getSourceObject(): ?SQLTableSchemaInterface
    {
        return $this->sourceObject;
    }

    /**
     * @return SQLTableSchemaInterface|null
     */
    public function getTargetObject(): ?SQLTableSchemaInterface
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
    public function isEngine(): bool
    {
        return $this->isEngine;
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
    public function isColumns(): bool
    {
        return count($this->getColumns()) > 0;
    }

    /**
     * @return bool
     */
    public function isRelations(): bool
    {
        return count($this->getRelations()) > 0;
    }

    /**
     * @return bool
     */
    public function isIndexes(): bool
    {
        return count($this->getIndexes()) > 0;
    }

    /**
     * @return ColumnDiff[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return IndexDiff[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @return RelationDiff[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}