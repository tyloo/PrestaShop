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

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;

class OrderShipment
{
    private int $id;

    private int $orderId;

    private CarrierSummary $carrierSummary;

    private int $addressId;

    private DecimalNumber $shippingCostTaxExcluded;

    private DecimalNumber $shippingCostTaxIncluded;

    private ?string $trackingNumber;

    private ?DateTime $shippedAt;

    private ?DateTime $deliveredAt;

    private ?DateTime $cancelledAt;

    private int $productsCount;

    public function __construct(
        int $id,
        int $orderId,
        CarrierSummary $carrierSummary,
        int $addressId,
        DecimalNumber $shippingCostTaxExcluded,
        DecimalNumber $shippingCostTaxIncluded,
        int $productsCount,
        ?string $trackingNumber,
        ?DateTime $shippedAt,
        ?DateTime $deliveredAt,
        ?DateTime $cancelledAt,
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->carrierSummary = $carrierSummary;
        $this->addressId = $addressId;
        $this->shippingCostTaxExcluded = $shippingCostTaxExcluded;
        $this->shippingCostTaxIncluded = $shippingCostTaxIncluded;
        $this->productsCount = $productsCount;
        $this->trackingNumber = $trackingNumber;
        $this->shippedAt = $shippedAt;
        $this->deliveredAt = $deliveredAt;
        $this->cancelledAt = $cancelledAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getCarrierSummary(): CarrierSummary
    {
        return $this->carrierSummary;
    }

    public function getAddressId(): int
    {
        return $this->addressId;
    }

    public function getShippingCostTaxExcluded(): DecimalNumber
    {
        return $this->shippingCostTaxExcluded;
    }

    public function getShippingCostTaxIncluded(): DecimalNumber
    {
        return $this->shippingCostTaxIncluded;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function getShippedAt(): ?DateTime
    {
        return $this->shippedAt;
    }

    public function getDeliveredAt(): ?DateTime
    {
        return $this->deliveredAt;
    }

    public function getCancelledAt(): ?DateTime
    {
        return $this->cancelledAt;
    }

    public function getProductsCount(): int
    {
        return $this->productsCount;
    }
}
