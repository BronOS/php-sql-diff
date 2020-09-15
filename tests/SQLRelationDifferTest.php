<?php

namespace BronOS\PhpSqlDiscovery\Tests;


use BronOS\PhpSqlDiff\Diff\DiffTypeEnum;
use BronOS\PhpSqlDiff\SQLRelationDiffer;
use BronOS\PhpSqlSchema\Relation\Action\CascadeAction;
use BronOS\PhpSqlSchema\Relation\Action\NoAction;
use BronOS\PhpSqlSchema\Relation\Action\RestrictAction;
use BronOS\PhpSqlSchema\Relation\Action\SetNullAction;
use BronOS\PhpSqlSchema\Relation\ForeignKey;
use BronOS\PhpSqlSchema\Relation\ForeignKeyInterface;
use PHPUnit\Framework\TestCase;

class SQLRelationDifferTest extends TestCase
{
    public function testNoDiff()
    {
        $rel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            null,
            null
        );
        $rel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            null,
            null
        );

        $differ = new SQLRelationDiffer();

        $this->assertNull($differ->diff($rel1, $rel2));
    }

    public function testDiffName()
    {
        $rel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            null,
            null
        );
        $rel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk2',
            null,
            null
        );

        $differ = new SQLRelationDiffer();

        $diff = $differ->diff($rel1, $rel2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->isName());

        $this->assertFalse($diff->isTargetField());
        $this->assertFalse($diff->isTargetTable());
        $this->assertFalse($diff->isSourceField());
        $this->assertFalse($diff->isOnDeleteAction());
        $this->assertFalse($diff->isOnUpdateAction());
    }

    public function testDiffRestrictAction()
    {
        $rel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            null,
            null
        );
        $rel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk2',
            new RestrictAction(),
            null
        );

        $differ = new SQLRelationDiffer();

        $diff = $differ->diff($rel1, $rel2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->isName());

        $this->assertFalse($diff->isTargetField());
        $this->assertFalse($diff->isTargetTable());
        $this->assertFalse($diff->isSourceField());
        $this->assertFalse($diff->isOnDeleteAction());
        $this->assertFalse($diff->isOnUpdateAction());
    }

    public function testDiffNullAction()
    {
        $rel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            null,
            null
        );
        $rel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk2',
            null,
            new NoAction()
        );

        $differ = new SQLRelationDiffer();

        $diff = $differ->diff($rel1, $rel2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->isName());

        $this->assertFalse($diff->isTargetField());
        $this->assertFalse($diff->isTargetTable());
        $this->assertFalse($diff->isSourceField());
        $this->assertFalse($diff->isOnDeleteAction());
        $this->assertTrue($diff->isOnUpdateAction());
    }

    public function testDiffNotNullAction()
    {
        $rel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            null,
            new NoAction()
        );
        $rel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk2',
            null,
            null
        );

        $differ = new SQLRelationDiffer();

        $diff = $differ->diff($rel1, $rel2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->isName());

        $this->assertFalse($diff->isTargetField());
        $this->assertFalse($diff->isTargetTable());
        $this->assertFalse($diff->isSourceField());
        $this->assertFalse($diff->isOnDeleteAction());
        $this->assertTrue($diff->isOnUpdateAction());
    }

    public function testDiffAction()
    {
        $rel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            new SetNullAction(),
            null
        );
        $rel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk2',
            new RestrictAction(),
            null
        );

        $differ = new SQLRelationDiffer();

        $diff = $differ->diff($rel1, $rel2);

        $this->assertNotNull($diff);
        $this->assertEquals(DiffTypeEnum::MODIFIED, $diff->getDiffType()->getValue());
        $this->assertTrue($diff->isName());

        $this->assertFalse($diff->isTargetField());
        $this->assertFalse($diff->isTargetTable());
        $this->assertFalse($diff->isSourceField());
        $this->assertTrue($diff->isOnDeleteAction());
        $this->assertFalse($diff->isOnUpdateAction());
    }

    public function testHashDiff()
    {
        $sourceRel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            new SetNullAction(),
            null
        );
        $sourceRel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk2',
            new RestrictAction(),
            null
        );
        $sourceRel3 = new ForeignKey(
            'f3',
            't3',
            'tf3',
            'fk3',
            new RestrictAction(),
            null
        );

        $targetRel1 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk1',
            new SetNullAction(),
            null
        );
        $targetRel2 = new ForeignKey(
            'f1',
            't1',
            'tf1',
            'fk4',
            new RestrictAction(),
            null
        );
        $targetRel3 = new ForeignKey(
            'f3-x',
            't3-x',
            'tf3-x',
            'fk3',
            new SetNullAction(),
            new CascadeAction()
        );

        $differ = new SQLRelationDiffer();

        $diffList = $differ->hashDiff([
            $sourceRel2->getName() => $sourceRel2,
            $sourceRel3->getName() => $sourceRel3,
            $sourceRel1->getName() => $sourceRel1,
        ], [
            $targetRel1->getName() => $targetRel1,
            $targetRel2->getName() => $targetRel2,
            $targetRel3->getName() => $targetRel3,
        ]);

        $this->assertCount(3, $diffList);

        $fk2 = $diffList[0];
        $fk3 = $diffList[1];
        $fk4 = $diffList[2];

        $this->assertTrue($fk2->getDiffType()->isNew());
        $this->assertNull($fk2->getTargetObject());
        $this->assertInstanceOf(ForeignKeyInterface::class, $fk2->getSourceObject());

        $this->assertTrue($fk3->getDiffType()->isModified());

        $this->assertTrue($fk4->getDiffType()->isDeleted());
        $this->assertNull($fk4->getSourceObject());
        $this->assertInstanceOf(ForeignKeyInterface::class, $fk4->getTargetObject());
    }
}
