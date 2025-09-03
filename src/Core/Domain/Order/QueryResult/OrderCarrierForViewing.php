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

use DateTimeImmutable;

class OrderCarrierForViewing
{
    /**
     * @param string $name  Carrier name or null in case of virtual order
     * @param string $price Price or null in case of virtual order
     */
    public function __construct(
        private readonly int $orderCarrierId,
        private readonly DateTimeImmutable $date,
        private readonly ?string $name,
        private readonly string $weight,
        private readonly int $carrierId,
        private readonly ?string $price,
        private readonly ?string $trackingUrl,
        private readonly ?string $trackingNumber,
        private readonly bool $canEdit,
    ) {
    }

    public function getOrderCarrierId(): int
    {
        return $this->orderCarrierId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function canEdit(): bool
    {
        return $this->canEdit;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }
}
