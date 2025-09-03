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

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Edits manufacturer address
 */
class EditManufacturerAddressCommand
{
    /**
     * @var AddressId
     */
    private $addressId;

    /**
     * @var int|null
     */
    private $manufacturerId;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $address2;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var string|null
     */
    private $postCode;

    /**
     * @var int|null
     */
    private $stateId;

    /**
     * @var string|null
     */
    private $homePhone;

    /**
     * @var string|null
     */
    private $mobilePhone;

    /**
     * @var string|null
     */
    private $other;

    /**
     * @var string|null
     */
    private $dni;

    /**
     * @param int $addressId
     *
     * @throws AddressConstraintException
     */
    public function __construct($addressId)
    {
        $this->addressId = new AddressId($addressId);
    }

    /**
     * @return AddressId
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param AddressId $addressId
     */
    public function setAddressId($addressId): void
    {
        $this->addressId = $addressId;
    }

    /**
     * @return int|null
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @param int $manufacturerId
     *
     * @throws AddressConstraintException
     */
    public function setManufacturerId($manufacturerId): void
    {
        $this->assertIsNullOrNonNegativeInt($manufacturerId);
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string|null $address2
     */
    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     */
    public function setCountryId($countryId): void
    {
        $this->countryId = $countryId;
    }

    /**
     * @return string|null
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string|null $postCode
     */
    public function setPostCode($postCode): void
    {
        $this->postCode = $postCode;
    }

    /**
     * @return int|null
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @param int|null $stateId
     */
    public function setStateId($stateId): void
    {
        $this->stateId = $stateId;
    }

    /**
     * @return string|null
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @param string|null $homePhone
     */
    public function setHomePhone($homePhone): void
    {
        $this->homePhone = $homePhone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone($mobilePhone): void
    {
        $this->mobilePhone = $mobilePhone;
    }

    /**
     * @return string|null
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * @param string|null $other
     */
    public function setOther($other): void
    {
        $this->other = $other;
    }

    /**
     * @return string|null
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param string|null $dni
     */
    public function setDni($dni): void
    {
        $this->dni = $dni;
    }

    /**
     * @throws AddressConstraintException
     */
    private function assertIsNullOrNonNegativeInt($value): void
    {
        if ($value === null || \is_int($value) || $value >= 0) {
            return;
        }

        throw new AddressConstraintException(\sprintf('Invalid manufacturer id "%s" provided for address.', var_export($value, true)), AddressConstraintException::INVALID_MANUFACTURER_ID);
    }
}
