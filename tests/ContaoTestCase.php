<?php

declare(strict_types=1);

namespace iMi\ContaoShibbolethLoginClientBundle\Tests;

use Contao\Model\Registry;
use Contao\MemberGroupModel;
use Contao\System;
use Contao\TestCase\ContaoDatabaseTrait;
use Contao\TestCase\FunctionalTestCase;

abstract class ContaoTestCase extends FunctionalTestCase
{
    use ContaoDatabaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        static::bootKernel();
        System::setContainer(static::getContainer());
        static::resetDatabaseSchema();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::ensureKernelShutdown();
        Registry::getInstance()->reset();
    }

    protected function query($statement)
    {
        $connection = static::getConnection();

        $connection->executeQuery($statement);

        return (int) $connection->lastInsertId();
    }

    protected function createMemberGroup(): MemberGroupModel
    {
        $model = new MemberGroupModel();
        $model->name = 'staff';

        $model->save();

        return $model;
    }
}
