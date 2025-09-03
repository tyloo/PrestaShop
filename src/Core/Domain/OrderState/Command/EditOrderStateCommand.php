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

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\Name;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Edits provided order state.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing order state.
 * For example, if the name is null, then the original value is not modified,
 * however, if name is set, then the original value will be overwritten.
 */
class EditOrderStateCommand
{
    private readonly OrderStateId $orderStateId;

    /**
     * @var array<string>|null
     */
    private $name;

    private ?string $color = null;

    private ?bool $loggable = null;

    private ?bool $invoice = null;

    private ?bool $hidden = null;

    private ?bool $sendEmail = null;

    private ?bool $pdfInvoice = null;

    private ?bool $pdfDelivery = null;

    private ?bool $shipped = null;

    private ?bool $paid = null;

    private ?bool $delivery = null;

    private ?array $template = null;

    /**
     * @var string|null
     */
    protected $pathName;

    /**
     * @var int|null
     */
    protected $fileSize;

    /**
     * @var string|null
     */
    protected $mimeType;

    /**
     * @var string|null
     */
    protected $originalName;

    /**
     * @param int $orderStateId
     */
    public function __construct($orderStateId)
    {
        $this->orderStateId = new OrderStateId($orderStateId);
    }

    public function getOrderStateId(): OrderStateId
    {
        return $this->orderStateId;
    }

    /**
     * @return array<string>|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array<string> $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @return self
     */
    public function setColor(?string $color)
    {
        $this->color = $color;

        return $this;
    }

    public function isLoggable(): ?bool
    {
        return $this->loggable;
    }

    /**
     * @return self
     */
    public function setLoggable(?bool $loggable)
    {
        $this->loggable = $loggable;

        return $this;
    }

    public function isInvoice(): ?bool
    {
        return $this->invoice;
    }

    /**
     * @return self
     */
    public function setInvoice(?bool $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    /**
     * @return self
     */
    public function setHidden(?bool $hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isSendEmailEnabled(): ?bool
    {
        return $this->sendEmail;
    }

    /**
     * @return self
     */
    public function setSendEmail(?bool $sendEmail)
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    public function isPdfInvoice(): ?bool
    {
        return $this->pdfInvoice;
    }

    /**
     * @return self
     */
    public function setPdfInvoice(?bool $pdfInvoice)
    {
        $this->pdfInvoice = $pdfInvoice;

        return $this;
    }

    public function isPdfDelivery(): ?bool
    {
        return $this->pdfDelivery;
    }

    /**
     * @return self
     */
    public function setPdfDelivery(?bool $pdfDelivery)
    {
        $this->pdfDelivery = $pdfDelivery;

        return $this;
    }

    public function isShipped(): ?bool
    {
        return $this->shipped;
    }

    /**
     * @return self
     */
    public function setShipped(?bool $shipped)
    {
        $this->shipped = $shipped;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    /**
     * @return self
     */
    public function setPaid(?bool $paid)
    {
        $this->paid = $paid;

        return $this;
    }

    public function isDelivery(): ?bool
    {
        return $this->delivery;
    }

    /**
     * @return self
     */
    public function setDelivery(?bool $delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getTemplate(): ?array
    {
        return $this->template;
    }

    /**
     * @return self
     */
    public function setTemplate(?array $template)
    {
        $this->template = $template;

        return $this;
    }

    public function setFileInformation(
        string $pathName,
        int $fileSize,
        string $mimeType,
        string $originalName,
    ): void {
        $this->pathName = $pathName;
        $this->fileSize = $fileSize;
        $this->mimeType = $mimeType;
        $this->originalName = $originalName;
    }

    public function getFilePathName(): ?string
    {
        return $this->pathName;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }
}
