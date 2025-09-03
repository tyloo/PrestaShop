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

namespace PrestaShop\PrestaShop\Core\Domain\Address\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateIdInterface;

/**
 * Transfers customer address data for editing
 */
class EditableCustomerAddress
{
    /**
     * @param StateId  $stateId
     * @param string[] $requiredFields
     */
    public function __construct(
        private readonly AddressId $addressId,
        private readonly CustomerId $customerId,
        private readonly string $customerEmail,
        private readonly string $addressAlias,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $address,
        private readonly string $city,
        private readonly CountryId $countryId,
        private readonly string $postCode,
        private readonly string $dni,
        private readonly string $company,
        private readonly string $vatNumber,
        private readonly string $address2,
        private readonly StateIdInterface $stateId,
        private readonly string $homePhone,
        private readonly string $mobilePhone,
        private readonly string $other,
        private readonly array $requiredFields,
    ) {
    }

    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getAddressAlias(): string
    {
        return $this->addressAlias;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return string[]
     */
    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function getStateId(): StateIdInterface
    {
        return $this->stateId;
    }

    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }
}
