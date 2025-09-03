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

namespace PrestaShop\PrestaShop\Core\Payment;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\Configuration\PaymentRestrictionsConfiguratorInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;

/**
 * Class PaymentModulePreferencesConfiguration is responsible for configuring payment module restrictions.
 */
final class PaymentModulePreferencesConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly PaymentModuleListProviderInterface $paymentModuleProvider,
        private readonly PaymentRestrictionsConfiguratorInterface $paymentRestrictionsConfigurator,
    ) {
    }

    /**
     * @return array{}|array{currency_restrictions: non-empty-array, country_restrictions: non-empty-array, group_restrictions: non-empty-array, carrier_restrictions: non-empty-array}
     */
    public function getConfiguration(): array
    {
        $config = [];
        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();

        foreach ($paymentModules as $paymentModule) {
            $config['currency_restrictions'][$paymentModule->get('name')] = $paymentModule->get('currencies');
            $config['country_restrictions'][$paymentModule->get('name')] = $paymentModule->get('countries');
            $config['group_restrictions'][$paymentModule->get('name')] = $paymentModule->get('groups');
            $config['carrier_restrictions'][$paymentModule->get('name')] = $paymentModule->get('carriers');
        }

        return $config;
    }

    public function updateConfiguration(array $config): array
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->paymentRestrictionsConfigurator->configureCurrencyRestrictions($config['currency_restrictions']);
            $this->paymentRestrictionsConfigurator->configureCountryRestrictions($config['country_restrictions']);
            $this->paymentRestrictionsConfigurator->configureGroupRestrictions($config['group_restrictions']);
            $this->paymentRestrictionsConfigurator->configureCarrierRestrictions($config['carrier_restrictions']);
        }

        return $errors;
    }

    public function validateConfiguration(array $config)
    {
        return isset(
            $config['currency_restrictions'],
            $config['country_restrictions'],
            $config['group_restrictions'],
            $config['carrier_restrictions']
        );
    }
}
