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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command;

use DateTime;
use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\NoCountryId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopIdInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

/**
 * Add specific price to a Product
 */
class AddSpecificPriceCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @var FixedPriceInterface
     */
    private $fixedPrice;

    /**
     * @var ShopIdInterface
     */
    private $shopId;

    /**
     * @var CombinationIdInterface
     */
    private $combinationId;

    /**
     * @var CurrencyIdInterface
     */
    private $currencyId;

    /**
     * @var CountryIdInterface
     */
    private $countryId;

    /**
     * @var GroupIdInterface
     */
    private $groupId;

    /**
     * @var int
     */
    private $customerId = 0;

    /**
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     */
    public function __construct(
        int $productId,
        string $reductionType,
        string $reductionValue,
        private readonly bool $includesTax,
        string $fixedPrice,
        private readonly int $fromQuantity,
        /**
         * @see DateTime
         * @see NullDateTime
         */
        private DateTimeInterface $dateTimeFrom,
        /**
         * @see DateTime
         * @see NullDateTime
         */
        private DateTimeInterface $dateTimeTo,
    ) {
        $this->productId = new ProductId($productId);
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->setFixedPrice($fixedPrice);
        $this->shopId = new NoShopId();
        $this->combinationId = new NoCombinationId();
        $this->currencyId = new NoCurrencyId();
        $this->groupId = new NoGroupId();
        $this->countryId = new NoCountryId();
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getReduction(): Reduction
    {
        return $this->reduction;
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

    public function getDateTimeFrom(): DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    /**
     * @see DateTime
     * @see NullDateTime
     */
    public function setDateTimeFrom(DateTimeInterface $dateTimeFrom): self
    {
        $this->dateTimeFrom = $dateTimeFrom;

        return $this;
    }

    public function getDateTimeTo(): ?DateTimeInterface
    {
        return $this->dateTimeTo;
    }

    /**
     * @see DateTime
     * @see NullDateTime
     */
    public function setDateTimeTo(DateTimeInterface $dateTimeTo): self
    {
        $this->dateTimeTo = $dateTimeTo;

        return $this;
    }

    public function getShopId(): ShopIdInterface
    {
        return $this->shopId;
    }

    /**
     * @return $this
     */
    public function setShopId(int $shopId): self
    {
        if ($shopId === NoShopId::NO_SHOP_ID) {
            $this->shopId = new NoShopId();
        } else {
            $this->shopId = new ShopId($shopId);
        }

        return $this;
    }

    public function getCombinationId(): CombinationIdInterface
    {
        return $this->combinationId;
    }

    /**
     * @return $this
     */
    public function setCombinationId(int $combinationId): self
    {
        if ($combinationId === NoCombinationId::NO_COMBINATION_ID) {
            $this->combinationId = new NoCombinationId();
        } else {
            $this->combinationId = new CombinationId($combinationId);
        }

        return $this;
    }

    public function getCurrencyId(): CurrencyIdInterface
    {
        return $this->currencyId;
    }

    /**
     * @return $this
     */
    public function setCurrencyId(int $currencyId): self
    {
        if ($currencyId === NoCurrencyId::NO_CURRENCY_ID) {
            $this->currencyId = new NoCurrencyId();
        } else {
            $this->currencyId = new CurrencyId($currencyId);
        }

        return $this;
    }

    public function getCountryId(): CountryIdInterface
    {
        return $this->countryId;
    }

    /**
     * @return $this
     *
     * @throws CountryConstraintException
     */
    public function setCountryId(int $countryId): self
    {
        if ($countryId === NoCountryId::NO_COUNTRY_ID_VALUE) {
            $this->countryId = new NoCountryId();
        } else {
            $this->countryId = new CountryId($countryId);
        }

        return $this;
    }

    public function getGroupId(): GroupIdInterface
    {
        return $this->groupId;
    }

    /**
     * @return $this
     */
    public function setGroupId(int $groupId): self
    {
        if ($groupId === NoGroupId::NO_GROUP_ID) {
            $this->groupId = new NoGroupId();
        } else {
            $this->groupId = new GroupId($groupId);
        }

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return $this
     */
    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    private function setFixedPrice(string $value): void
    {
        if (InitialPrice::isInitialPriceValue($value)) {
            $this->fixedPrice = new InitialPrice();

            return;
        }

        $this->fixedPrice = new FixedPrice($value);
    }
}
