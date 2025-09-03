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

/**
 * Adds new address
 */
class AddManufacturerAddressCommand
{
    /**
     * @var int|null
     */
    private $manufacturerId;

    /**
     * @param string      $lastName
     * @param string      $firstName
     * @param string      $address
     * @param int|null    $countryId
     * @param string      $city
     * @param int         $manufacturerId
     * @param string|null $address2
     * @param string|null $postCode
     * @param int|null    $stateId
     * @param string|null $homePhone
     * @param string      $mobilePhone
     * @param string|null $other
     * @param string|null $dni
     *
     * @throws AddressConstraintException
     */
    public function __construct(
        private $lastName,
        private $firstName,
        private $address,
        private $countryId,
        private $city,
        $manufacturerId = null,
        private $address2 = null,
        private $postCode = null,
        private $stateId = null,
        private $homePhone = null,
        private $mobilePhone = null,
        private $other = null,
        private $dni = null,
    ) {
        $this->assertIsNullOrNonNegativeInt($manufacturerId);
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return int
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @return string|null
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @return int|null
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @return string|null
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @return string|null
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * @return string|null
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @throws AddressConstraintException
     */
    private function assertIsNullOrNonNegativeInt($value)
    {
        if ($value === null || \is_int($value) || $value >= 0) {
            return;
        }

        throw new AddressConstraintException(\sprintf('Invalid manufacturer id "%s" provided for address.', var_export($value, true)), AddressConstraintException::INVALID_MANUFACTURER_ID);
    }
}
