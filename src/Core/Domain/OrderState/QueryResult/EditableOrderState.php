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

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use SplFileInfo;

/**
 * Stores editable data for order state
 */
class EditableOrderState
{
    /**
     * @var SplFileInfo|null
     */
    protected $icon;

    public function __construct(
        private readonly OrderStateId $orderStateId,
        private readonly array $localizedNames,
        ?SplFileInfo $icon,
        private readonly string $color,
        private readonly bool $loggable,
        private readonly bool $invoice,
        private readonly bool $hidden,
        private readonly bool $sendEmail,
        private readonly bool $pdfInvoice,
        private readonly bool $pdfDelivery,
        private readonly bool $shipped,
        private readonly bool $paid,
        private readonly bool $delivery,
        private readonly array $localizedTemplates,
        private readonly bool $isDeleted,
    ) {
        $this->icon = $icon;
    }

    /**
     * @return OrderStateId
     */
    public function getOrderStateId()
    {
        return $this->orderStateId;
    }

    /**
     * @return array
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @return bool
     */
    public function isLoggable()
    {
        return $this->loggable;
    }

    /**
     * @return bool
     */
    public function isInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function isSendEmailEnabled()
    {
        return $this->sendEmail;
    }

    /**
     * @return bool
     */
    public function isPdfInvoice()
    {
        return $this->pdfInvoice;
    }

    /**
     * @return bool
     */
    public function isPdfDelivery()
    {
        return $this->pdfDelivery;
    }

    /**
     * @return bool
     */
    public function isShipped()
    {
        return $this->shipped;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->paid;
    }

    /**
     * @return bool
     */
    public function isDelivery()
    {
        return $this->delivery;
    }

    /**
     * @return array
     */
    public function getLocalizedTemplates()
    {
        return $this->localizedTemplates;
    }

    public function getIcon(): ?SplFileInfo
    {
        return $this->icon;
    }
}
