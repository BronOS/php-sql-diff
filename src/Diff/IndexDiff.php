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


use BronOS\PhpSqlSchema\Index\IndexInterface;

/**
 * A representation of diff between sql indexes.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class IndexDiff extends AbstractDiff
{
    private ?IndexInterface $sourceObject;
    private ?IndexInterface $targetObject;
    private bool $isName;
    private bool $isType;
    private bool $isFields;

    /**
     * AbstractSQLSchemaDiff constructor.
     *
     * @param DiffTypeEnum        $diffType
     * @param IndexInterface|null $sourceObject
     * @param IndexInterface|null $targetObject
     * @param bool                $isName
     * @param bool                $isType
     * @param bool                $isFields
     */
    public function __construct(
        DiffTypeEnum $diffType,
        ?IndexInterface $sourceObject = null,
        ?IndexInterface $targetObject = null,
        bool $isName = false,
        bool $isType = false,
        bool $isFields = false
    ) {
        parent::__construct($diffType);

        $this->sourceObject = $sourceObject;
        $this->targetObject = $targetObject;
        $this->isName = $isName;
        $this->isType = $isType;
        $this->isFields = $isFields;
    }

    /**
     * @return IndexInterface|null
     */
    public function getSourceObject(): ?IndexInterface
    {
        return $this->sourceObject;
    }

    /**
     * @return IndexInterface|null
     */
    public function getTargetObject(): ?IndexInterface
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
    public function isFields(): bool
    {
        return $this->isFields;
    }
}