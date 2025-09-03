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
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product quantity in cart
 * Quantity given must include gift product
 */
class UpdateProductQuantityInCartCommand
{
    private readonly CartId $cartId;

    private readonly ProductId $productId;

    private readonly int $newQuantity;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var CustomizationId|null
     */
    private $customizationId;

    /**
     * @param int $cartId
     * @param int $productId
     *
     * @throws CartConstraintException
     * @throws CartException
     */
    public function __construct(
        $cartId,
        $productId,
        int $quantity,
        ?int $combinationId = null,
        ?int $customizationId = null,
    ) {
        $this->setCombinationId($combinationId);
        $this->setCustomizationId($customizationId);
        $this->assertQuantityIsPositive($quantity);

        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->newQuantity = $quantity;
    }

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getNewQuantity(): int
    {
        return $this->newQuantity;
    }

    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    public function getCustomizationId(): ?CustomizationId
    {
        return $this->customizationId;
    }

    private function setCombinationId(?int $combinationId): void
    {
        if ($combinationId !== null) {
            $combinationId = new CombinationId($combinationId);
        }

        $this->combinationId = $combinationId;
    }

    private function setCustomizationId(?int $customizationId): void
    {
        if ($customizationId !== null) {
            $customizationId = new CustomizationId($customizationId);
        }

        $this->customizationId = $customizationId;
    }

    /**
     * @throws CartConstraintException
     */
    private function assertQuantityIsPositive(int $qty): void
    {
        if ($qty <= 0) {
            throw new CartConstraintException(\sprintf('Quantity must be positive integer. "%s" given.', $qty), CartConstraintException::INVALID_QUANTITY);
        }
    }
}
