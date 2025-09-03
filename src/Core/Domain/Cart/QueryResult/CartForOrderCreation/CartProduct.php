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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds data of cart product information
 */
class CartProduct
{
    public function __construct(
        private readonly int $productId,
        private readonly int $attributeId,
        private readonly string $name,
        private readonly string $attribute,
        private readonly string $reference,
        private readonly string $unitPrice,
        private readonly int $quantity,
        private readonly string $price,
        private readonly string $imageLink,
        private readonly ?Customization $customization,
        private readonly int $availableStock,
        private readonly bool $availableOutOfStock,
        private readonly bool $isGift = false,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getCustomization(): ?Customization
    {
        return $this->customization;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }

    public function isAvailableOutOfStock(): bool
    {
        return $this->availableOutOfStock;
    }

    public function isGift(): bool
    {
        return $this->isGift;
    }
}
