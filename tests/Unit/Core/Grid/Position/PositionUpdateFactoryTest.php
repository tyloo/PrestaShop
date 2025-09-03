<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Position;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory;

/**
 * Class PositionUpdateFactoryTest.
 */
class PositionUpdateFactoryTest extends TestCase
{
    public function testHandleData(): void
    {
        $definition = $this->getDefinition();
        $data = [
            'positions' => [
                ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 2],
            ],
        ];

        $positionUpdateFactory = $this->getPositionUpdateFactory();
        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($data, $definition);
        /** @var PositionModificationCollectionInterface $collection */
        $collection = $positionUpdate->getPositionModificationCollection();
        $this->assertNotNull($collection);
        $this->assertEquals(1, $collection->count());
        /** @var PositionModificationInterface $positionModification */
        $positionModification = $collection->current();
        $this->assertEquals(1, $positionModification->getId());
        $this->assertEquals(1, $positionModification->getOldPosition());
        $this->assertEquals(2, $positionModification->getNewPosition());
        $this->assertNull($positionUpdate->getParentId());
    }

    public function testHandleDataWithParent(): void
    {
        $definition = $this->getDefinitionWithParent();
        $data = [
            'positions' => [
                ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 2],
            ],
            'parentId' => 42,
        ];

        $positionUpdateFactory = $this->getPositionUpdateFactory();
        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($data, $definition);
        /** @var PositionModificationCollectionInterface $collection */
        $collection = $positionUpdate->getPositionModificationCollection();
        $this->assertNotNull($collection);
        $this->assertEquals(1, $collection->count());
        /** @var PositionModificationInterface $positionModification */
        $positionModification = $collection->current();
        $this->assertEquals(1, $positionModification->getId());
        $this->assertEquals(1, $positionModification->getOldPosition());
        $this->assertEquals(2, $positionModification->getNewPosition());
        $this->assertEquals(42, $positionUpdate->getParentId());
    }

    public function testDataPositionsValidation(): void
    {
        $this->checkDataValidation([], 'Missing positions in your data.');
    }

    public function testDataEmptyPositionValidation(): void
    {
        $this->checkDataValidation(['positions' => []], 'Missing positions in your data.');
    }

    public function testDataPositionValidation(): void
    {
        $data = ['positions' => [
            ['row' => 1],
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'rowId']);

        $data = ['positions' => [
            ['rowId' => 1],
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'oldPosition']);

        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1],
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'newPosition']);
    }

    public function testDataParentIdValidation(): void
    {
        $definition = $this->getDefinitionWithParent();
        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 1],
        ]];
        $this->checkDataValidation($data, 'Missing parentId in your data.', null, $definition);
    }

    /**
     * @param string|null             $expectedErrorKey
     * @param PositionDefinition|null $definition
     */
    private function checkDataValidation(array $data, $expectedErrorKey = null, ?array $expectedErrorParameters = null, $definition = null): void
    {
        if ($definition === null) {
            $definition = $this->getDefinition();
        }

        $positionUpdateFactory = $this->getPositionUpdateFactory();

        /** @var PositionDataException $caughtException */
        $caughtException = null;

        try {
            $positionUpdateFactory->buildPositionUpdate($data, $definition);
        } catch (PositionDataException $positionDataException) {
            $caughtException = $positionDataException;
        }

        if ($expectedErrorKey === null) {
            $this->assertNull($caughtException);
        } else {
            $this->assertNotNull($caughtException);
            $this->assertInstanceOf(PositionDataException::class, $caughtException);
            $this->assertEquals($expectedErrorKey, $caughtException->getKey());
            $this->assertEquals('Admin.Notifications.Failure', $caughtException->getDomain());
            if ($expectedErrorParameters !== null) {
                $this->assertSame($expectedErrorParameters, $caughtException->getParameters());
            }
        }
    }

    private function getDefinition(): PositionDefinition
    {
        return new PositionDefinition(
            'product',
            'id_product',
            'position'
        );
    }

    private function getDefinitionWithParent(): PositionDefinition
    {
        return new PositionDefinition(
            'product',
            'id_product',
            'position',
            'id_category'
        );
    }

    private function getPositionUpdateFactory(): PositionUpdateFactory
    {
        return new PositionUpdateFactory(
            'positions',
            'rowId',
            'oldPosition',
            'newPosition',
            'parentId'
        );
    }
}
