<?php

namespace BronOS\PhpSqlDiscovery\Tests;


use BronOS\PhpSqlDiff\Diff\DiffTypeEnum;
use BronOS\PhpSqlDiff\SQLIndexDiffer;
use BronOS\PhpSqlSchema\Index\IndexInterface;
use BronOS\PhpSqlSchema\Index\Key;
use BronOS\PhpSqlSchema\Index\PrimaryKey;
use BronOS\PhpSqlSchema\Index\UniqueKey;
use BronOS\PhpSqlSchema\SQLTableSchema;
use PHPUnit\Framework\TestCase;

class SQLIndexDifferTest extends TestCase
{
    public function testNoDiff()
    {
        $idx1 = new PrimaryKey(
            ['f1', 'f2']
        );
        $idx2 = new PrimaryKey(
            ['f2', 'f1']
        );

        $differ = new SQLIndexDiffer();

        $this->assertNull($differ->diff($idx1, $idx2));
    }

    public function testDiffFields()
    {
        $idx1 = new PrimaryKey(
            ['f1', 'f2']
        );
        $idx2 = new PrimaryKey(
            ['f3', 'f2']
        );

        $differ = new SQLIndexDiffer();

        $diff = $differ->diff($idx1, $idx2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertInstanceOf(IndexInterface::class, $diff->getSourceObject());
        $this->assertInstanceOf(IndexInterface::class, $diff->getTargetObject());
        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isType());
        $this->assertTrue($diff->isFields());
    }

    public function testDiffType()
    {
        $idx1 = new UniqueKey(
            ['f1', 'f2'],
            'idx'
        );
        $idx2 = new Key(
            ['f2', 'f1'],
            'idx'
        );

        $differ = new SQLIndexDiffer();

        $diff = $differ->diff($idx1, $idx2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertInstanceOf(IndexInterface::class, $diff->getSourceObject());
        $this->assertInstanceOf(IndexInterface::class, $diff->getTargetObject());
        $this->assertFalse($diff->isName());
        $this->assertTrue($diff->isType());
        $this->assertFalse($diff->isFields());
    }

    public function testHashDiff()
    {
        $schema1 = new SQLTableSchema(
            's1',
            [],
            [
                new UniqueKey(
                    ['f1', 'f2'],
                ),
                new Key(
                    ['f1'],
                ),
                new PrimaryKey(
                    ['f1'],
                ),
            ]
        );

        $schema2 = new SQLTableSchema(
            's1',
            [],
            [
                new UniqueKey(
                    ['f1', 'f2'],
                ),
                new Key(
                    ['f2'],
                ),
                new PrimaryKey(
                    ['f2'],
                ),
            ]
        );

        $differ = new SQLIndexDiffer();

        $diffList = $differ->hashDiff($schema1, $schema2);

        $this->assertCount(3, $diffList);

        $idx2 = $diffList[0];
        $idx3 = $diffList[1];
        $idx4 = $diffList[2];

        $this->assertTrue($idx2->getDiffType()->isNew());
        $this->assertNull($idx2->getTargetObject());
        $this->assertInstanceOf(IndexInterface::class, $idx2->getSourceObject());

        $this->assertTrue($idx3->getDiffType()->isModified());

        $this->assertTrue($idx4->getDiffType()->isDeleted());
        $this->assertNull($idx4->getSourceObject());
        $this->assertInstanceOf(IndexInterface::class, $idx4->getTargetObject());
    }
}
