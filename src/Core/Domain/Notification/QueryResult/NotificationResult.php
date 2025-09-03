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

namespace PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult;

/**
 * NotificationResult contains the notification data
 */
class NotificationResult
{
    /**
     * @var string
     */
    protected $customerThreadViewUrl;

    /**
     * @var string
     */
    protected $orderViewUrl;

    public function __construct(
        private readonly int $orderId,
        private readonly int $customerId,
        private readonly string $customerName,
        private readonly int $customerMessageId,
        private readonly int $customerThreadId,
        private readonly string $customerViewUrl,
        private readonly string $totalPaid,
        private readonly string $carrier,
        private readonly string $isoCode,
        private readonly string $company,
        private readonly string $status,
        private readonly string $dateAdd,
        string $customerThreadViewUrl,
        string $orderViewUrl,
    ) {
        $this->customerThreadViewUrl = $customerThreadViewUrl;
        $this->orderViewUrl = $orderViewUrl;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerMessageId(): int
    {
        return $this->customerMessageId;
    }

    public function getCustomerThreadId(): int
    {
        return $this->customerThreadId;
    }

    public function getCustomerViewUrl(): string
    {
        return $this->customerViewUrl;
    }

    public function getTotalPaid(): string
    {
        return $this->totalPaid;
    }

    public function getCarrier(): string
    {
        return $this->carrier;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDateAdd(): string
    {
        return $this->dateAdd;
    }

    public function getCustomerThreadViewUrl(): string
    {
        return $this->customerThreadViewUrl;
    }

    public function getOrderViewUrl(): string
    {
        return $this->orderViewUrl;
    }
}
