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

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult;

use DateTimeImmutable;

class OrderReturnForEditing
{
    public function __construct(
        private readonly int $orderReturnId,
        private readonly int $customerId,
        private readonly string $customerFirstName,
        private readonly string $customerLastName,
        private readonly int $orderId,
        private readonly DateTimeImmutable $orderDate,
        private readonly int $orderReturnStateId,
        private readonly string $question,
    ) {
    }

    public function getOrderReturnId(): int
    {
        return $this->orderReturnId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getOrderReturnStateId(): int
    {
        return $this->orderReturnStateId;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getCustomerFullName(): string
    {
        return \sprintf('%s %s', $this->customerFirstName, $this->customerLastName);
    }

    public function getCustomerFirstName(): string
    {
        return $this->customerFirstName;
    }

    public function getCustomerLastName(): string
    {
        return $this->customerLastName;
    }

    public function getOrderDate(): DateTimeImmutable
    {
        return $this->orderDate;
    }
}
