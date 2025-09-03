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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPriceInterface;

class SpecificPriceForListing
{
    public function __construct(
        private readonly int $specificPriceId,
        private readonly string $reductionType,
        private readonly DecimalNumber $reductionValue,
        private readonly bool $includesTax,
        private readonly FixedPriceInterface $fixedPrice,
        private readonly int $fromQuantity,
        private readonly DateTimeInterface $dateTimeFrom,
        private readonly DateTimeInterface $dateTimeTo,
        private readonly ?string $combinationName,
        private readonly ?string $shopName,
        private readonly ?string $currencyName,
        private readonly ?string $currencyISOCode,
        private readonly ?string $countryName,
        private readonly ?string $groupName,
        private readonly ?string $customerName,
    ) {
    }

    public function getSpecificPriceId(): int
    {
        return $this->specificPriceId;
    }

    public function getReductionType(): string
    {
        return $this->reductionType;
    }

    public function getReductionValue(): DecimalNumber
    {
        return $this->reductionValue;
    }

    public function includesTax(): bool
    {
        return $this->includesTax;
    }

    public function getCombinationName(): ?string
    {
        return $this->combinationName;
    }

    public function getFixedPrice(): FixedPriceInterface
    {
        return $this->fixedPrice;
    }

    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    public function getCurrencyISOCode(): ?string
    {
        return $this->currencyISOCode;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function getDateTimeFrom(): DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    public function getDateTimeTo(): DateTimeInterface
    {
        return $this->dateTimeTo;
    }
}
