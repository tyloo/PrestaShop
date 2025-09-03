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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Holds data of summarized order
 */
class OrderSummary
{
    public function __construct(
        private readonly int $orderId,
        private readonly string $orderPlacedDate,
        private readonly string $paymentMethodName,
        private readonly string $orderStatus,
        private readonly int $orderProductsCount,
        private readonly string $totalPaid,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getOrderPlacedDate(): string
    {
        return $this->orderPlacedDate;
    }

    public function getPaymentMethodName(): string
    {
        return $this->paymentMethodName;
    }

    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    public function getOrderProductsCount(): int
    {
        return $this->orderProductsCount;
    }

    public function getTotalPaid(): string
    {
        return $this->totalPaid;
    }
}
