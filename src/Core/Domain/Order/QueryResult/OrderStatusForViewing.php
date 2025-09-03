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

use DateTimeImmutable;

class OrderStatusForViewing
{
    public function __construct(
        private readonly int $orderHistoryId,
        private readonly int $orderStatusId,
        private readonly string $name,
        private readonly string $color,
        private readonly DateTimeImmutable $createdAt,
        private readonly bool $withEmail,
        /**
         * @var string|null First name of employee who updated order status or null otherwise
         */
        private readonly ?string $employeeFirstName,
        /**
         * @var string|null Last name of employee who updated order status or null otherwise
         */
        private readonly ?string $employeeLastName,
        private readonly ?string $apiClientId,
    ) {
    }

    public function getOrderHistoryId(): int
    {
        return $this->orderHistoryId;
    }

    public function getOrderStatusId(): int
    {
        return $this->orderStatusId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function withEmail(): bool
    {
        return $this->withEmail;
    }

    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
    }

    public function getApiClientId(): ?string
    {
        return $this->apiClientId;
    }
}
