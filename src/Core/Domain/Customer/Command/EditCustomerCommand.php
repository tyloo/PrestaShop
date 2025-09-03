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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Edits provided customer.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing customer.
 * For example, if the email is null, then the original value is not modified,
 * however, if email is set, then the original value will be overwritten.
 */
class EditCustomerCommand
{
    private readonly CustomerId $customerId;

    private ?FirstName $firstName = null;

    private ?LastName $lastName = null;

    private ?Email $email = null;

    private ?Password $password = null;

    /**
     * @var int|null
     */
    private $defaultGroupId;

    /**
     * @var int[]|null
     */
    private ?array $groupIds = null;

    /**
     * @var int|null
     */
    private $genderId;

    /**
     * @var bool|null
     */
    private $isNewsletterSubscribed;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool|null
     */
    private $isPartnerOffersSubscribed;

    private ?Birthday $birthday = null;

    /**
     * @var string|null
     */
    private $companyName;

    /**
     * @var string|null
     */
    private $siretCode;

    private ?ApeCode $apeCode = null;

    /**
     * @var string|null
     */
    private $website;

    /**
     * @var float|null
     */
    private $allowedOutstandingAmount;

    /**
     * @var int|null
     */
    private $maxPaymentDays;

    /**
     * @var int|null
     */
    private $riskId;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
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

    public function getPassword(): ?Password
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password): static
    {
        $this->password = new Password($password);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @param int $defaultGroupId
     */
    public function setDefaultGroupId($defaultGroupId): static
    {
        $this->defaultGroupId = $defaultGroupId;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getGroupIds(): ?array
    {
        return $this->groupIds;
    }

    /**
     * @param int[] $groupIds
     */
    public function setGroupIds(array $groupIds): static
    {
        $this->groupIds = $groupIds;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGenderId()
    {
        return $this->genderId;
    }

    /**
     * @param int $genderId
     */
    public function setGenderId($genderId): static
    {
        $this->genderId = $genderId;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }

    /**
     * @param bool $isNewsletterSubscribed
     */
    public function setNewsletterSubscribed($isNewsletterSubscribed): void
    {
        $this->isNewsletterSubscribed = $isNewsletterSubscribed;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     */
    public function setIsEnabled($isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPartnerOffersSubscribed()
    {
        return $this->isPartnerOffersSubscribed;
    }

    /**
     * @param bool $isPartnerOffersSubscribed
     */
    public function setIsPartnerOffersSubscribed($isPartnerOffersSubscribed): static
    {
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;

        return $this;
    }

    public function getBirthday(): ?Birthday
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday($birthday): static
    {
        $this->birthday = new Birthday($birthday);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiretCode()
    {
        return $this->siretCode;
    }

    /**
     * @param string $siretCode
     */
    public function setSiretCode($siretCode): static
    {
        $this->siretCode = $siretCode;

        return $this;
    }

    public function getApeCode(): ?ApeCode
    {
        return $this->apeCode;
    }

    /**
     * @param string $apeCode
     */
    public function setApeCode($apeCode): static
    {
        $this->apeCode = new ApeCode($apeCode);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website): static
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAllowedOutstandingAmount()
    {
        return $this->allowedOutstandingAmount;
    }

    /**
     * @param float $allowedOutstandingAmount
     */
    public function setAllowedOutstandingAmount($allowedOutstandingAmount): static
    {
        $this->allowedOutstandingAmount = $allowedOutstandingAmount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxPaymentDays()
    {
        return $this->maxPaymentDays;
    }

    /**
     * @param int $maxPaymentDays
     */
    public function setMaxPaymentDays($maxPaymentDays): static
    {
        $this->maxPaymentDays = $maxPaymentDays;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRiskId()
    {
        return $this->riskId;
    }

    /**
     * @param int $riskId
     */
    public function setRiskId($riskId): static
    {
        $this->riskId = $riskId;

        return $this;
    }
}
