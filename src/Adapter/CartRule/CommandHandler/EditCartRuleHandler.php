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

namespace PrestaShop\PrestaShop\Adapter\CartRule\CommandHandler;

use CartRule;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleActionFiller;
use PrestaShop\PrestaShop\Adapter\CartRule\Repository\CartRuleRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\EditCartRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

#[AsCommandHandler]
class EditCartRuleHandler implements EditCartRuleHandlerInterface
{
    public function __construct(
        private readonly CartRuleRepository $cartRuleRepository,
        private readonly CartRuleActionFiller $cartRuleActionFiller,
    ) {
    }

    public function handle(EditCartRuleCommand $command): void
    {
        $cartRule = $this->cartRuleRepository->get($command->getCartRuleId());
        $updatableProperties = $this->fillUpdatableProperties($cartRule, $command);

        if (empty($updatableProperties)) {
            return;
        }

        $this->cartRuleRepository->partialUpdate($cartRule, $updatableProperties);
    }

    /**
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillUpdatableProperties(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $propertiesToUpdate = [];
        if ($command->getLocalizedNames() !== null) {
            $cartRule->name = $command->getLocalizedNames();
            $propertiesToUpdate['name'] = array_keys($command->getLocalizedNames());
        }

        if ($command->getDescription() !== null) {
            $cartRule->description = $command->getDescription();
            $propertiesToUpdate[] = 'description';
        }

        if ($command->getCode() !== null) {
            $cartRule->code = $command->getCode();
            $propertiesToUpdate[] = 'code';
        }

        if ($command->highlightInCart() !== null) {
            $cartRule->highlight = $command->highlightInCart();
            $propertiesToUpdate[] = 'highlight';
        }

        if ($command->allowPartialUse() !== null) {
            $cartRule->partial_use = $command->allowPartialUse();
            $propertiesToUpdate[] = 'partial_use';
        }

        if ($command->getPriority() !== null) {
            $cartRule->priority = $command->getPriority();
            $propertiesToUpdate[] = 'priority';
        }

        if ($command->isActive() !== null) {
            $cartRule->active = $command->isActive();
            $propertiesToUpdate[] = 'active';
        }

        $conditionsToUpdate = $this->fillConditions($cartRule, $command);
        $actionsToUpdate = $this->fillActions($cartRule, $command);

        return array_merge($propertiesToUpdate, $conditionsToUpdate, $actionsToUpdate);
    }

    /**
     * Fills cart rule with conditions data from command.
     *
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillConditions(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $updatableProperties = [];

        if ($command->getCustomerId() instanceof \PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerIdInterface) {
            $cartRule->id_customer = $command->getCustomerId()->getValue();
            $updatableProperties[] = 'id_customer';
        }

        if ($command->getValidFrom() instanceof DateTimeImmutable) {
            $cartRule->date_from = $command->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_from';
        }

        if ($command->getValidTo() instanceof DateTimeImmutable) {
            $cartRule->date_to = $command->getValidTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_to';
        }

        $minimumAmount = $command->getMinimumAmount();
        if ($minimumAmount instanceof \PrestaShop\PrestaShop\Core\Domain\ValueObject\Money) {
            $cartRule->minimum_amount = (float) (string) $minimumAmount->getAmount();
            $cartRule->minimum_amount_currency = $minimumAmount->getCurrencyId()->getValue();
            $cartRule->minimum_amount_tax = $minimumAmount->isTaxIncluded();
            $cartRule->minimum_amount_shipping = $command->isMinimumAmountShippingIncluded();
            $updatableProperties = array_merge($updatableProperties, [
                'minimum_amount',
                'minimum_amount_currency',
                'minimum_amount_tax',
                'minimum_amount_shipping',
            ]);
        }

        if ($command->getTotalQuantity() !== null) {
            $cartRule->quantity = $command->getTotalQuantity();
            $updatableProperties[] = 'quantity';
        }

        if ($command->getQuantityPerUser() !== null) {
            $cartRule->quantity_per_user = $command->getQuantityPerUser();
            $updatableProperties[] = 'quantity_per_user';
        }

        return $updatableProperties;
    }

    /**
     * Fills cart rule with actions data from command.
     *
     * @return string[] updatable properties
     */
    private function fillActions(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $cartRuleAction = $command->getCartRuleAction();

        if (! $cartRuleAction instanceof \PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction) {
            return [];
        }

        return $this->cartRuleActionFiller->fillUpdatableProperties($cartRule, $cartRuleAction);
    }
}
