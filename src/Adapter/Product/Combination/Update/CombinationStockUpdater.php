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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use StockAvailable;

/**
 * Updates stock for product combination
 */
class CombinationStockUpdater
{
    public function __construct(
        private readonly StockAvailableRepository $stockAvailableRepository,
        private readonly CombinationRepository $combinationRepository,
        private readonly MovementReasonRepository $movementReasonRepository,
        private readonly StockManager $stockManager,
        private readonly ShopConfigurationInterface $configuration,
        private readonly HookDispatcherInterface $hookDispatcher,
    ) {
    }

    public function update(
        CombinationId $combinationId,
        CombinationStockProperties $properties,
        ShopConstraint $shopConstraint,
    ): void {
        $combination = $this->combinationRepository->getByShopConstraint($combinationId, $shopConstraint);
        $this->updateStockByShopConstraint(
            $combination,
            $properties,
            $shopConstraint
        );
    }

    private function updateStockAvailable(StockAvailable $stockAvailable, CombinationStockProperties $properties): void
    {
        $updateLocation = $properties->getLocation() !== null;
        $stockModification = $properties->getStockModification();

        if (! $stockModification && ! $updateLocation) {
            return;
        }

        if ($stockModification !== null) {
            $previousQuantity = (int) $stockAvailable->quantity;
            if ($stockModification->getDeltaQuantity() !== null) {
                $stockAvailable->quantity += $stockModification->getDeltaQuantity();
            } else {
                $stockAvailable->quantity = $stockModification->getFixedQuantity();
            }
        }

        if ($updateLocation) {
            $stockAvailable->location = $properties->getLocation();
        }

        $fallbackShopId = $this->stockAvailableRepository->getFallbackShopId($stockAvailable);
        $this->stockAvailableRepository->update($stockAvailable, $fallbackShopId);
        // save movement only after stockAvailable has been updated
        if ($stockModification !== null) {
            $this->saveMovement($stockAvailable, $stockModification, $previousQuantity, $fallbackShopId->getValue());

            // Update reserved and physical quantity for this stock
            $shopConstraint = ShopConstraint::shop($fallbackShopId->getValue());
            $this->stockAvailableRepository->updatePhysicalProductQuantity(
                new StockId((int) $stockAvailable->id),
                new OrderStateId((int) $this->configuration->get('PS_OS_ERROR', null, $shopConstraint)),
                new OrderStateId((int) $this->configuration->get('PS_OS_CANCELED', null, $shopConstraint))
            );
        }
    }

    private function saveMovement(StockAvailable $stockAvailable, StockModification $stockModification, int $previousQuantity, int $affectedShopId): void
    {
        if ($stockModification->getDeltaQuantity() !== null) {
            $deltaQuantity = $stockModification->getDeltaQuantity();
        } else {
            $deltaQuantity = $stockModification->getFixedQuantity() - $previousQuantity;
        }

        $movementReasonId = $this->movementReasonRepository->getEmployeeEditionReasonId($deltaQuantity > $previousQuantity);

        $this->stockManager->saveMovement(
            $stockAvailable->id_product,
            $stockAvailable->id_product_attribute,
            $deltaQuantity,
            [
                'id_stock_mvt_reason' => $movementReasonId->getValue(),
                'id_shop' => $affectedShopId,
            ]
        );

        $this->hookDispatcher->dispatchWithParameters('actionUpdateQuantity',
            [
                'id_product' => $stockAvailable->id_product,
                'id_product_attribute' => $stockAvailable->id_product_attribute,
                'quantity' => $stockAvailable->quantity,
                'delta_quantity' => $deltaQuantity,
                'id_shop' => $stockAvailable->id_shop,
            ]);
    }

    private function updateStockByShopConstraint(
        Combination $combination,
        CombinationStockProperties $properties,
        ShopConstraint $shopConstraint,
    ): void {
        $combinationId = new CombinationId((int) $combination->id);
        if ($shopConstraint->forAllShops() || ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds())) {
            // Since each stock has a distinct ID we can't use the ObjectModel multi shop feature based on id_shop_list,
            // so we manually loop to update each associated stocks
            if ($shopConstraint instanceof ShopCollection) {
                $shopIds = $shopConstraint->getShopIds();
            } else {
                $shopIds = $this->combinationRepository->getAssociatedShopIds($combinationId);
            }

            foreach ($shopIds as $shopId) {
                $this->updateStockAvailable(
                    $this->stockAvailableRepository->getForCombination($combinationId, $shopId),
                    $properties
                );
            }
        } else {
            $this->updateStockAvailable(
                $this->stockAvailableRepository->getForCombination($combinationId, new ShopId($combination->getShopId())),
                $properties
            );
        }
    }
}
