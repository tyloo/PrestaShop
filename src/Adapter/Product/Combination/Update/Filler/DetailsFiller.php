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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler;

use Combination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

/**
 * Fills combination properties which can be considered as combination details
 */
class DetailsFiller implements CombinationFillerInterface
{
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array
    {
        $updatableProperties = [];

        if ($command->getGtin() instanceof \PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Gtin) {
            $combination->ean13 = $command->getGtin()->getValue();
            $updatableProperties[] = 'ean13';
        }

        if ($command->getIsbn() instanceof \PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn) {
            $combination->isbn = $command->getIsbn()->getValue();
            $updatableProperties[] = 'isbn';
        }

        if ($command->getMpn() !== null) {
            $combination->mpn = $command->getMpn();
            $updatableProperties[] = 'mpn';
        }

        if ($command->getReference() instanceof \PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference) {
            $combination->reference = $command->getReference()->getValue();
            $updatableProperties[] = 'reference';
        }

        if ($command->getUpc() instanceof \PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc) {
            $combination->upc = $command->getUpc()->getValue();
            $updatableProperties[] = 'upc';
        }

        if ($command->getImpactOnWeight() instanceof \PrestaShop\Decimal\DecimalNumber) {
            $combination->weight = (float) (string) $command->getImpactOnWeight();
            $updatableProperties[] = 'weight';
        }

        return $updatableProperties;
    }
}
