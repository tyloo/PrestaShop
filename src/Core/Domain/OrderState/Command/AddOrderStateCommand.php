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

use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateConstraintException;

/**
 * Adds new order state with provided data
 */
class AddOrderStateCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

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
     * @param string[] $localizedNames
     * @param string[] $localizedTemplates
     */
    public function __construct(
        array $localizedNames,
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
    ) {
        $this->setLocalizedNames($localizedNames);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws OrderStateConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if ($localizedNames === []) {
            throw new OrderStateConstraintException('Order status name cannot be empty', OrderStateConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isLoggable(): bool
    {
        return $this->loggable;
    }

    public function isInvoice(): bool
    {
        return $this->invoice;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function isSendEmailEnabled(): bool
    {
        return $this->sendEmail;
    }

    public function isPdfInvoice(): bool
    {
        return $this->pdfInvoice;
    }

    public function isPdfDelivery(): bool
    {
        return $this->pdfDelivery;
    }

    public function isShipped(): bool
    {
        return $this->shipped;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function isDelivery(): bool
    {
        return $this->delivery;
    }

    /**
     * @return string[]
     */
    public function getLocalizedTemplates(): array
    {
        return $this->localizedTemplates;
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
