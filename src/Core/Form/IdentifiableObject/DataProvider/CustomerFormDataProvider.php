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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;

/**
 * Provides data for customer forms
 */
final class CustomerFormDataProvider implements FormDataProviderInterface
{
    /**
     * @param bool $isB2bFeatureEnabled
     */
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ConfigurationInterface $configuration,
        private readonly DefaultGroupsProviderInterface $defaultGroupsProvider,
        private $isB2bFeatureEnabled,
    ) {
    }

    /**
     * @return mixed[]
     */
    public function getData($customerId): array
    {
        /** @var EditableCustomer $editableCustomer */
        $editableCustomer = $this->queryBus->handle(new GetCustomerForEditing((int) $customerId));
        $birthday = $editableCustomer->getBirthday();

        $data = [
            'gender_id' => $editableCustomer->getGenderId(),
            'first_name' => $editableCustomer->getFirstName()->getValue(),
            'last_name' => $editableCustomer->getLastName()->getValue(),
            'email' => $editableCustomer->getEmail()->getValue(),
            'birthday' => $birthday->isEmpty() ? null : $birthday->getValue(),
            'is_enabled' => $editableCustomer->isEnabled(),
            'is_partner_offers_subscribed' => $editableCustomer->isPartnerOffersSubscribed(),
            'group_ids' => $editableCustomer->getGroupIds(),
            'default_group_id' => $editableCustomer->getDefaultGroupId(),
            'is_guest' => $editableCustomer->isGuest(),
        ];

        if ($this->isB2bFeatureEnabled) {
            $data = array_merge($data, [
                'company_name' => $editableCustomer->getCompanyName(),
                'siret_code' => $editableCustomer->getSiretCode(),
                'ape_code' => $editableCustomer->getApeCode(),
                'website' => $editableCustomer->getWebsite(),
                'allowed_outstanding_amount' => $editableCustomer->getAllowedOutstandingAmount(),
                'max_payment_days' => $editableCustomer->getMaxPaymentDays(),
                'risk_id' => $editableCustomer->getRiskId(),
            ]);
        }

        return $data;
    }

    /**
     * @return mixed[]
     */
    public function getDefaultData(): array
    {
        $defaultGroups = $this->defaultGroupsProvider->getGroups();

        $data = [
            'is_enabled' => true,
            'is_partner_offers_subscribed' => false,
            'group_ids' => [
                $defaultGroups->getVisitorsGroup()->getId(),
                $defaultGroups->getGuestsGroup()->getId(),
                $defaultGroups->getCustomersGroup()->getId(),
            ],
            'default_group_id' => (int) $this->configuration->get('PS_CUSTOMER_GROUP'),
            'is_guest' => false,
        ];

        if ($this->isB2bFeatureEnabled) {
            $data = array_merge($data, [
                'allowed_outstanding_amount' => 0,
                'max_payment_days' => 0,
            ]);
        }

        return $data;
    }
}
