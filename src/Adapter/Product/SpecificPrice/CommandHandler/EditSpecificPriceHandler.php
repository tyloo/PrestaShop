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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\CommandHandler;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler\EditSpecificPriceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use SpecificPrice;

/**
 * Handles @see EditSpecificPriceCommand using legacy object model
 */
#[AsCommandHandler]
class EditSpecificPriceHandler implements EditSpecificPriceHandlerInterface
{
    public function __construct(
        private readonly SpecificPriceRepository $specificPriceRepository,
    ) {
    }

    public function handle(EditSpecificPriceCommand $command): void
    {
        $specificPrice = $this->specificPriceRepository->get($command->getSpecificPriceId());

        $this->specificPriceRepository->partialUpdate(
            $specificPrice,
            $this->fillUpdatableProperties($command, $specificPrice)
        );
    }

    /**
     * @return string[]
     */
    protected function fillUpdatableProperties(EditSpecificPriceCommand $command, SpecificPrice $specificPrice): array
    {
        $updatableProperties = [];

        if ($command->getReduction() instanceof Reduction) {
            $specificPrice->reduction_type = $command->getReduction()->getType();
            $reductionValue = $command->getReduction()->getValue();
            // VO stores percent expressed based on 100, while the DB stored the float value (VO: 57.5 - DB: 0.575)
            if ($command->getReduction()->getType() === Reduction::TYPE_PERCENTAGE) {
                $reductionValue = $reductionValue->dividedBy(new DecimalNumber('100'));
            }

            $specificPrice->reduction = (string) $reductionValue;
            $updatableProperties = [
                'reduction_type',
                'reduction',
            ];
        }

        if ($command->getFixedPrice() instanceof \PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPriceInterface) {
            $specificPrice->price = (string) $command->getFixedPrice()->getValue();
            $updatableProperties[] = 'price';
        }

        if ($command->includesTax() !== null) {
            $specificPrice->reduction_tax = $command->includesTax();
            $updatableProperties[] = 'reduction_tax';
        }

        if ($command->getFromQuantity() !== null) {
            $specificPrice->from_quantity = $command->getFromQuantity();
            $updatableProperties[] = 'from_quantity';
        }

        if ($command->getShopId() instanceof \PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopIdInterface) {
            $specificPrice->id_shop = $command->getShopId()->getValue();
            $updatableProperties[] = 'id_shop';
        }

        if ($command->getCombinationId() instanceof \PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface) {
            $specificPrice->id_product_attribute = $command->getCombinationId()->getValue();
            $updatableProperties[] = 'id_product_attribute';
        }

        if ($command->getCurrencyId() instanceof \PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyIdInterface) {
            $specificPrice->id_currency = $command->getCurrencyId()->getValue();
            $updatableProperties[] = 'id_currency';
        }

        if ($command->getCountryId() !== null) {
            $specificPrice->id_country = $command->getCountryId();
            $updatableProperties[] = 'id_country';
        }

        if ($command->getGroupId() instanceof \PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupIdInterface) {
            $specificPrice->id_group = $command->getGroupId()->getValue();
            $updatableProperties[] = 'id_group';
        }

        if ($command->getCustomerId() !== null) {
            $specificPrice->id_customer = $command->getCustomerId();
            $updatableProperties[] = 'id_customer';
        }

        if ($command->getDateTimeFrom() instanceof DateTimeInterface) {
            $specificPrice->from = $command->getDateTimeFrom()->format(DateTime::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'from';
        }

        if ($command->getDateTimeTo() instanceof DateTimeInterface) {
            $specificPrice->to = $command->getDateTimeTo()->format(DateTime::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'to';
        }

        return $updatableProperties;
    }
}
