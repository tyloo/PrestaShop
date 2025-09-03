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

/**
 * Transfers combination data for editing
 */
class CombinationForEditing
{
    /**
     * @param int[] $imageIds
     */
    public function __construct(
        private readonly int $combinationId,
        private readonly int $productId,
        private readonly string $name,
        private readonly CombinationDetails $details,
        private readonly CombinationPrices $prices,
        private readonly CombinationStock $stock,
        private readonly array $imageIds,
        private readonly string $coverThumbnailUrl,
        private readonly bool $isDefault,
    ) {
    }

    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDetails(): CombinationDetails
    {
        return $this->details;
    }

    public function getPrices(): CombinationPrices
    {
        return $this->prices;
    }

    public function getStock(): CombinationStock
    {
        return $this->stock;
    }

    /**
     * @return int[]
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function getCoverThumbnailUrl(): string
    {
        return $this->coverThumbnailUrl;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
