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
    private $reductionAmount;

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
     * @var DateTimeInterface
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface
     */
    private $dateTimeTo;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var CustomerInfo|null
     */
    private $customerInfo;

    /**
     * @var int|null
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $shopId;

    /**
     * @var int|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var int|null
     */
    private $groupId;

    public function __construct(
        int $specificPriceId,
        string $reductionType,
        DecimalNumber $reductionAmount,
        bool $includesTax,
        FixedPriceInterface $fixedPrice,
        int $fromQuantity,
        DateTimeInterface $dateTimeFrom,
        DateTimeInterface $dateTimeTo,
        int $productId,
        ?CustomerInfo $customerInfo,
        ?int $combinationId,
        ?int $shopId,
        ?int $currencyId,
        ?int $countryId,
        ?int $groupId,
    ) {
        $this->specificPriceId = $specificPriceId;
        $this->reductionType = $reductionType;
        $this->reductionAmount = $reductionAmount;
        $this->includesTax = $includesTax;
        $this->fixedPrice = $fixedPrice;
        $this->fromQuantity = $fromQuantity;
        $this->dateTimeFrom = $dateTimeFrom;
        $this->dateTimeTo = $dateTimeTo;
        $this->productId = $productId;
        $this->customerInfo = $customerInfo;
        $this->combinationId = $combinationId;
        $this->shopId = $shopId;
        $this->currencyId = $currencyId;
        $this->countryId = $countryId;
        $this->groupId = $groupId;
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
