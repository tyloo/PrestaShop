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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Edit employee with given data.
 */
class EditEmployeeCommand
{
    private EmployeeId $employeeId;

    private ?FirstName $firstName = null;

    private ?LastName $lastName = null;

    private ?Email $email = null;

    /**
     * @var int
     */
    private $defaultPageId;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var int
     */
    private $profileId;

    /**
     * @var array
     */
    private $shopAssociation;

    private ?Password $plainPassword = null;

    private bool $hasEnabledGravatar = false;

    /**
     * @param int $employeeId
     */
    public function __construct($employeeId)
    {
        $this->employeeId = new EmployeeId((int) $employeeId);
    }

    public function getEmployeeId(): EmployeeId
    {
        return $this->employeeId;
    }

    public function setEmployeeId(EmployeeId $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getFirstName(): ?FirstName
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName): static
    {
        $this->firstName = new FirstName($firstName);

        return $this;
    }

    public function getLastName(): ?LastName
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName): static
    {
        $this->lastName = new LastName($lastName);

        return $this;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): static
    {
        $this->email = new Email($email);

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPageId()
    {
        return $this->defaultPageId;
    }

    /**
     * @param int $defaultPageId
     */
    public function setDefaultPageId($defaultPageId): static
    {
        $this->defaultPageId = $defaultPageId;

        return $this;
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @param int $languageId
     */
    public function setLanguageId($languageId): static
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param int $profileId
     */
    public function setProfileId($profileId): static
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param array $shopAssociation
     */
    public function setShopAssociation($shopAssociation): static
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    public function getPlainPassword(): ?Password
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword, int $minLength, int $maxLength, int $minScore): static
    {
        $this->plainPassword = new Password($plainPassword, $minLength, $maxLength, $minScore);

        return $this;
    }

    public function hasEnabledGravatar(): bool
    {
        return $this->hasEnabledGravatar;
    }

    public function setHasEnabledGravatar(bool $hasEnabledGravatar): static
    {
        $this->hasEnabledGravatar = $hasEnabledGravatar;

        return $this;
    }
}
