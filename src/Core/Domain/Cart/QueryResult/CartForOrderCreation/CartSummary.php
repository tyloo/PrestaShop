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
 * Holds cart summary data
 */
class CartSummary
{
    public function __construct(
        private readonly string $totalProductsPrice,
        private readonly string $totalDiscount,
        private readonly string $totalShippingPrice,
        private readonly string $totalShippingWithoutTaxes,
        private readonly string $totalTaxes,
        private readonly string $totalPriceWithTaxes,
        private readonly string $totalPriceWithoutTaxes,
        private readonly string $orderMessage,
        private readonly string $processOrderLink,
    ) {
    }

    public function getTotalProductsPrice(): string
    {
        return $this->totalProductsPrice;
    }

    public function getTotalDiscount(): string
    {
        return $this->totalDiscount;
    }

    public function getTotalShippingPrice(): string
    {
        return $this->totalShippingPrice;
    }

    public function getTotalShippingWithoutTaxes(): string
    {
        return $this->totalShippingWithoutTaxes;
    }

    public function getTotalTaxes(): string
    {
        return $this->totalTaxes;
    }

    public function getTotalPriceWithTaxes(): string
    {
        return $this->totalPriceWithTaxes;
    }

    public function getTotalPriceWithoutTaxes(): string
    {
        return $this->totalPriceWithoutTaxes;
    }

    public function getProcessOrderLink(): string
    {
        return $this->processOrderLink;
    }

    public function getOrderMessage(): string
    {
        return $this->orderMessage;
    }
}
