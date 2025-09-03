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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSL;
use PrestaShop\PrestaShop\Core\Security\PasswordGenerator;

/**
 * Saves or updates customer data submitted in form
 */
final class CustomerFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @param int  $contextShopId
     * @param bool $isB2bFeatureEnabled
     */
    public function __construct(
        private readonly CommandBusInterface $bus,
        private $contextShopId,
        private $isB2bFeatureEnabled,
        private readonly DefaultGroupsProviderInterface $defaultGroupsProvider,
    ) {
    }

    public function create(array $data)
    {
        $command = $this->buildCustomerAddCommandFromFormData($data);

        /** @var CustomerId $customerId */
        $customerId = $this->bus->handle($command);

        return $customerId->getValue();
    }

    public function update($customerId, array $data): void
    {
        $command = $this->buildCustomerEditCommand($customerId, $data);

        $this->bus->handle($command);
    }

    private function buildCustomerAddCommandFromFormData(array $data): AddCustomerCommand
    {
        // Default data from the form
        $password = $data['password'];
        $defaultGroupId = (int) $data['default_group_id'];
        $groupIds = array_map(fn ($groupId): int => (int) $groupId, $data['group_ids']);
        $isEnabled = (bool) $data['is_enabled'];

        /*
         * If a guest is created, we will alter the data a bit.
         * The data should already come correct from the form, but we can't trust the JS.
         *
         * Difference between a customer and a guest:
         * - Password is randomly generated.
         * - He is always enabled.
         * - His default group is the default GUEST group and he should belong to this group.
         */
        if ($data['is_guest']) {
            $password = (new PasswordGenerator(new OpenSSL()))->generatePassword(16, 'RANDOM');
            $guestGroupId = $this->defaultGroupsProvider->getGroups()->getGuestsGroup()->getId();
            $defaultGroupId = $guestGroupId;
            $groupIds = [$guestGroupId];
            $isEnabled = true;
        }

        $command = new AddCustomerCommand(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $password,
            $defaultGroupId,
            $groupIds,
            $this->contextShopId,
            (int) $data['gender_id'],
            $isEnabled,
            (bool) $data['is_partner_offers_subscribed'],
            $data['birthday'] ?: Birthday::EMPTY_BIRTHDAY,
            (bool) $data['is_guest']
        );

        // Optional data processed only if B2B mode is enabled
        if ($this->isB2bFeatureEnabled) {
            $command
                ->setCompanyName((string) $data['company_name'])
                ->setSiretCode((string) $data['siret_code'])
                ->setApeCode((string) $data['ape_code'])
                ->setWebsite((string) $data['website'])
                ->setAllowedOutstandingAmount((float) $data['allowed_outstanding_amount'])
                ->setMaxPaymentDays((int) $data['max_payment_days'])
                ->setRiskId((int) $data['risk_id'])
            ;
        }

        return $command;
    }

    /**
     * @param int $customerId
     *
     * @return EditCustomerCommand
     */
    private function buildCustomerEditCommand($customerId, array $data)
    {
        $groupIds = array_map(fn ($groupId): int => (int) $groupId, $data['group_ids']);

        $command = (new EditCustomerCommand($customerId))
            ->setGenderId($data['gender_id'])
            ->setEmail($data['email'])
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setIsEnabled($data['is_enabled'])
            ->setIsPartnerOffersSubscribed($data['is_partner_offers_subscribed'])
            ->setDefaultGroupId((int) $data['default_group_id'])
            ->setGroupIds($groupIds)
            ->setBirthday($data['birthday'] ?: Birthday::EMPTY_BIRTHDAY)
        ;

        if ($data['password'] !== null) {
            $command->setPassword($data['password']);
        }

        if ($this->isB2bFeatureEnabled) {
            $command
                ->setCompanyName((string) $data['company_name'])
                ->setSiretCode((string) $data['siret_code'])
                ->setApeCode((string) $data['ape_code'])
                ->setWebsite((string) $data['website'])
                ->setAllowedOutstandingAmount((float) $data['allowed_outstanding_amount'])
                ->setMaxPaymentDays((int) $data['max_payment_days'])
                ->setRiskId((int) $data['risk_id'])
            ;
        }

        return $command;
    }
}
