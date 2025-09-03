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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderCustomerForViewing
{
    public function __construct(
        private readonly int $id,
        private readonly string $firstName,
        /**
         * @var string Gender name
         */
        private readonly string $lastName,
        private readonly string $gender,
        private readonly string $email,
        private readonly DateTimeImmutable $accountRegistrationDate,
        /**
         * @var string Formatted price with currency
         */
        private readonly string $totalSpentSinceRegistration,
        private readonly int $validOrdersPlaced,
        private readonly ?string $privateNote,
        private readonly bool $isGuest,
        private readonly int $languageId,
        private readonly string $ape = '',
        private readonly string $siret = '',
        private readonly array $groups = [],
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAccountRegistrationDate(): DateTimeImmutable
    {
        return $this->accountRegistrationDate;
    }

    public function getTotalSpentSinceRegistration(): string
    {
        return $this->totalSpentSinceRegistration;
    }

    public function getValidOrdersPlaced(): int
    {
        return $this->validOrdersPlaced;
    }

    public function getPrivateNote(): ?string
    {
        return $this->privateNote;
    }

    public function isGuest(): bool
    {
        return $this->isGuest;
    }

    public function getApe(): string
    {
        return $this->ape;
    }

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}
