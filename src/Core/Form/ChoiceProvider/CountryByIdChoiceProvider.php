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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CountryByIdChoiceProvider provides country choices with ID values.
 */
final class CountryByIdChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var array
     */
    private $countries;

    /**
     * @var int[]
     */
    private ?array $dniCountriesId = null;

    /**
     * @var int[]
     */
    private ?array $postcodeCountriesId = null;

    /**
     * @param int $langId
     */
    public function __construct(
        private $langId,
        private readonly CountryDataProvider $countryDataProvider,
        private readonly string $psImageDir,
        private readonly string $psImageBaseUrl,
    ) {
    }

    /**
     * Get currency choices.
     */
    public function getChoices(): array
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->getCountries(),
            'id_country',
            'name'
        );
    }

    public function getChoicesAttributes(): array
    {
        $countries = $this->getCountries();
        $dniCountriesId = $this->getDniCountriesId();
        $postcodeCountriesId = $this->getPostcodeCountriesId();
        $choicesAttributes = [];

        foreach ($countries as $country) {
            if (\in_array($country['id_country'], $dniCountriesId, true)) {
                $choicesAttributes[$country['name']]['need_dni'] = 1;
            }

            if (\in_array($country['id_country'], $postcodeCountriesId, true)) {
                $choicesAttributes[$country['name']]['need_postcode'] = 1;
            }

            $flagPath = 'flags/' . strtolower((string) $country['iso_code']) . '.jpg';

            if (file_exists($this->psImageDir . $flagPath)) {
                $choicesAttributes[$country['name']]['data-logo'] = $this->psImageBaseUrl . $flagPath;
            }
        }

        return $choicesAttributes;
    }

    /**
     * @return array
     */
    private function getCountries()
    {
        if ($this->countries === null) {
            $this->countries = $this->countryDataProvider->getCountries($this->langId);
        }

        return $this->countries;
    }

    /**
     * @return int[]
     */
    private function getDniCountriesId(): array
    {
        if ($this->dniCountriesId === null) {
            $this->dniCountriesId = $this->countryDataProvider->getCountriesIdWhichNeedDni();
        }

        return $this->dniCountriesId;
    }

    private function getPostcodeCountriesId(): array
    {
        if ($this->postcodeCountriesId === null) {
            $this->postcodeCountriesId = $this->countryDataProvider->getCountriesIdWhichNeedPostcode();
        }

        return $this->postcodeCountriesId;
    }
}
