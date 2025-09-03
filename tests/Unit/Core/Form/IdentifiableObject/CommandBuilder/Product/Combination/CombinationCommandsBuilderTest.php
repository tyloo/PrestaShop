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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilderInterface;

class CombinationCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('getExpectedCommands')]
    public function testBuildCommands(array $formData, array $commandBuilders, array $expectedCommands): void
    {
        $builder = new CombinationCommandsBuilder($commandBuilders);
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        $collection = [];
        yield [
            [],
            [],
            $collection,
        ];

        $alwaysEmptyBuilder = new AlwaysEmptyBuilder();
        yield [
            [],
            [$alwaysEmptyBuilder],
            $collection,
        ];

        $commandA = new FakeCombinationCommand($this->getCombinationId(), 'A');
        $commandB = new FakeCombinationCommand($this->getCombinationId(), 'B');

        $builderA = new ConditionBuilder(['field_a' => 'a'], $commandA);
        $builderB = new ConditionBuilder(['field_b' => 'b'], $commandB);

        yield [
            ['field_a' => 'c', 'field_b' => 'b'],
            [$alwaysEmptyBuilder, $builderA, $builderB],
            [$commandB],
        ];

        yield [
            ['field_a' => 'a'],
            [$alwaysEmptyBuilder, $builderA, $builderB],
            [$commandA],
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderA, $alwaysEmptyBuilder, $builderB],
            [$commandA, $commandB],
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderB, $builderA, $alwaysEmptyBuilder],
            [$commandB, $commandA],
        ];

        $multiBuilder = new MultiCommandsBuilder([$builderA, $builderB]);
        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$multiBuilder, $alwaysEmptyBuilder],
            [$commandA, $commandB],
        ];

        $multiBuilder = new MultiCommandsBuilder([$builderB, $builderA]);
        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$multiBuilder, $alwaysEmptyBuilder],
            [$commandB, $commandA],
        ];
    }
}

class FakeCombinationCommand
{
    /**
     * @var CombinationId
     */
    public $combinationId;

    public function __construct(
        CombinationId $combinationId,
        public $value,
    ) {
        $this->combinationId = $combinationId;
    }
}

class ConditionBuilder implements CombinationCommandsBuilderInterface
{
    public function __construct(
        private readonly array $formCondition,
        private $command,
    ) {
    }

    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        foreach ($this->formCondition as $key => $value) {
            if (! isset($formData[$key]) || $formData[$key] !== $value) {
                return [];
            }
        }

        return [$this->command];
    }
}

class AlwaysEmptyBuilder implements CombinationCommandsBuilderInterface
{
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        return [];
    }
}

class MultiCommandsBuilder implements CombinationCommandsBuilderInterface
{
    public function __construct(
        /**
         * @var CombinationCommandsBuilderInterface[]
         */
        private readonly array $builders,
    ) {
    }

    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $commands = [];
        foreach ($this->builders as $builder) {
            $commands = array_merge($commands, $builder->buildCommands($combinationId, $formData, $singleShopConstraint));
        }

        return $commands;
    }
}
