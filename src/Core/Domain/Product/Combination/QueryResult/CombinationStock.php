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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use DateTimeInterface;

class CombinationStock
{
    public function __construct(
        private readonly int $quantity,
        private readonly int $minimalQuantity,
        private readonly int $lowStockThreshold,
        private readonly bool $lowStockAlertEnabled,
        private readonly string $location,
        private readonly ?DateTimeInterface $availableDate,
        /**
         * @var string[] key value pairs where key is the id of language
         */
        private readonly array $localizedAvailableNowLabels,
        /**
         * @var string[] key value pairs where key is the id of language
         */
        private readonly array $localizedAvailableLaterLabels,
    ) {
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getMinimalQuantity(): int
    {
        return $this->minimalQuantity;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getLowStockThreshold(): int
    {
        return $this->lowStockThreshold;
    }

    public function isLowStockAlertEnabled(): bool
    {
        return $this->lowStockAlertEnabled;
    }

    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }

    /**
     * @return string[]
     */
    public function getLocalizedAvailableNowLabels(): array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @return string[]
     */
    public function getLocalizedAvailableLaterLabels(): array
    {
        return $this->localizedAvailableLaterLabels;
    }
}
