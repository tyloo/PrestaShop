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

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use Tools;

/**
 * Adds new zone with provided data.
 */
class AddCountryCommand
{
    /**
     * @var string
     */
    private $isoCode;

    private readonly ZoneId $zoneId;

    private readonly ?CountryZipCodeFormat $zipCodeFormat;

    public function __construct(
        /**
         * @var string[]
         */
        private readonly array $localizedNames,
        string $isoCode,
        private readonly int $callPrefix,
        private readonly int $defaultCurrency,
        int $zoneId,
        private readonly bool $needZipCode,
        ?string $zipCodeFormat,
        private readonly string $addressFormat,
        private readonly bool $enabled,
        private readonly bool $containsStates,
        private readonly bool $needIdNumber,
        private readonly bool $displayTaxLabel,
        /**
         * @var int[]
         */
        private readonly array $shopAssociation,
    ) {
        $this->isoCode = Tools::strtoupper(Tools::substr($isoCode, 0, 2));
        $this->zoneId = new ZoneId($zoneId);
        $this->zipCodeFormat = $zipCodeFormat ? new CountryZipCodeFormat($zipCodeFormat) : null;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function getCallPrefix(): int
    {
        return $this->callPrefix;
    }

    public function getDefaultCurrency(): int
    {
        return $this->defaultCurrency;
    }

    public function getZoneId(): ZoneId
    {
        return $this->zoneId;
    }

    public function needZipCode(): bool
    {
        return $this->needZipCode;
    }

    public function getZipCodeFormat(): ?CountryZipCodeFormat
    {
        return $this->zipCodeFormat;
    }

    public function getAddressFormat(): string
    {
        return $this->addressFormat;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    public function needIdNumber(): bool
    {
        return $this->needIdNumber;
    }

    public function displayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
