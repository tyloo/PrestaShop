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

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerPermission;

class ConfigurablePermissions
{
    /**
     * @param string[] $permissions
     */
    public function __construct(
        private readonly array $profilePermissionsForTabs,
        private readonly array $profilePermissionsForModules,
        private readonly array $profiles,
        private readonly array $tabs,
        private array $bulkConfiguration,
        private readonly array $permissions,
        private readonly int $employeeProfileId,
        private readonly bool $hasEmployeeEditPermission,
    ) {
    }

    public function getProfilePermissionsForTabs(): array
    {
        return $this->profilePermissionsForTabs;
    }

    public function getProfiles(): array
    {
        return $this->profiles;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function isBulkViewConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::VIEW];
    }

    public function isBulkAddConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::ADD];
    }

    public function isBulkEditConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::EDIT];
    }

    public function isBulkDeleteConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::DELETE];
    }

    public function isBulkAllConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::ALL];
    }

    public function getProfilePermissionsForModules(): array
    {
        return $this->profilePermissionsForModules;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getEmployeeProfileId(): int
    {
        return $this->employeeProfileId;
    }

    public function hasEmployeeEditPermission(): bool
    {
        return $this->hasEmployeeEditPermission;
    }
}
