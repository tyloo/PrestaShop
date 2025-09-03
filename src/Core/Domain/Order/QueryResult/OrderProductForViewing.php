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

use JsonSerializable;

class OrderProductForViewing implements JsonSerializable
{
    public const TYPE_PACK = 'pack';

    public const TYPE_PRODUCT_WITH_COMBINATIONS = 'product_with_combinations';

    public const TYPE_PRODUCT_WITHOUT_COMBINATIONS = 'product_without_combinations';

    public function __construct(
        /**
         * @var int
         */
        private readonly ?int $orderDetailId,
        private readonly int $id,
        private readonly int $combinationId,
        private readonly string $name,
        private readonly string $reference,
        private readonly string $supplierReference,
        private readonly int $quantity,
        private readonly string $unitPrice,
        private readonly string $totalPrice,
        private readonly int $availableQuantity,
        private readonly ?string $imagePath,
        private readonly string $unitPriceTaxExclRaw,
        private readonly string $unitPriceTaxInclRaw,
        private readonly string $taxRate,
        private readonly string $amountRefunded,
        private readonly int $quantityRefunded,
        private readonly string $amountRefundable,
        private readonly string $amountRefundableRaw,
        private readonly string $location,
        /**
         * @var int
         */
        private readonly ?int $orderInvoiceId,
        private readonly string $orderInvoiceNumber,
        private readonly string $type,
        private readonly bool $availableOutOfStock,
        /**
         * @var OrderProductForViewing[]
         */
        private readonly array $packItems = [],
        /**
         * @var OrderProductCustomizationsForViewing
         */
        private readonly ?OrderProductCustomizationsForViewing $customizations = null,
        private readonly string $mpn = '',
    ) {
    }

    /**
     * Get product's order detail ID
     */
    public function getOrderDetailId(): ?int
    {
        return $this->orderDetailId;
    }

    /**
     * Get product ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * Get product's name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OrderProductForViewing[]
     */
    public function getPackItems(): array
    {
        return $this->packItems;
    }

    /**
     * Product reference
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Get product's supplier reference
     */
    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    /**
     * get tax rate to be applied on this product
     */
    public function getTaxRate(): string
    {
        return $this->taxRate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get product's location
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Get product's quantity
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Get product's unit price
     */
    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    /**
     * Get product's formatted total price
     */
    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    /**
     * Get available quantity for this product
     */
    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }

    /**
     * Get image path for this product
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * Get unit price without taxes
     */
    public function getUnitPriceTaxExclRaw(): string
    {
        return $this->unitPriceTaxExclRaw;
    }

    /**
     * Get unit price including taxes
     */
    public function getUnitPriceTaxInclRaw(): string
    {
        return $this->unitPriceTaxInclRaw;
    }

    /**
     * How much (money) has already been refunded for this product
     */
    public function getAmountRefunded(): string
    {
        return $this->amountRefunded;
    }

    /**
     * How many (quantity) of this product has already been refunded
     */
    public function getQuantityRefunded(): int
    {
        return $this->quantityRefunded;
    }

    /**
     * How much (money) can be refunded for this product (formatted for display)
     */
    public function getAmountRefundable(): string
    {
        return $this->amountRefundable;
    }

    /**
     * How much (money) can be refunded for this product
     */
    public function getAmountRefundableRaw(): string
    {
        return $this->amountRefundableRaw;
    }

    /**
     * How many (quantity) of this product can be refunded
     */
    public function getQuantityRefundable(): int
    {
        return $this->quantity - $this->quantityRefunded;
    }

    /**
     * Can this product be refunded
     */
    public function isRefundable(): bool
    {
        if ($this->quantity <= $this->quantityRefunded) {
            return false;
        }

        return true;
    }

    /**
     * Get the id of this product's invoice
     */
    public function getOrderInvoiceId(): ?int
    {
        return $this->orderInvoiceId;
    }

    /**
     * Get the number (reference) of this product's invoice
     */
    public function getOrderInvoiceNumber(): string
    {
        return $this->orderInvoiceNumber;
    }

    /**
     * Get customizations of this product
     */
    public function getCustomizations(): ?OrderProductCustomizationsForViewing
    {
        return $this->customizations;
    }

    public function isAvailableOutOfStock(): bool
    {
        return $this->availableOutOfStock;
    }

    /**
     * Get product MPN
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'orderDetailId' => $this->getOrderDetailId(),
            'name' => $this->getName(),
            'reference' => $this->getReference(),
            'supplierReference' => $this->getSupplierReference(),
            'mpn' => $this->getMpn(),
            'location' => $this->getLocation(),
            'imagePath' => $this->getImagePath(),
            'quantity' => $this->getQuantity(),
            'availableQuantity' => $this->getAvailableQuantity(),
            'unitPrice' => $this->getUnitPrice(),
            'unitPriceTaxExclRaw' => $this->getUnitPriceTaxExclRaw(),
            'unitPriceTaxInclRaw' => $this->getUnitPriceTaxInclRaw(),
            'totalPrice' => $this->getTotalPrice(),
            'taxRate' => $this->getTaxRate(),
            'type' => $this->getType(),
            'packItems' => $this->getPackItems(),
        ];
    }
}
