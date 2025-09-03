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

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Transfers product shipping information data
 */
class ProductShippingInformation
{
    /**
     * @param int[]    $carrierReferences
     * @param string[] $localizedDeliveryTimeInStockNotes
     * @param string[] $localizedDeliveryTimeOutOfStockNotes
     */
    public function __construct(
        private readonly DecimalNumber $width,
        private readonly DecimalNumber $height,
        private readonly DecimalNumber $depth,
        private readonly DecimalNumber $weight,
        private readonly DecimalNumber $additionalShippingCost,
        private readonly array $carrierReferences,
        private readonly int $deliveryTimeNotesType,
        private readonly array $localizedDeliveryTimeInStockNotes,
        private readonly array $localizedDeliveryTimeOutOfStockNotes,
    ) {
    }

    public function getWidth(): DecimalNumber
    {
        return $this->width;
    }

    public function getHeight(): DecimalNumber
    {
        return $this->height;
    }

    public function getDepth(): DecimalNumber
    {
        return $this->depth;
    }

    public function getWeight(): DecimalNumber
    {
        return $this->weight;
    }

    public function getAdditionalShippingCost(): DecimalNumber
    {
        return $this->additionalShippingCost;
    }

    /**
     * @return int[]
     */
    public function getCarrierReferences(): array
    {
        return $this->carrierReferences;
    }

    public function getDeliveryTimeNoteType(): int
    {
        return $this->deliveryTimeNotesType;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeInStockNotes(): array
    {
        return $this->localizedDeliveryTimeInStockNotes;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeOutOfStockNotes(): array
    {
        return $this->localizedDeliveryTimeOutOfStockNotes;
    }
}
