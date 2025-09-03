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

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Stores editable data for customer
 */
class EditableCustomer
{
    /**
     * @param int    $genderId
     * @param bool   $isEnabled
     * @param bool   $isPartnerOffersSubscribed
     * @param bool   $isNewsletterSubscribed
     * @param int[]  $groupIds
     * @param int    $defaultGroupId
     * @param string $companyName
     * @param string $siretCode
     * @param string $apeCode
     * @param string $website
     * @param float  $allowedOutstandingAmount
     * @param int    $maxPaymentDays
     * @param int    $riskId
     */
    public function __construct(
        private readonly CustomerId $customerId,
        private $genderId,
        private readonly FirstName $firstName,
        private readonly LastName $lastName,
        private readonly Email $email,
        private readonly Birthday $birthday,
        private $isEnabled,
        private $isPartnerOffersSubscribed,
        private $isNewsletterSubscribed,
        private readonly array $groupIds,
        private $defaultGroupId,
        private $companyName,
        private $siretCode,
        private $apeCode,
        private $website,
        private $allowedOutstandingAmount,
        private $maxPaymentDays,
        private $riskId,
        private readonly bool $isGuest = false,
    ) {
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return int
     */
    public function getGenderId()
    {
        return $this->genderId;
    }

    /**
     * @return FirstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return LastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function isPartnerOffersSubscribed()
    {
        return $this->isPartnerOffersSubscribed;
    }

    /**
     * @return array|int[]
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @return int
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function getSiretCode()
    {
        return $this->siretCode;
    }

    /**
     * @return string
     */
    public function getApeCode()
    {
        return $this->apeCode;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return float
     */
    public function getAllowedOutstandingAmount()
    {
        return $this->allowedOutstandingAmount;
    }

    /**
     * @return int
     */
    public function getMaxPaymentDays()
    {
        return $this->maxPaymentDays;
    }

    /**
     * @return int
     */
    public function getRiskId()
    {
        return $this->riskId;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }

    public function isGuest(): bool
    {
        return $this->isGuest;
    }
}
