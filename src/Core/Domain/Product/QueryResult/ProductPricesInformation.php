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

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;

/**
 * Holds information about product prices
 */
class ProductPricesInformation
{
    public function __construct(
        private readonly DecimalNumber $price,
        private readonly DecimalNumber $priceTaxIncluded,
        private readonly DecimalNumber $ecotax,
        private readonly DecimalNumber $ecotaxTaxIncluded,
        private readonly int $taxRulesGroupId,
        private readonly bool $onSale,
        private readonly DecimalNumber $wholesalePrice,
        private readonly DecimalNumber $unitPrice,
        private readonly DecimalNumber $unitPriceTaxIncluded,
        private readonly string $unity,
        private readonly DecimalNumber $unitPriceRatio,
        private readonly ?PriorityList $specificPricePriorities,
    ) {
    }

    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }

    public function getPriceTaxIncluded(): DecimalNumber
    {
        return $this->priceTaxIncluded;
    }

    public function getEcotax(): DecimalNumber
    {
        return $this->ecotax;
    }

    public function getEcotaxTaxIncluded(): DecimalNumber
    {
        return $this->ecotaxTaxIncluded;
    }

    public function getTaxRulesGroupId(): int
    {
        return $this->taxRulesGroupId;
    }

    public function isOnSale(): bool
    {
        return $this->onSale;
    }

    public function getWholesalePrice(): DecimalNumber
    {
        return $this->wholesalePrice;
    }

    public function getUnitPrice(): DecimalNumber
    {
        return $this->unitPrice;
    }

    public function getUnitPriceTaxIncluded(): DecimalNumber
    {
        return $this->unitPriceTaxIncluded;
    }

    public function getUnity(): string
    {
        return $this->unity;
    }

    public function getUnitPriceRatio(): DecimalNumber
    {
        return $this->unitPriceRatio;
    }

    public function getSpecificPricePriorities(): ?PriorityList
    {
        return $this->specificPricePriorities;
    }
}
