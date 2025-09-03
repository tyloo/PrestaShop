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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ProductFillerInterface;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;

/**
 * Handles the @see UpdateProductCommand using legacy object model
 */
#[AsCommandHandler]
class UpdateProductHandler implements UpdateProductHandlerInterface
{
    public function __construct(
        private readonly ProductFillerInterface $productUpdatablePropertyFiller,
        private readonly ProductRepository $productRepository,
        private readonly ProductIndexationUpdater $productIndexationUpdater,
    ) {
    }

    public function handle(UpdateProductCommand $command): void
    {
        $shopConstraint = $command->getShopConstraint();
        $product = $this->productRepository->getByShopConstraint($command->getProductId(), $shopConstraint);
        $this->productIndexationUpdater->isVisibleOnSearch($product);

        $updatableProperties = $this->productUpdatablePropertyFiller->fillUpdatableProperties(
            $product,
            $command
        );

        if ($command->isActive() !== null) {
            $product->active = $command->isActive();
            $updatableProperties[] = 'active';
        }

        if (empty($updatableProperties)) {
            return;
        }

        $this->productRepository->partialUpdate(
            $product,
            $updatableProperties,
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_PRODUCT
        );

        if (
            // Reindexing is costly operation, so we check if properties impacting indexation have changed and then reindex if needed.
            $this->productIndexationUpdater->isIndexationNeeded($updatableProperties)
            // If multiple shops are impacted it's safer to update indexation, it's more complicated to check if it's needed
            || $shopConstraint->forAllShops()
            || $shopConstraint->getShopGroupId()
            || ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds())
        ) {
            $this->productIndexationUpdater->updateIndexation($product, $command->getShopConstraint());
        }
    }
}
