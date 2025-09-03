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

namespace Tests\Unit\Core\Foundation\IoC;

use Exception;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use Tests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure;
use Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testBindByClosure(): void
    {
        $this->container->bind('foo', fn (): string => 'FOO');

        $this->assertEquals('FOO', $this->container->make('foo'));
    }

    public function testCannotBindTheSameServiceTwice(): void
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->bind('foo', function (): void {});
        $this->container->bind('foo', function (): void {});
    }

    public function testBindByClosureInstanceNotSharedByDefault(): void
    {
        $this->container->bind('different', fn (): Dummy => new Dummy());

        $first = $this->container->make('different');
        $second = $this->container->make('different');

        $this->assertNotSame($first, $second);
    }

    public function testBindByClosureInstanceSharedIfExplicitelyRequired(): void
    {
        $this->container->bind('same', fn (): Dummy => new Dummy(), true);

        $first = $this->container->make('same');
        $second = $this->container->make('same');

        $this->assertSame($first, $second);
    }

    public function testBindClassName(): void
    {
        $this->container->bind('dummy', Dummy::class);

        $this->assertEquals(Dummy::class, $this->container->make('dummy')::class);
    }

    public function testMakeWithoutBind(): void
    {
        $this->assertEquals(Dummy::class, $this->container->make(Dummy::class)::class);
    }

    public function testClassesCanBeLoadedWithCustomNamespacePrefix(): void
    {
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\IoC\Fixtures');

        $this->assertEquals(Dummy::class, $this->container->make('Fixtures:Dummy')::class);
    }

    public function testAnAliasCannotBeChanged(): void
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\IoC\Fixtures');
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\Other');
    }

    public function testDepsAreFetchedAutomagically(): void
    {
        $this->assertEquals(Fixtures\ClassWithDep::class, $this->container->make(Fixtures\ClassWithDep::class)::class);
    }

    public function testDepsAreFetchedAutomagicallyWhenDependsOnThingWithADefaultValue(): void
    {
        $this->assertEquals(Fixtures\ClassWithDepAndDefault::class, $this->container->make(Fixtures\ClassWithDepAndDefault::class)::class);
    }

    public function testUnbuildableNotBuilt(): void
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->make(Fixtures\UnBuildable::class);
    }

    public function testNonExistingClassNotBuilt(): void
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\AClassThatDoesntExistAtAll');
    }

    public function testDependencyLoopDoesntCrashContainer(): void
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        /*
         * CycleA depends on CycleB,
         * CycleB depends on CycleA
         */
        $this->container->make(Fixtures\CycleA::class);
    }

    public function testCanBuildClassWhoseDependencyIsBuitByClosure(): void
    {
        $this->container->bind(
            DepBuiltByClosure::class,
            fn (): DepBuiltByClosure => new DepBuiltByClosure(42)
        );

        $instance = $this->container->make(
            Fixtures\ClassDependingOnClosureBuiltDep::class
        );
        $this->assertEquals(42, $instance->getDep()->getValue());
    }

    /**
     * data provider for test_container_can_bind_values_directly
     */
    public function valuesToBind()
    {
        return [
            [new Dummy()],
            [42],
            [[1, 2, 3]],
        ];
    }

    /**
     * @dataProvider valuesToBind
     */
    public function testContainerCanBindValuesDirectly($value): void
    {
        $this->container->bind('value', $value);
        $this->assertSame($value, $this->container->make('value'));
    }

    public function testContainerDoesntBindStringsAsLiteralValues(): void
    {
        $this->expectException(Exception::class);

        $this->container->bind('value', 'a string which is not a class name');
        $this->container->make('value');
    }
}
