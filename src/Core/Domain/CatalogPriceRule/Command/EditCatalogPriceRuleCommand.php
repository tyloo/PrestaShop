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

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command;

use DateTime;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Edits catalog price rule with given data
 */
class EditCatalogPriceRuleCommand
{
    private readonly CatalogPriceRuleId $catalogPriceRuleId;

    /**
     * @var string|null
     */
    private $name;

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

    /**
     * @var int|null
     */
    private $fromQuantity;

    /**
     * @var float|null
     */
    private $price;

    /**
     * @var DateTime|null
     */
    private $dateTimeFrom;

    /**
     * @var DateTime|null
     */
    private $dateTimeTo;

    /**
     * @var bool|null
     */
    private $includeTax;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @throws CatalogPriceRuleConstraintException
     */
    public function __construct(int $catalogPriceRuleId)
    {
        $this->catalogPriceRuleId = new CatalogPriceRuleId($catalogPriceRuleId);
    }

    public function getCatalogPriceRuleId(): CatalogPriceRuleId
    {
        return $this->catalogPriceRuleId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }

    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    public function setCurrencyId(int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function setCountryId(int $countryId): void
    {
        $this->countryId = $countryId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getFromQuantity(): ?int
    {
        return $this->fromQuantity;
    }

    public function setFromQuantity(int $fromQuantity): void
    {
        $this->fromQuantity = $fromQuantity;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    /**
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeFrom(string $dateTimeFrom): void
    {
        $this->dateTimeFrom = $this->createDateTime($dateTimeFrom);
    }

    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }

    /**
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeTo(string $dateTimeTo): void
    {
        $this->dateTimeTo = $this->createDateTime($dateTimeTo);
    }

    public function isTaxIncluded(): ?bool
    {
        return $this->includeTax;
    }

    public function setIncludeTax(bool $includeTax): void
    {
        $this->includeTax = $includeTax;
    }

    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    public function setReduction(string $type, string $value): void
    {
        $this->reduction = new Reduction($type, $value);
    }

    private function createDateTime(string $dateTime): DateTime
    {
        try {
            return new DateTime($dateTime);
        } catch (Exception $exception) {
            throw new CatalogPriceRuleConstraintException('Invalid date time format', CatalogPriceRuleConstraintException::INVALID_DATETIME, $exception);
        }
    }
}
