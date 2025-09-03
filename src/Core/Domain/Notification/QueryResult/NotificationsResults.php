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
 * NotificationsResults is a collection of NotificationsResult
 */
class NotificationsResults
{
    /**
     * @param NotificationsResult[] $notifications
     */
    public function __construct(
        private readonly array $notifications,
    ) {
    }

    /**
     * @return NotificationsResult[]
     */
    public function getNotificationsResults(): array
    {
        return $this->notifications;
    }

    /**
     * @return array{total: mixed, results: list<array{id_order: mixed, id_customer: mixed, id_customer_message: mixed, id_customer_thread: mixed, total_paid: mixed, carrier: mixed, iso_code: mixed, company: mixed, status: mixed, customer_name: mixed, date_add: mixed, customer_view_url: mixed, customer_thread_view_url: mixed, order_view_url: mixed}>}[]
     */
    public function getNotificationsResultsForJS(): array
    {
        $response = [];
        foreach ($this->getNotificationsResults() as $element) {
            $notifications = [];
            foreach ($element->getNotifications() as $notification) {
                $notifications[] = [
                    'id_order' => $notification->getOrderId(),
                    'id_customer' => $notification->getCustomerId(),
                    'id_customer_message' => $notification->getCustomerMessageId(),
                    'id_customer_thread' => $notification->getCustomerThreadId(),
                    'total_paid' => $notification->getTotalPaid(),
                    'carrier' => $notification->getCarrier(),
                    'iso_code' => $notification->getIsoCode(),
                    'company' => $notification->getCompany(),
                    'status' => $notification->getStatus(),
                    'customer_name' => $notification->getCustomerName(),
                    'date_add' => $notification->getDateAdd(),
                    'customer_view_url' => $notification->getCustomerViewUrl(),
                    'customer_thread_view_url' => $notification->getCustomerThreadViewUrl(),
                    'order_view_url' => $notification->getOrderViewUrl(),
                ];
            }

            $response[$element->getType()->getValue()] = [
                'total' => $element->getTotal(),
                'results' => $notifications,
            ];
        }

        return $response;
    }
}
