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

namespace PrestaShop\PrestaShop\Core\Module\Configuration;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;

/**
 * Class PaymentRestrictionsConfigurator is responsible for configuring restrictions for payment modules.
 */
final class PaymentRestrictionsConfigurator implements PaymentRestrictionsConfiguratorInterface
{
    /**
     * @param string $databasePrefix
     * @param int    $shopId
     */
    public function __construct(
        private readonly Connection $connection,
        private $databasePrefix,
        private $shopId,
        private readonly PaymentModuleListProviderInterface $paymentModuleProvider,
    ) {
    }

    public function configureCurrencyRestrictions(array $currencyRestrictions): void
    {
        $this->configureRestrictions('currency', $currencyRestrictions);
    }

    public function configureCountryRestrictions(array $countryRestrictions): void
    {
        $this->configureRestrictions('country', $countryRestrictions);
    }

    public function configureGroupRestrictions(array $groupRestrictions): void
    {
        $this->configureRestrictions('group', $groupRestrictions);
    }

    public function configureCarrierRestrictions(array $carrierRestrictions): void
    {
        $this->configureRestrictions('carrier', $carrierRestrictions);
    }

    private function configureRestrictions(string $restrictionType, array $restrictions): void
    {
        [$moduleIds, $newConfiguration] = $this->parseRestrictionData($restrictions);

        $this->clearCurrentConfiguration($restrictionType, $moduleIds);
        $this->insertNewConfiguration($restrictionType, $newConfiguration);
    }

    /**
     * Clear current configuration for given restriction type.
     *
     * @param int[] $moduleIds
     */
    private function clearCurrentConfiguration(string $restrictionType, array $moduleIds): int
    {
        $clearSql = '
            DELETE FROM ' . $this->getTableNameForRestriction($restrictionType) . '
            WHERE id_shop = ' . (int) $this->shopId . ' AND id_module IN (' . implode(',', array_map('intval', $moduleIds)) . ')
        ';

        return $this->connection->executeUpdate($clearSql);
    }

    /**
     * Insert new configuration for given restriction type.
     *
     * @param array $newConfiguration
     */
    private function insertNewConfiguration(string $restrictionType, $newConfiguration): void
    {
        if (! empty($newConfiguration)) {
            $fieldName = $restrictionType === 'carrier' ? 'reference' : $restrictionType;

            $this->connection->executeUpdate('
                INSERT INTO `' . $this->getTableNameForRestriction($restrictionType) . '`
                (`id_module`, `id_shop`, `id_' . $fieldName . '`)
                VALUES ' . implode(',', $newConfiguration));
        }
    }

    /**
     * Get table name for module restrictions.
     */
    private function getTableNameForRestriction(string $restrictionType): string
    {
        return $this->databasePrefix . 'module_' . $restrictionType;
    }

    /**
     * Parse data from restrictions.
     */
    private function parseRestrictionData(array $restrictions): array
    {
        $moduleIds = [];
        $insertValues = [];

        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();

        foreach ($restrictions as $moduleName => $restriction) {
            if (isset($paymentModules[$moduleName])) {
                $moduleId = $paymentModules[$moduleName]->database->get('id');

                $moduleIds[] = $moduleId;

                if (! \is_array($restriction)) {
                    $restriction = [$restriction];
                }

                foreach ($restriction as $restrictionValues) {
                    $insertValues[] = '(' . (int) $moduleId . ', ' . (int) $this->shopId . ', ' . (int) $restrictionValues . ')';
                }
            }
        }

        return [
            $moduleIds,
            $insertValues,
        ];
    }
}
