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

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;

class CatalogPriceRuleForListing
{
    public function __construct(
        private readonly int $catalogPriceRuleId,
        private readonly string $catalogPriceRuleName,
        private readonly int $fromQuantity,
        private readonly string $reductionType,
        private readonly DecimalNumber $reduction,
        private readonly bool $isTaxIncluded,
        private readonly DateTimeInterface $dateStart,
        private readonly DateTimeInterface $dateEnd,
        private readonly ?string $shopName,
        private readonly ?string $currencyName,
        private readonly ?string $countryName,
        private readonly ?string $groupName,
        private readonly ?string $currencyIso,
    ) {
    }

    public function getCatalogPriceRuleId(): int
    {
        return $this->catalogPriceRuleId;
    }

    public function getCatalogPriceRuleName(): string
    {
        return $this->catalogPriceRuleName;
    }

    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    public function getReductionType(): string
    {
        return $this->reductionType;
    }

    public function getReduction(): DecimalNumber
    {
        return $this->reduction;
    }

    public function getDateStart(): DateTimeInterface
    {
        return $this->dateStart;
    }

    public function getDateEnd(): DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function getCurrencyIso(): ?string
    {
        return $this->currencyIso;
    }

    public function isTaxIncluded(): bool
    {
        return $this->isTaxIncluded;
    }
}
