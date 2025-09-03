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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Holds information of combination prices
 */
class CombinationPrices
{
    public function __construct(
        private readonly DecimalNumber $impactOnPrice,
        private readonly DecimalNumber $impactOnPriceTaxIncluded,
        private readonly DecimalNumber $impactOnUnitPrice,
        private readonly DecimalNumber $impactOnUnitPriceTaxIncluded,
        private readonly DecimalNumber $ecotax,
        private readonly DecimalNumber $ecotaxTaxIncluded,
        private readonly DecimalNumber $wholesalePrice,
        /**
         * Value between 0 and 100.
         */
        private readonly DecimalNumber $productTaxRate,
        private readonly DecimalNumber $productPrice,
        private readonly DecimalNumber $productEcotax,
    ) {
    }

    public function getImpactOnPrice(): DecimalNumber
    {
        return $this->impactOnPrice;
    }

    public function getImpactOnPriceTaxIncluded(): DecimalNumber
    {
        return $this->impactOnPriceTaxIncluded;
    }

    public function getImpactOnUnitPrice(): DecimalNumber
    {
        return $this->impactOnUnitPrice;
    }

    public function getImpactOnUnitPriceTaxIncluded(): DecimalNumber
    {
        return $this->impactOnUnitPriceTaxIncluded;
    }

    public function getEcotax(): DecimalNumber
    {
        return $this->ecotax;
    }

    public function getEcotaxTaxIncluded(): DecimalNumber
    {
        return $this->ecotaxTaxIncluded;
    }

    public function getWholesalePrice(): DecimalNumber
    {
        return $this->wholesalePrice;
    }

    public function getProductTaxRate(): DecimalNumber
    {
        return $this->productTaxRate;
    }

    public function getProductPrice(): DecimalNumber
    {
        return $this->productPrice;
    }

    public function getProductEcotax(): DecimalNumber
    {
        return $this->productEcotax;
    }
}
