<?php

namespace BronOS\PhpSqlDiscovery\Tests;


use BronOS\PhpSqlDiff\DefaultSQLDatabaseDiffer;
use BronOS\PhpSqlDiff\DefaultSQLTableDiffer;
use BronOS\PhpSqlDiff\Diff\ColumnDiff;
use BronOS\PhpSqlDiff\Diff\IndexDiff;
use BronOS\PhpSqlDiff\Diff\TableDiff;
use BronOS\PhpSqlSchema\Column\Numeric\IntColumn;
use BronOS\PhpSqlSchema\Column\String\VarCharColumn;
use BronOS\PhpSqlSchema\Index\Key;
use BronOS\PhpSqlSchema\Index\PrimaryKey;
use BronOS\PhpSqlSchema\Relation\ForeignKey;
use BronOS\PhpSqlSchema\SQLDatabaseSchema;
use BronOS\PhpSqlSchema\SQLTableSchema;
use PHPUnit\Framework\TestCase;

class SQLDatabaseDifferTest extends TestCase
{
    public function testNoDiff()
    {
        $table1 = new SQLTableSchema(
            'tbl1',
            [
                new IntColumn(
                    'id',
                    11,
                    true,
                    true
                ),
                new IntColumn(
                    'tbl2_id',
                    11,
                ),
                new VarCharColumn(
                    'nickname',
                    100
                ),
            ],
            [
                new Key(
                    ['nickname']
                ),
            ],
            [
                new ForeignKey(
                    'tbl2_id',
                    'tbl2',
                    'id',
                    'tbl2_to_tbl1'
                )
            ]
        );
        $table2 = new SQLTableSchema(
            'tbl1',
            [
                new IntColumn(
                    'id',
                    11,
                    true,
                    true
                ),
                new IntColumn(
                    'tbl2_id',
                    11,
                ),
                new VarCharColumn(
                    'nickname',
                    100
                ),
            ],
            [
                new PrimaryKey(
                    ['id']
                ),
                new Key(
                    ['nickname']
                ),
                new Key(
                    ['tbl2_id'],
                    'tbl2_to_tbl1'
                ),
            ],
            [
                new ForeignKey(
                    'tbl2_id',
                    'tbl2',
                    'id',
                    'tbl2_to_tbl1'
                )
            ],
            'InnoDB',
            'latin1',
            'latin1_general_ci'
        );

        $db1 = new SQLDatabaseSchema(
            'db1',
            [$table1],
        );
        $db2 = new SQLDatabaseSchema(
            'db1',
            [$table2],
            $table2->getEngine(),
            $table2->getCharset(),
            $table2->getCollation()
        );

        $differ = new DefaultSQLDatabaseDiffer();

        $this->assertNull($differ->diff($db1, $db2, $db2->getDefaultEngine(), $db2->getDefaultCharset(), $db2->getDefaultCollation()));
    }

    public function testDiffNewDeleted()
    {
        $table1 = new SQLTableSchema(
            'tbl1',
            [
                new IntColumn(
                    'id',
                    11,
                    true,
                    true
                ),
                new IntColumn(
                    'tbl2_id',
                    11,
                ),
                new VarCharColumn(
                    'nickname2',
                    100
                ),
            ],
            [
                new Key(
                    ['nickname2'],
                    'k1'
                ),
            ],
            [
                new ForeignKey(
                    'tbl2_id',
                    'tbl2',
                    'id',
                    'tbl2_to_tbl1'
                )
            ]
        );
        $table2 = new SQLTableSchema(
            'tbl1',
            [
                new IntColumn(
                    'id',
                    10,
                    true,
                    true
                ),
                new IntColumn(
                    'tbl2_id',
                    11,
                ),
                new VarCharColumn(
                    'nickname',
                    100
                ),
            ],
            [
                new PrimaryKey(
                    ['id']
                ),
                new Key(
                    ['nickname'],
                    'k1'
                ),
                new Key(
                    ['tbl2_id'],
                    'tbl2_to_tbl1'
                ),
            ],
            [
                new ForeignKey(
                    'tbl2_id',
                    'tbl2',
                    'id',
                    'tbl2_to_tbl1'
                )
            ]
        );

        $differ = new DefaultSQLTableDiffer();
        $diff = $differ->diff($table1, $table2, 'InnoDB', 'latin1', 'latin1_general_ci');

        $this->assertNotNull($diff);
        $this->assertInstanceOf(TableDiff::class, $diff);
        $this->assertFalse($diff->isName());
        $this->assertFalse($diff->isCharset());
        $this->assertFalse($diff->isCollate());
        $this->assertFalse($diff->isRelations());
        $this->assertTrue($diff->isColumns());
        $this->assertTrue($diff->isIndexes());

        $this->assertCount(3, $diff->getColumnDiffs());
        $this->assertCount(1, $diff->getIndexDiffs());

        $idDiff = $diff->getColumnDiffs()[0];
        $nickname2Diff = $diff->getColumnDiffs()[1];
        $nicknameDiff = $diff->getColumnDiffs()[2];
        $idxDiff = $diff->getIndexDiffs()[0];

        $this->assertInstanceOf(ColumnDiff::class, $idDiff);
        $this->assertTrue($idDiff->getDiffType()->isModified());
        $this->assertEquals('id', $idDiff->getSourceObject()->getName());

        $this->assertInstanceOf(ColumnDiff::class, $nickname2Diff);
        $this->assertEquals('nickname2', $nickname2Diff->getSourceObject()->getName());
        $this->assertTrue($nickname2Diff->getDiffType()->isNew());

        $this->assertInstanceOf(ColumnDiff::class, $nicknameDiff);
        $this->assertEquals('nickname', $nicknameDiff->getTargetObject()->getName());
        $this->assertTrue($nicknameDiff->getDiffType()->isDeleted());

        $this->assertInstanceOf(IndexDiff::class, $idxDiff);
        $this->assertTrue($idxDiff->getDiffType()->isModified());
        $this->assertTrue($idxDiff->isFields());
    }
}
