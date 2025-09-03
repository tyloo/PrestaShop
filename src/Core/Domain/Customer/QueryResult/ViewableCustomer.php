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

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Class CustomerInformation stores customer information for viewing in Back Office.
 */
class ViewableCustomer
{
    /**
     * @param CartInformation[]           $cartsInformation
     * @param MessageInformation[]        $messagesInformation
     * @param DiscountInformation[]       $discountsInformation
     * @param SentEmailInformation[]      $sentEmailsInformation
     * @param LastConnectionInformation[] $lastConnectionsInformation
     * @param GroupInformation[]          $groupsInformation
     * @param AddressInformation[]        $addressesInformation
     */
    public function __construct(
        private readonly CustomerId $customerId,
        private readonly GeneralInformation $generalInformation,
        private readonly PersonalInformation $personalInformation,
        private readonly OrdersInformation $ordersInformation,
        /**
         * @deprecated Since 9.0.0 for performance reasons and returns only empty array.
         */
        private readonly array $cartsInformation,
        /**
         * @deprecated Since 9.0.0, returns empty ProductsInformation object with no data.
         */
        private readonly ProductsInformation $productsInformation,
        private readonly array $messagesInformation,
        /**
         * @deprecated Since 9.0.0, returns only empty array.
         */
        private readonly array $discountsInformation,
        private readonly array $sentEmailsInformation,
        private readonly array $lastConnectionsInformation,
        private readonly array $groupsInformation,
        /**
         * @deprecated Since 9.0.0, returns only empty array.
         */
        private readonly array $addressesInformation,
    ) {
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getPersonalInformation(): PersonalInformation
    {
        return $this->personalInformation;
    }

    public function getOrdersInformation(): OrdersInformation
    {
        return $this->ordersInformation;
    }

    /**
     * @deprecated Since 9.0.0 for performance reasons and returns only empty array.
     *
     * @return CartInformation[]
     */
    public function getCartsInformation(): array
    {
        return $this->cartsInformation;
    }

    /**
     * @deprecated Since 9.0.0, returns empty ProductsInformation object with no data.
     */
    public function getProductsInformation(): ProductsInformation
    {
        return $this->productsInformation;
    }

    /**
     * @return MessageInformation[]
     */
    public function getMessagesInformation(): array
    {
        return $this->messagesInformation;
    }

    /**
     * @deprecated Since 9.0.0, returns only empty array.
     *
     * @return DiscountInformation[]
     */
    public function getDiscountsInformation(): array
    {
        return $this->discountsInformation;
    }

    /**
     * @return SentEmailInformation[]
     */
    public function getSentEmailsInformation(): array
    {
        return $this->sentEmailsInformation;
    }

    /**
     * @return LastConnectionInformation[]
     */
    public function getLastConnectionsInformation(): array
    {
        return $this->lastConnectionsInformation;
    }

    /**
     * @return GroupInformation[]
     */
    public function getGroupsInformation(): array
    {
        return $this->groupsInformation;
    }

    /**
     * @deprecated Since 9.0.0, returns only empty array.
     *
     * @return AddressInformation[]
     */
    public function getAddressesInformation(): array
    {
        return $this->addressesInformation;
    }

    public function getGeneralInformation(): GeneralInformation
    {
        return $this->generalInformation;
    }
}
