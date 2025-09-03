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

namespace PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Transfers contact data for editing.
 */
class EditableContact
{
    /**
     * @var ContactId
     */
    private $contactId;

    /**
     * @var Email|null
     */
    private $email;

    /**
     * @param int      $contactId
     * @param string[] $localisedTitles
     * @param string   $email
     * @param bool     $isMessagesSavingEnabled
     * @param string[] $localisedDescription
     * @param int[]    $shopAssociation
     *
     * @throws ContactException
     * @throws DomainConstraintException
     */
    public function __construct(
        $contactId,
        private readonly array $localisedTitles,
        $email,
        private $isMessagesSavingEnabled,
        private $localisedDescription,
        private readonly array $shopAssociation,
    ) {
        $this->contactId = new ContactId($contactId);
        $this->email = $email ? new Email($email) : null;
    }

    /**
     * @return ContactId
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @return string[]
     */
    public function getLocalisedTitles(): array
    {
        return $this->localisedTitles;
    }

    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isMessagesSavingEnabled()
    {
        return $this->isMessagesSavingEnabled;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
