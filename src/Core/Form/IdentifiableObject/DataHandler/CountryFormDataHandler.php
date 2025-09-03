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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Handles submitted zone form data.
 */
class CountryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Create object from form data.
     */
    public function create(array $data): int
    {
        $addCountryCommand = new AddCountryCommand(
            $data['name'],
            (string) $data['iso_code'],
            (int) $data['call_prefix'],
            (int) $data['default_currency'],
            (int) $data['zone'],
            $data['need_zip_code'],
            $data['zip_code_format'],
            (string) $data['address_format'],
            $data['is_enabled'],
            $data['contains_states'],
            $data['need_identification_number'],
            $data['display_tax_label'],
            $data['shop_association'] ?? []
        );

        /** @var CountryId $countryId */
        $countryId = $this->commandBus->handle($addCountryCommand);

        return $countryId->getValue();
    }

    public function update($id, array $data): void
    {
        $editCountryCommand = new EditCountryCommand($id);

        if ($data['name'] !== null) {
            $editCountryCommand->setLocalizedNames($data['name']);
        }

        if ($data['iso_code'] !== null) {
            $editCountryCommand->setIsoCode($data['iso_code']);
        }

        if ($data['call_prefix'] !== null) {
            $editCountryCommand->setCallPrefix((int) $data['call_prefix']);
        }

        if ($data['address_format'] !== null) {
            $editCountryCommand->setAddressFormat($data['address_format']);
        }

        if ($data['zip_code_format'] !== null) {
            $editCountryCommand->setZipCodeFormat($data['zip_code_format']);
        }

        if ($data['default_currency'] !== null) {
            $editCountryCommand->setDefaultCurrency($data['default_currency']);
        }

        if ($data['zone'] !== null) {
            $editCountryCommand->setZoneId($data['zone']);
        }

        if ($data['need_zip_code'] !== null) {
            $editCountryCommand->setNeedZipCode($data['need_zip_code']);
        }

        if ($data['is_enabled'] !== null) {
            $editCountryCommand->setEnabled($data['is_enabled']);
        }

        if ($data['contains_states'] !== null) {
            $editCountryCommand->setContainsStates($data['contains_states']);
        }

        if ($data['need_identification_number'] !== null) {
            $editCountryCommand->setNeedIdNumber($data['need_identification_number']);
        }

        if ($data['display_tax_label'] !== null) {
            $editCountryCommand->setDisplayTaxLabel($data['display_tax_label']);
        }

        if (isset($data['shop_association'])) {
            $editCountryCommand->setShopAssociation($data['shop_association']);
        }

        $this->commandBus->handle($editCountryCommand);
    }
}
