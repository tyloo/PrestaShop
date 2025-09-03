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
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Adds new customer with provided data
 */
class AddCustomerCommand
{
    private readonly FirstName $firstName;

    private readonly LastName $lastName;

    private readonly Email $email;

    private readonly Password $password;

    private readonly Birthday $birthday;

    /**
     * @var string|null Only for B2b customers
     */
    private $companyName;

    /**
     * @var string|null Only for B2b customers
     */
    private $siretCode;

    /**
     * @var ApeCode|null Only for B2b customers
     */
    private ?ApeCode $apeCode = null;

    /**
     * @var string|null Only for B2b customers
     */
    private $website;

    /**
     * @var float|null Only for B2b customers
     */
    private $allowedOutstandingAmount;

    /**
     * @var int|null Only for B2b customers
     */
    private $maxPaymentDays;

    /**
     * @var int|null Only for B2b customers
     */
    private $riskId;

    /**
     * @param string      $firstName
     * @param string      $lastName
     * @param string      $email
     * @param string      $password
     * @param int         $defaultGroupId
     * @param int[]       $groupIds
     * @param int         $shopId
     * @param int|null    $genderId
     * @param bool        $isEnabled
     * @param bool        $isPartnerOffersSubscribed
     * @param string|null $birthday
     * @param bool        $isGuest
     */
    public function __construct(
        $firstName,
        $lastName,
        $email,
        $password,
        private $defaultGroupId,
        private readonly array $groupIds,
        private $shopId,
        private $genderId = null,
        private $isEnabled = true,
        private $isPartnerOffersSubscribed = false,
        $birthday = null,
        private $isGuest = false,
    ) {
        $this->firstName = new FirstName($firstName);
        $this->lastName = new LastName($lastName);
        $this->email = new Email($email);
        $this->password = new Password($password);
        $this->birthday = $birthday !== null ? new Birthday($birthday) : Birthday::createEmpty();
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

    public function getPassword(): Password
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @return int[]
     */
    public function getGroupIds(): array
    {
        return $this->groupIds;
    }

    /**
     * @return int|null
     */
    public function getGenderId()
    {
        return $this->genderId;
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

    public function getBirthday(): Birthday
    {
        return $this->birthday;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
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
     *
     * @return self
     */
    public function setCompanyName($companyName)
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
     *
     * @return self
     */
    public function setSiretCode($siretCode)
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
     *
     * @return self
     */
    public function setApeCode($apeCode)
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
     *
     * @return self
     */
    public function setWebsite($website)
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
     *
     * @return self
     */
    public function setAllowedOutstandingAmount($allowedOutstandingAmount)
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
     *
     * @return self
     */
    public function setMaxPaymentDays($maxPaymentDays)
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
     *
     * @return self
     */
    public function setRiskId($riskId)
    {
        $this->riskId = $riskId;

        return $this;
    }

    public function isGuest(): bool
    {
        return $this->isGuest;
    }
}
