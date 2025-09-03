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
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Adds new catalog price rule with provided data
 */
class AddCatalogPriceRuleCommand
{
    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @var DecimalNumber
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
     * @throws DomainConstraintException
     */
    public function __construct(
        private readonly string $name,
        private readonly int $currencyId,
        private readonly int $countryId,
        private readonly int $groupId,
        private readonly int $fromQuantity,
        string $reductionType,
        string $reductionValue,
        private readonly int $shopId,
        private readonly bool $includeTax,
        float $price,
    ) {
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->price = new DecimalNumber((string) $price);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    public function getReduction(): Reduction
    {
        return $this->reduction;
    }

    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }

    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }

    public function isTaxIncluded(): bool
    {
        return $this->includeTax;
    }

    /**
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeFrom(string $dateTimeFrom): void
    {
        $this->dateTimeFrom = $this->createDateTime($dateTimeFrom);
    }

    /**
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeTo(string $dateTimeTo): void
    {
        $this->dateTimeTo = $this->createDateTime($dateTimeTo);
    }

    /**
     * @throws CatalogPriceRuleConstraintException
     */
    private function createDateTime(string $dateTime): DateTime
    {
        try {
            return new DateTime($dateTime);
        } catch (Exception $exception) {
            throw new CatalogPriceRuleConstraintException('An error occured when creating DateTime object for catalog price rule', CatalogPriceRuleConstraintException::INVALID_DATETIME, $exception);
        }
    }
}
