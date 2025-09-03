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

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateIdInterface;

/**
 * Adds new customer address
 */
class AddCustomerAddressCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var StateIdInterface
     */
    private $stateId;

    /**
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function __construct(
        int $customerId,
        private readonly string $addressAlias,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $address,
        private readonly string $city,
        int $countryId,
        private readonly string $postCode,
        private readonly ?string $dni = null,
        private readonly ?string $company = null,
        private readonly ?string $vatNumber = null,
        private readonly ?string $address2 = null,
        int $id_state = 0,
        private readonly ?string $homePhone = null,
        private readonly ?string $mobilePhone = null,
        private readonly ?string $other = null,
    ) {
        $this->customerId = new CustomerId($customerId);
        $this->countryId = new CountryId($countryId);
        $this->stateId = $id_state === NoStateId::NO_STATE_ID_VALUE ? new NoStateId() : new StateId($id_state);
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
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

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function getCountryId(): CountryId
    {
        return $this->countryId;
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
