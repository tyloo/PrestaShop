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

use PrestaShop\Decimal\DecimalNumber;

class OrderPricesForViewing
{
    public function __construct(
        private readonly DecimalNumber $productsPriceRaw,
        private readonly DecimalNumber $discountsAmountRaw,
        private readonly DecimalNumber $wrappingPriceRaw,
        private readonly DecimalNumber $shippingPriceRaw,
        private readonly DecimalNumber $shippingRefundableAmountRaw,
        private readonly DecimalNumber $taxesAmountRaw,
        private readonly DecimalNumber $totalAmountRaw,
        private readonly string $productsPriceFormatted,
        private readonly string $discountsAmountFormatted,
        private readonly string $wrappingPriceFormatted,
        private readonly string $shippingPriceFormatted,
        private readonly string $shippingRefundableAmountFormatted,
        private readonly string $taxesAmountFormatted,
        private readonly string $totalAmountFormatted,
    ) {
    }

    public function getProductsPriceFormatted(): string
    {
        return $this->productsPriceFormatted;
    }

    public function getDiscountsAmountFormatted(): ?string
    {
        return $this->discountsAmountFormatted;
    }

    public function getWrappingPriceFormatted(): ?string
    {
        return $this->wrappingPriceFormatted;
    }

    public function getShippingPriceFormatted(): ?string
    {
        return $this->shippingPriceFormatted;
    }

    public function getShippingRefundableAmountFormatted(): ?string
    {
        return $this->shippingRefundableAmountFormatted;
    }

    public function getTaxesAmountFormatted(): string
    {
        return $this->taxesAmountFormatted;
    }

    public function getTotalAmountFormatted(): string
    {
        return $this->totalAmountFormatted;
    }

    public function getProductsPriceRaw(): DecimalNumber
    {
        return $this->productsPriceRaw;
    }

    public function getDiscountsAmountRaw(): DecimalNumber
    {
        return $this->discountsAmountRaw;
    }

    public function getWrappingPriceRaw(): DecimalNumber
    {
        return $this->wrappingPriceRaw;
    }

    public function getShippingPriceRaw(): DecimalNumber
    {
        return $this->shippingPriceRaw;
    }

    public function getShippingRefundableAmountRaw(): DecimalNumber
    {
        return $this->shippingRefundableAmountRaw;
    }

    public function getTaxesAmountRaw(): DecimalNumber
    {
        return $this->taxesAmountRaw;
    }

    public function getTotalAmountRaw(): DecimalNumber
    {
        return $this->totalAmountRaw;
    }
}
