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

class OrderReturnForViewing
{
    public function __construct(
        private readonly int $idOrderReturn,
        private readonly int $orderInvoiceId,
        private readonly DateTimeImmutable $date,
        private readonly string $type,
        private readonly string $stateName,
        private readonly string $orderReturnNumber,
    ) {
    }

    public function getOrderInvoiceId(): int
    {
        return $this->orderInvoiceId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStateName(): string
    {
        return $this->stateName;
    }

    public function getOrderReturnNumber(): string
    {
        return $this->orderReturnNumber;
    }

    public function getIdOrderReturn(): int
    {
        return $this->idOrderReturn;
    }
}
