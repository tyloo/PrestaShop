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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Stores editable data of an employee.
 */
class EditableEmployee
{
    /**
     * @param string $avatarUrl
     * @param int    $defaultPageId
     * @param int    $languageId
     * @param bool   $active
     * @param int    $profileId
     */
    public function __construct(
        private readonly EmployeeId $employeeId,
        private readonly FirstName $firstName,
        private readonly LastName $lastName,
        private readonly Email $email,
        private $avatarUrl,
        private $defaultPageId,
        private $languageId,
        private $active,
        private $profileId,
        private readonly array $shopAssociation,
        private readonly bool $hasEnabledGravatar = false,
    ) {
    }

    public function getEmployeeId(): EmployeeId
    {
        return $this->employeeId;
    }

    public function getFirstName(): FirstName
    {
        return $this->firstName;
    }

    public function getLastName(): LastName
    {
        return $this->lastName;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getDefaultPageId()
    {
        return $this->defaultPageId;
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    public function hasEnabledGravatar(): bool
    {
        return $this->hasEnabledGravatar;
    }
}
