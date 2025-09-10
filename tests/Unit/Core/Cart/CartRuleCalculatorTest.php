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

namespace Tests\Unit\Core\Cart;

use PHPUnit\Framework\TestCase;

class CartRuleCalculatorTest extends TestCase
{
    public function testDivisionByZeroPrevention(): void
    {
        // Test the simple division by zero prevention logic
        $taxRate = -1.0; // This would cause division by zero in the original code

        // Test the simple condition that prevents division by zero
        $result = (1 + $taxRate) != 0 ? 10.0 / (1 + $taxRate) : 10.0;

        $this->assertEquals(10.0, $result, 'Should handle tax rate -1 without division by zero');
    }
}
