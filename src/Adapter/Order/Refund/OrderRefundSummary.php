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

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use OrderDetail;

/**
 * Container of all the necessary information for an order refund
 */
class OrderRefundSummary
{
    public function __construct(
        private array $orderDetails,
        private readonly array $productRefunds,
        private readonly float $refundedAmount,
        private readonly float $refundedShipping,
        private readonly float $voucherAmount,
        private readonly bool $voucherChosen,
        private readonly bool $isTaxIncluded,
        private readonly int $precision,
    ) {
    }

    /**
     * @return OrderDetail[]
     */
    public function getOrderDetails(): array
    {
        return $this->orderDetails;
    }

    public function getProductRefunds(): array
    {
        return $this->productRefunds;
    }

    public function getRefundedAmount(): float
    {
        return $this->refundedAmount;
    }

    public function getRefundedShipping(): float
    {
        return $this->refundedShipping;
    }

    public function getVoucherAmount(): float
    {
        return $this->voucherAmount;
    }

    public function isVoucherChosen(): bool
    {
        return $this->voucherChosen;
    }

    public function isTaxIncluded(): bool
    {
        return $this->isTaxIncluded;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function getOrderDetailById(int $orderDetailId): ?OrderDetail
    {
        return $this->orderDetails[$orderDetailId] ?? null;
    }
}
