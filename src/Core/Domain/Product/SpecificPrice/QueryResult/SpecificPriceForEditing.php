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

class SpecificPriceForEditing
{
    public function __construct(
        private readonly int $specificPriceId,
        private readonly string $reductionType,
        private readonly DecimalNumber $reductionAmount,
        private readonly bool $includesTax,
        private readonly FixedPriceInterface $fixedPrice,
        private readonly int $fromQuantity,
        private readonly DateTimeInterface $dateTimeFrom,
        private readonly DateTimeInterface $dateTimeTo,
        private readonly int $productId,
        private readonly ?CustomerInfo $customerInfo,
        private readonly ?int $combinationId,
        private readonly ?int $shopId,
        private readonly ?int $currencyId,
        private readonly ?int $countryId,
        private readonly ?int $groupId,
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

    public function getReductionAmount(): DecimalNumber
    {
        return $this->reductionAmount;
    }

    public function includesTax(): bool
    {
        return $this->includesTax;
    }

    public function getFixedPrice(): FixedPriceInterface
    {
        return $this->fixedPrice;
    }

    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function getCustomerInfo(): ?CustomerInfo
    {
        return $this->customerInfo;
    }

    public function getDateTimeFrom(): DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    public function getDateTimeTo(): DateTimeInterface
    {
        return $this->dateTimeTo;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getCombinationId(): ?int
    {
        return $this->combinationId;
    }
}
