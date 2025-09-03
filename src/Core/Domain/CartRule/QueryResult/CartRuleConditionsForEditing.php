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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerIdInterface;

class CartRuleConditionsForEditing
{
    public function __construct(
        private readonly CustomerIdInterface $customerId,
        private readonly ?DateTime $dateFrom,
        private readonly ?DateTime $dateTo,
        private readonly int $quantity,
        private readonly int $quantityPerUser,
        private readonly ?CartRuleMinimumForEditing $minimum,
        private readonly CartRuleRestrictionsForEditing $restrictions,
    ) {
    }

    public function getCustomerId(): CustomerIdInterface
    {
        return $this->customerId;
    }

    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    public function getDateTo(): ?DateTime
    {
        return $this->dateTo;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getQuantityPerUser(): int
    {
        return $this->quantityPerUser;
    }

    public function getMinimum(): ?CartRuleMinimumForEditing
    {
        return $this->minimum;
    }

    public function getRestrictions(): CartRuleRestrictionsForEditing
    {
        return $this->restrictions;
    }
}
