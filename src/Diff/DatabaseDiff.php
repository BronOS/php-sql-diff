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


use BronOS\PhpSqlSchema\SQLDatabaseSchemaInterface;

/**
 * A representation of diff between sql databases.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class DatabaseDiff extends AbstractDiff
{
    private ?SQLDatabaseSchemaInterface $sourceObject;
    private ?SQLDatabaseSchemaInterface $targetObject;
    private bool $isName;
    private bool $isDefaultEngine;
    private bool $isDefaultCharset;
    private bool $isDefaultCollate;
    private array $tableDiffs = [];

    /**
     * AbstractSQLSchemaDiff constructor.
     *
     * @param DiffTypeEnum                    $diffType
     * @param SQLDatabaseSchemaInterface|null $sourceObject
     * @param SQLDatabaseSchemaInterface|null $targetObject
     * @param bool                            $isName
     * @param bool                            $isDefaultEngine
     * @param bool                            $isDefaultCharset
     * @param bool                            $isDefaultCollate
     * @param array                           $tableDiffs
     */
    public function __construct(
        DiffTypeEnum $diffType,
        ?SQLDatabaseSchemaInterface $sourceObject = null,
        ?SQLDatabaseSchemaInterface $targetObject = null,
        bool $isName = false,
        bool $isDefaultEngine = false,
        bool $isDefaultCharset = false,
        bool $isDefaultCollate = false,
        array $tableDiffs = []
    ) {
        parent::__construct($diffType);

        $this->sourceObject = $sourceObject;
        $this->targetObject = $targetObject;
        $this->isName = $isName;
        $this->isDefaultEngine = $isDefaultEngine;
        $this->isDefaultCharset = $isDefaultCharset;
        $this->isDefaultCollate = $isDefaultCollate;
        $this->tableDiffs = $tableDiffs;
    }

    /**
     * @return SQLDatabaseSchemaInterface|null
     */
    public function getSourceObject(): ?SQLDatabaseSchemaInterface
    {
        return $this->sourceObject;
    }

    /**
     * @return SQLDatabaseSchemaInterface|null
     */
    public function getTargetObject(): ?SQLDatabaseSchemaInterface
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
    public function isDefaultEngine(): bool
    {
        return $this->isDefaultEngine;
    }

    /**
     * @return bool
     */
    public function isDefaultCharset(): bool
    {
        return $this->isDefaultCharset;
    }

    /**
     * @return bool
     */
    public function isDefaultCollate(): bool
    {
        return $this->isDefaultCollate;
    }

    /**
     * @return TableDiff[]
     */
    public function getTableDiffs(): array
    {
        return $this->tableDiffs;
    }

    /**
     * @return bool
     */
    public function isTables(): bool
    {
        return count($this->getTableDiffs()) > 0;
    }
}