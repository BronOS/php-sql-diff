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


use BronOS\PhpEnum\ImmutableConstEnum;

/**
 * Diff type enum.
 *
 * @package   bronos\php-sql-diff
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 *
 * @method static $this NEW()
 * @method static $this DELETED()
 * @method static $this MODIFIED()
 */
class DiffTypeEnum extends ImmutableConstEnum
{
    public const NEW = 1;
    public const DELETED = 2;
    public const MODIFIED = 3;

    /**
     * Returns whether a diff is new (presents on the left side and absent on the right).
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isEqual(self::NEW);
    }

    /**
     * Returns whether a diff is deleted (presents on the right side and absent on the left).
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isEqual(self::DELETED);
    }

    /**
     * Returns whether a diff contains modifications (presents on both sides).
     *
     * @return bool
     */
    public function isModified(): bool
    {
        return $this->isEqual(self::MODIFIED);
    }
}