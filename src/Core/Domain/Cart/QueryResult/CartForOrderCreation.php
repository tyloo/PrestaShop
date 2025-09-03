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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartRule;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartShipping;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartSummary;

/**
 * Holds cart information data
 */
class CartForOrderCreation
{
    /**
     * @param CartRule[]    $cartRules
     * @param CartAddress[] $addresses
     */
    public function __construct(
        private readonly int $cartId,
        /**
         * @var CartProduct[]
         */
        private readonly array $products,
        private readonly int $currencyId,
        private readonly int $langId,
        private readonly array $cartRules,
        private readonly array $addresses,
        private readonly CartSummary $summary,
        private readonly ?CartShipping $shipping = null,
    ) {
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    /**
     * @return CartProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getLangId(): int
    {
        return $this->langId;
    }

    /**
     * @return CartRule[]
     */
    public function getCartRules(): array
    {
        return $this->cartRules;
    }

    /**
     * @return CartAddress[]
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    public function getShipping(): ?CartShipping
    {
        return $this->shipping;
    }

    public function getSummary(): CartSummary
    {
        return $this->summary;
    }
}
