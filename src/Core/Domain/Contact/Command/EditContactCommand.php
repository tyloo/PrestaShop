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

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Class EditContactCommand is responsible for editing contact data.
 */
class EditContactCommand extends AbstractContactCommand
{
    private readonly ContactId $contactId;

    /**
     * @var string[]
     */
    private ?array $localisedTitles = null;

    private ?Email $email = null;

    /**
     * @var bool
     */
    private $isMessagesSavingEnabled;

    /**
     * @var string[]
     */
    private ?array $localisedDescription = null;

    /**
     * @var int[]
     */
    private ?array $shopAssociation = null;

    /**
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function __construct($contactId)
    {
        $this->contactId = new ContactId($contactId);
    }

    public function getContactId(): ContactId
    {
        return $this->contactId;
    }

    /**
     * @return string[]
     */
    public function getLocalisedTitles(): ?array
    {
        return $this->localisedTitles;
    }

    /**
     * @param string[] $localisedTitles
     *
     * @throws ContactConstraintException
     */
    public function setLocalisedTitles(array $localisedTitles): static
    {
        if (! $this->assertIsNotEmptyAndContainsAllNonEmptyStringValues($localisedTitles)) {
            throw new ContactConstraintException(\sprintf('Expected to have not empty titles array but received %s', var_export($localisedTitles, true)), ContactConstraintException::INVALID_TITLE);
        }

        foreach ($localisedTitles as $title) {
            if (! $this->assertIsGenericName($title)) {
                throw new ContactConstraintException(\sprintf('Expected value %s to match given regex /^[^<>{}]*$/u but failed', var_export($title, true)), ContactConstraintException::INVALID_TITLE);
            }
        }

        $this->localisedTitles = $localisedTitles;

        return $this;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    public function setEmail($email): static
    {
        $this->email = new Email($email);

        return $this;
    }

    /**
     * @return bool
     */
    public function isMessagesSavingEnabled()
    {
        return $this->isMessagesSavingEnabled;
    }

    /**
     * @param bool $isMessagesSavingEnabled
     */
    public function setIsMessagesSavingEnabled($isMessagesSavingEnabled): static
    {
        $this->isMessagesSavingEnabled = $isMessagesSavingEnabled;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription(): ?array
    {
        return $this->localisedDescription;
    }

    /**
     * @param string[] $localisedDescription
     */
    public function setLocalisedDescription(array $localisedDescription): static
    {
        $this->localisedDescription = $localisedDescription;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @throws ContactConstraintException
     */
    public function setShopAssociation(array $shopAssociation): static
    {
        if (! $this->assertArrayContainsAllIntegerValues($shopAssociation)) {
            throw new ContactConstraintException(\sprintf('Given shop association %s must contain all integer values', var_export($shopAssociation, true)), ContactConstraintException::INVALID_SHOP_ASSOCIATION);
        }

        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
