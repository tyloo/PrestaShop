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

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\EditStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\EditStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;
use State;

/**
 * Handles state editing
 */
#[AsCommandHandler]
class EditStateHandler implements EditStateHandlerInterface
{
    /**
     * @throws CannotUpdateStateException
     * @throws StateConstraintException
     * @throws StateException
     * @throws StateNotFoundException
     */
    public function handle(EditStateCommand $command): void
    {
        $state = $this->getState($command->getStateId());

        try {
            if ($command->getZoneId()->getValue() !== null) {
                $state->id_zone = $command->getZoneId()->getValue();
            }

            if ($command->getCountryId()->getValue() !== null) {
                $state->id_country = $command->getCountryId()->getValue();
            }

            if ($command->getIsoCode() !== null) {
                $state->iso_code = $command->getIsoCode();
            }

            if ($command->getName() !== null) {
                $state->name = $command->getName();
            }

            if ($command->getActive() !== null) {
                $state->active = $command->getActive();
            }

            $isValid = $state->validateFields(true);
            if ($isValid !== true) {
                throw new StateConstraintException('State contains invalid field values: ' . $isValid);
            }

            if ($state->update() === false) {
                throw new CannotUpdateStateException('Failed to update state');
            }
        } catch (PrestaShopException $prestaShopException) {
            throw new StateException('An unexpected error occurred when updating state', 0, $prestaShopException);
        }
    }

    /**
     * @throws StateNotFoundException
     * @throws StateException
     */
    private function getState(StateId $stateId): State
    {
        $stateIdValue = $stateId->getValue();

        try {
            $state = new State($stateIdValue);
        } catch (PrestaShopException $prestaShopException) {
            throw new StateException(\sprintf('Failed to get state with id: "%s"', $stateIdValue), 0, $prestaShopException);
        }

        if ($state->id !== $stateIdValue) {
            throw new StateNotFoundException(\sprintf('State with id "%s" was not found.', $stateIdValue));
        }

        return $state;
    }
}
