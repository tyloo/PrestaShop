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
    /**
     * @var int
     */
    private $specificPriceId;

    /**
     * @var string
     */
    private $reductionType;

    /**
     * @var DecimalNumber
     */
    private $reductionValue;

    /**
     * @var bool
     */
    private $includesTax;

    /**
     * @var FixedPriceInterface
     */
    private $fixedPrice;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var string|null
     */
    private $shopName;

    /**
     * @var string|null
     */
    private $currencyName;

    /**
     * @var string|null
     */
    private $currencyISOCode;

    /**
     * @var string|null
     */
    private $countryName;

    /**
     * @var string|null
     */
    private $groupName;

    /**
     * @var string|null
     */
    private $customerName;

    /**
     * @var string|null
     */
    private $combinationName;

    /**
     * @var DateTimeInterface
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface
     */
    private $dateTimeTo;

    public function __construct(
        int $specificPriceId,
        string $reductionType,
        DecimalNumber $reductionValue,
        bool $includesTax,
        FixedPriceInterface $fixedPrice,
        int $fromQuantity,
        DateTimeInterface $dateTimeFrom,
        DateTimeInterface $dateTimeTo,
        ?string $combinationName,
        ?string $shop,
        ?string $currencyName,
        ?string $currencyISOCode,
        ?string $country,
        ?string $group,
        ?string $customer,
    ) {
        $this->specificPriceId = $specificPriceId;
        $this->reductionType = $reductionType;
        $this->reductionValue = $reductionValue;
        $this->includesTax = $includesTax;
        $this->fixedPrice = $fixedPrice;
        $this->fromQuantity = $fromQuantity;
        $this->dateTimeFrom = $dateTimeFrom;
        $this->dateTimeTo = $dateTimeTo;
        $this->combinationName = $combinationName;
        $this->shopName = $shop;
        $this->currencyName = $currencyName;
        $this->currencyISOCode = $currencyISOCode;
        $this->countryName = $country;
        $this->groupName = $group;
        $this->customerName = $customer;
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
