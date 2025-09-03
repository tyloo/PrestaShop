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

/**
 * DTO for order product details
 */
class OrderPreviewProductDetail
{
    public function __construct(
        private readonly string $name,
        private readonly string $reference,
        private readonly string $location,
        private readonly int $quantity,
        private readonly string $unitPrice,
        private readonly string $totalPrice,
        private readonly string $totalTax,
        private readonly int $id,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalTax(): string
    {
        return $this->totalTax;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
