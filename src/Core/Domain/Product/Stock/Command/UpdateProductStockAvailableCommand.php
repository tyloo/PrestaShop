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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\CommandHandler\UpdateProductStockHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates product stock properties which are in a dedicated StockAvailable entity
 *
 * @see UpdateProductStockHandlerInterface
 */
class UpdateProductStockAvailableCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var int|null
     */
    private $deltaQuantity;

    /**
     * @var OutOfStockType|null
     */
    private $outOfStockType;

    /**
     * @var string|null
     */
    private $location;

    public function __construct(
        int $productId,
        ShopConstraint $shopConstraint,
    ) {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function getDeltaQuantity(): ?int
    {
        return $this->deltaQuantity;
    }

    public function setDeltaQuantity(int $deltaQuantity): self
    {
        $this->deltaQuantity = $deltaQuantity;

        return $this;
    }

    public function getOutOfStockType(): ?OutOfStockType
    {
        return $this->outOfStockType;
    }

    /**
     * @return $this
     *
     * @throws ProductStockConstraintException
     */
    public function setOutOfStockType(int $outOfStockType): self
    {
        $this->outOfStockType = new OutOfStockType($outOfStockType);

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }
}
