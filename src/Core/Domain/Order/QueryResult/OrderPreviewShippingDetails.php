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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

@trigger_error(
    \sprintf(
        '%s is deprecated since version 1.7.7.5 and will be removed in the next major version. Use %s::%s instead.',
        OrderPreviewShippingDetails::class,
        OrderPreview::class,
        'getShippingAddressFormatted()'
    ),
    \E_USER_DEPRECATED
);

/**
 * DTO for order shipping details
 *
 * @deprecated Since 1.7.7.5 and will be removed in the next major.
 */
class OrderPreviewShippingDetails
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly ?string $company,
        private readonly ?string $vatNumber,
        private readonly string $address1,
        private readonly string $address2,
        private readonly string $city,
        private readonly string $postalCode,
        private readonly ?string $stateName,
        private readonly string $country,
        private readonly string $phone,
        private readonly ?string $carrierName,
        private readonly ?string $trackingNumber,
        private readonly ?string $dni = null,
        private readonly ?string $trackingUrl = null,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getAddress1(): string
    {
        return $this->address1;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAddress2(): string
    {
        return $this->address2;
    }

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function getDNI(): ?string
    {
        return $this->dni;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }
}
