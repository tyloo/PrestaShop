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
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopIdInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

class EditSpecificPriceCommand
{
    private readonly SpecificPriceId $specificPriceId;

    /**
     * @var Reduction|null
     */
    private $reduction;

    /**
     * @var bool|null
     */
    private $includesTax;

    /**
     * @var FixedPriceInterface|null
     */
    private $fixedPrice;

    /**
     * @var int|null
     */
    private $fromQuantity;

    /**
     * @var ShopIdInterface|null
     */
    private $shopId;

    /**
     * @var CombinationIdInterface|null
     */
    private $combinationId;

    /**
     * @var CurrencyIdInterface|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var GroupIdInterface|null
     */
    private $groupId;

    /**
     * @var int|null
     *
     * @todo: countryId & customerId should also use the same convention of {Foo}IdInterface,
     *        but it requires some refactoring as it was already used in many places this primitive way
     *        related reminder issue https://github.com/PrestaShop/PrestaShop/issues/27205
     */
    private $customerId;

    /**
     * @var DateTimeInterface|null
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface|null
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeTo;

    public function __construct(int $specificPriceId)
    {
        $this->specificPriceId = new SpecificPriceId($specificPriceId);
    }

    public function getSpecificPriceId(): SpecificPriceId
    {
        return $this->specificPriceId;
    }

    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    public function setReduction(string $reductionType, string $reductionValue): self
    {
        $this->reduction = new Reduction($reductionType, $reductionValue);

        return $this;
    }

    public function includesTax(): ?bool
    {
        return $this->includesTax;
    }

    public function setIncludesTax(bool $includesTax): self
    {
        $this->includesTax = $includesTax;

        return $this;
    }

    public function getFixedPrice(): ?FixedPriceInterface
    {
        return $this->fixedPrice;
    }

    public function setFixedPrice(string $fixedPrice): self
    {
        if (InitialPrice::isInitialPriceValue($fixedPrice)) {
            $this->fixedPrice = new InitialPrice();
        } else {
            $this->fixedPrice = new FixedPrice($fixedPrice);
        }

        return $this;
    }

    public function getFromQuantity(): ?int
    {
        return $this->fromQuantity;
    }

    public function setFromQuantity(int $fromQuantity): self
    {
        $this->fromQuantity = $fromQuantity;

        return $this;
    }

    public function getShopId(): ?ShopIdInterface
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId === NoShopId::NO_SHOP_ID ? new NoShopId() : new ShopId($shopId);

        return $this;
    }

    public function getCombinationId(): ?CombinationIdInterface
    {
        return $this->combinationId;
    }

    public function setCombinationId(int $combinationId): self
    {
        $this->combinationId = $combinationId === NoCombinationId::NO_COMBINATION_ID ? new NoCombinationId() : new CombinationId($combinationId);

        return $this;
    }

    public function getCurrencyId(): ?CurrencyIdInterface
    {
        return $this->currencyId;
    }

    public function setCurrencyId(int $currencyId): self
    {
        $this->currencyId = $currencyId === NoCurrencyId::NO_CURRENCY_ID ? new NoCurrencyId() : new CurrencyId($currencyId);

        return $this;
    }

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    public function getGroupId(): ?GroupIdInterface
    {
        return $this->groupId;
    }

    public function setGroupId(int $groupId): self
    {
        $this->groupId = $groupId === NoGroupId::NO_GROUP_ID ? new NoGroupId() : new GroupId($groupId);

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getDateTimeFrom(): ?DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    public function setDateTimeFrom(DateTimeInterface $dateTimeFrom): self
    {
        $this->dateTimeFrom = $dateTimeFrom;

        return $this;
    }

    public function getDateTimeTo(): ?DateTimeInterface
    {
        return $this->dateTimeTo;
    }

    public function setDateTimeTo(DateTimeInterface $dateTimeTo): self
    {
        $this->dateTimeTo = $dateTimeTo;

        return $this;
    }
}
