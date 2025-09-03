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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult;

/**
 * Transfers product supplier for editing data
 */
class ProductSupplierForEditing
{
    /**
     * @param int    $productSupplierId ProductSupplier entity record id
     * @param int    $productId         the associated product id
     * @param int    $supplierId        the associated supplier id
     * @param string $reference         the reference for this product supplier
     */
    public function __construct(
        private readonly int $productSupplierId,
        private readonly int $productId,
        private readonly int $supplierId,
        private readonly string $supplierName,
        private readonly string $reference,
        private readonly string $priceTaxExcluded,
        private readonly int $currencyId,
        private readonly ?int $combinationId = null,
    ) {
    }

    public function getProductSupplierId(): int
    {
        return $this->productSupplierId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getPriceTaxExcluded(): string
    {
        return $this->priceTaxExcluded;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getCombinationId(): ?int
    {
        return $this->combinationId;
    }
}
