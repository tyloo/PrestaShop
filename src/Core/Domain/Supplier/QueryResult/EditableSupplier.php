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

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Transfers supplier data for editing
 */
class EditableSupplier
{
    /**
     * @param string[] $localizedDescriptions
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     */
    public function __construct(
        private readonly SupplierId $supplierId,
        private readonly string $name,
        private readonly array $localizedDescriptions,
        private readonly string $address,
        private readonly string $city,
        private readonly string $address2,
        private readonly int $countryId,
        private readonly string $postCode,
        private readonly int $stateId,
        private readonly string $phone,
        private readonly string $mobilePhone,
        private readonly array $localizedMetaTitles,
        private readonly array $localizedMetaDescriptions,
        private readonly bool $enabled,
        private readonly array $associatedShops,
        private readonly string $dni,
        /**
         * @var array
         */
        private readonly ?array $logoImage = null,
    ) {
    }

    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress2(): string
    {
        return $this->address2;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function getStateId(): int
    {
        return $this->stateId;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getMobilePhone(): string
    {
        return $this->mobilePhone;
    }

    public function getLogoImage(): ?array
    {
        return $this->logoImage;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles(): array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions(): array
    {
        return $this->localizedMetaDescriptions;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getAssociatedShops(): array
    {
        return $this->associatedShops;
    }

    public function getDni(): string
    {
        return $this->dni;
    }
}
