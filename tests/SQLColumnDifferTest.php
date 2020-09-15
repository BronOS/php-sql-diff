<?php

namespace BronOS\PhpSqlDiscovery\Tests;


use BronOS\PhpSqlDiff\Diff\ColumnDiff;
use BronOS\PhpSqlDiff\Diff\DiffTypeEnum;
use BronOS\PhpSqlDiff\SQLColumnDiffer;
use BronOS\PhpSqlSchema\Column\Bool\BoolColumn;
use BronOS\PhpSqlSchema\Column\DateTime\DateTimeColumn;
use BronOS\PhpSqlSchema\Column\Numeric\FloatColumn;
use BronOS\PhpSqlSchema\Column\Numeric\IntColumn;
use BronOS\PhpSqlSchema\Column\Numeric\TinyIntColumn;
use BronOS\PhpSqlSchema\Column\String\EnumColumn;
use BronOS\PhpSqlSchema\Column\String\TextColumn;
use PHPUnit\Framework\TestCase;

class SQLColumnDifferTest extends TestCase
{
    public function testNoDiff()
    {
        $clm1 = new IntColumn(
            'id',
            11,
            true,
            true,
            false
        );
        $clm2 = new IntColumn(
            'id',
            11,
            true,
            true,
            false
        );

        $differ = new SQLColumnDiffer();

        $this->assertNull($differ->diff($clm1, $clm2));
    }

    public function testNoDiffBoolTinyInt()
    {
        $clm1 = new BoolColumn(
            'id',
            true,
            BoolColumn::NULL_KEYWORD
        );
        $clm2 = new TinyIntColumn(
            'id',
            1,
            false,
            false,
            true,
            TinyIntColumn::NULL_KEYWORD
        );

        $differ = new SQLColumnDiffer();

        $this->assertNull($differ->diff($clm1, $clm2));
    }

    public function testDiffBoolTinyInt()
    {
        $clm1 = new BoolColumn(
            't_bool',
        );
        $clm2 = new TinyIntColumn(
            't_bool',
            2,
        );

        $differ = new SQLColumnDiffer();

        $diff = $differ->diff($clm1, $clm2);

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(BoolColumn::class, $diff->getSourceObject());
        $this->assertInstanceOf(TinyIntColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertTrue($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertTrue($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertTrue($diff->isSize());
        $this->assertTrue($diff->isUnsigned());
        $this->assertTrue($diff->isZerofill());
    }

    public function testDiffSameType()
    {
        $clm1 = new IntColumn(
            'id',
            11,
            true,
            true,
            false
        );
        $clm2 = new IntColumn(
            'id',
            11,
            false,
            true,
            false
        );

        $differ = new SQLColumnDiffer();

        $diff = $differ->diff($clm1, $clm2);

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(IntColumn::class, $diff->getSourceObject());
        $this->assertInstanceOf(IntColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertTrue($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testDiffText()
    {
        $clm1 = new TextColumn(
            'txt',
            true,
            false,
            false,
            'char1',
            'char1_bin',
            'cmt1',
        );
        $clm2 = new TextColumn(
            'txt',
            false,
            false,
            false,
            'char2',
            'char2_bin',
            'cmt2',
        );

        $differ = new SQLColumnDiffer();

        $diff = $differ->diff($clm1, $clm2);

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(TextColumn::class, $diff->getSourceObject());
        $this->assertInstanceOf(TextColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertTrue($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertTrue($diff->isBinary());
        $this->assertTrue($diff->isCharset());
        $this->assertTrue($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testDiffDate()
    {
        $clm1 = new DateTimeColumn(
            'dt',
            true,
        );
        $clm2 = new DateTimeColumn(
            'dt',
            false,
        );

        $differ = new SQLColumnDiffer();

        $diff = $differ->diff($clm1, $clm2);

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(DateTimeColumn::class, $diff->getSourceObject());
        $this->assertInstanceOf(DateTimeColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertTrue($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testDiffFloat()
    {
        $clm1 = new FloatColumn(
            'flt',
        );
        $clm2 = new FloatColumn(
            'flt',
            10,
            2,
        );

        $differ = new SQLColumnDiffer();

        $diff = $differ->diff($clm1, $clm2);

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(FloatColumn::class, $diff->getSourceObject());
        $this->assertInstanceOf(FloatColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertTrue($diff->isPrecision());
        $this->assertTrue($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testDiffEnum()
    {
        $clm1 = new EnumColumn(
            'enm',
            ['x', 'y', 'z']
        );
        $clm2 = new EnumColumn(
            'enm',
            ['a', 'b', 'c']
        );

        $differ = new SQLColumnDiffer();

        $diff = $differ->diff($clm1, $clm2);

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(EnumColumn::class, $diff->getSourceObject());
        $this->assertInstanceOf(EnumColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertTrue($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testDeleted()
    {
        $clm1 = new IntColumn(
            'id',
            11,
            true,
            true,
            false
        );

        $differ = new SQLColumnDiffer();

        $diffList = $differ->hashDiff([], [$clm1->getName() => $clm1]);

        $this->assertCount(1, $diffList);

        $diff = $diffList[0];

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::DELETED, $diff->getDiffType()->getValue());
        $this->assertFalse($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertTrue($diff->getDiffType()->isDeleted());
        $this->assertNull($diff->getSourceObject());
        $this->assertInstanceOf(IntColumn::class, $diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testNew()
    {
        $clm1 = new IntColumn(
            'id',
            11,
            true,
            true,
            false
        );

        $differ = new SQLColumnDiffer();

        $diffList = $differ->hashDiff([$clm1->getName() => $clm1], []);

        $this->assertCount(1, $diffList);

        $diff = $diffList[0];

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::NEW, $diff->getDiffType()->getValue());
        $this->assertFalse($diff->getDiffType()->isModified());
        $this->assertTrue($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(IntColumn::class, $diff->getSourceObject());
        $this->assertNull($diff->getTargetObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertFalse($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }

    public function testModified()
    {
        $clm1 = new IntColumn(
            'id',
            11,
            true,
            true,
            false
        );
        $clm2 = new IntColumn(
            'id',
            10,
            true,
            true,
            false
        );

        $differ = new SQLColumnDiffer();

        $diffList = $differ->hashDiff([$clm1->getName() => $clm1], [$clm2->getName() => $clm2]);

        $this->assertCount(1, $diffList);

        $diff = $diffList[0];

        $this->assertNotNull($diff);
        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->getDiffType()->isModified());
        $this->assertFalse($diff->getDiffType()->isNew());
        $this->assertFalse($diff->getDiffType()->isDeleted());
        $this->assertInstanceOf(IntColumn::class, $diff->getTargetObject());
        $this->assertInstanceOf(IntColumn::class, $diff->getSourceObject());

        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertFalse($diff->isNullable());
        $this->assertFalse($diff->isDefault());
        $this->assertFalse($diff->isComment());
        $this->assertFalse($diff->isAutoincrement());
        $this->assertFalse($diff->isBinary());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isPrecision());
        $this->assertFalse($diff->isScale());
        $this->assertFalse($diff->isDefaultTimestamp());
        $this->assertFalse($diff->isOnUpdateTimestamp());
        $this->assertFalse($diff->isOptions());
        $this->assertTrue($diff->isSize());
        $this->assertFalse($diff->isUnsigned());
        $this->assertFalse($diff->isZerofill());
    }
}
