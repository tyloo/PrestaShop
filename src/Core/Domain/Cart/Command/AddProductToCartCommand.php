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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Responsible for adding product to cart
 */
class AddProductToCartCommand
{
    private readonly CartId $cartId;

    private readonly ProductId $productId;

    private readonly int $quantity;

    private ?CombinationId $combinationId = null;

    /**
     * @throws CartConstraintException
     */
    public function __construct(
        int $cartId,
        int $productId,
        int $quantity,
        ?int $combinationId = null,
        /**
         * @var array key-value pairs where key is customizationFieldId and value is customization field value
         */
        private readonly array $customizationsByFieldIds = [],
    ) {
        $this->assertQtyIsPositive($quantity);
        $this->setCombinationId($combinationId);
        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->quantity = $quantity;
    }

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    public function getCustomizationsByFieldIds(): array
    {
        return $this->customizationsByFieldIds;
    }

    /**
     * @throws CartConstraintException
     */
    private function assertQtyIsPositive(int $qty): void
    {
        if ($qty <= 0) {
            throw new CartConstraintException(\sprintf('Quantity must be positive integer. "%s" given.', $qty), CartConstraintException::INVALID_QUANTITY);
        }
    }

    private function setCombinationId(?int $combinationId): void
    {
        if ($combinationId !== null) {
            $combinationId = new CombinationId($combinationId);
        }

        $this->combinationId = $combinationId;
    }
}
