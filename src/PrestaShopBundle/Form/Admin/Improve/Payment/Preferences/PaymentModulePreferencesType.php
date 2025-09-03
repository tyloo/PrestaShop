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

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PaymentModulePreferencesType defines form in "Improve > Payment > Preferences" page.
 */
class PaymentModulePreferencesType extends TranslatorAwareType
{
    private readonly array $paymentModules;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules,
        private array $countryChoices,
        private readonly array $groupChoices,
        private readonly array $carrierChoices,
        private readonly CurrencyByIdChoiceProvider $currencyChoicesProvider,
        private readonly CountryDataProvider $countryDataProvider,
    ) {
        parent::__construct($translator, $locales);
        $this->paymentModules = $this->sortPaymentModules($paymentModules);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->getCurrencyChoices(),
                'multiple_choices' => $this->getCurrencyChoicesForPaymentModules(),
                'headers_fixed' => true,
            ])
            ->add('country_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->countryChoices,
                'multiple_choices' => $this->getCountryChoicesForPaymentModules(),
                'headers_fixed' => true,
            ])
            ->add('group_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Group restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->groupChoices,
                'multiple_choices' => $this->getGroupChoicesForPaymentModules(),
                'headers_fixed' => true,
            ])
            ->add('carrier_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Carrier restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->carrierChoices,
                'multiple_choices' => $this->getCarrierChoicesForPaymentModules(),
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple currency choices for payment modules.
     */
    private function getCurrencyChoicesForPaymentModules(): array
    {
        $choices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $moduleInstance = $paymentModule->getInstance();

            if ($moduleInstance->currencies_mode === 'radio') {
                $allowMultipleCurrencies = false;
                $currencyChoices = $this->getCurrencyChoices();
            } else {
                $allowMultipleCurrencies = true;
                $currencyChoices = $this->currencyChoicesProvider->getChoices();
            }

            $choices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => $allowMultipleCurrencies,
                'choices' => $currencyChoices,
            ];
        }

        return $choices;
    }

    /**
     * Get multiple country choices for payment modules.
     */
    private function getCountryChoicesForPaymentModules(): array
    {
        $multipleChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $limitedCountries = $paymentModule->get('limited_countries');

            if (\is_array($limitedCountries) && ! empty($limitedCountries)) {
                $countryChoices = $this->getLimitedCountryChoices($limitedCountries);
            } else {
                $countryChoices = $this->countryChoices;
            }

            $multipleChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $countryChoices,
            ];
        }

        return $multipleChoices;
    }

    /**
     * Get multiple group choices for payment modules.
     */
    private function getGroupChoicesForPaymentModules(): array
    {
        $groupChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $groupChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->groupChoices,
            ];
        }

        return $groupChoices;
    }

    /**
     * Get multiple carrier choices for payment modules.
     */
    private function getCarrierChoicesForPaymentModules(): array
    {
        $carrierChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $carrierChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->carrierChoices,
            ];
        }

        return $carrierChoices;
    }

    /**
     * Get currency choices with specific addtional choices.
     */
    private function getCurrencyChoices(): array
    {
        return array_merge(
            $this->currencyChoicesProvider->getChoices(),
            $this->getAdditionalCurrencyChoices()
        );
    }

    /**
     * Get payment preferences specific currency choices.
     */
    private function getAdditionalCurrencyChoices(): array
    {
        return [
            $this->trans('Customer currency', 'Admin.Payment.Feature') => -1,
            $this->trans('Shop default currency', 'Admin.Payment.Feature') => -2,
        ];
    }

    /**
     * Get country choices by country ISO codes.
     */
    private function getLimitedCountryChoices(array $limitedCountryIsoCodes): array
    {
        $countryChoices = [];

        foreach ($limitedCountryIsoCodes as $isoCode) {
            $countryId = $this->countryDataProvider->getIdByIsoCode($isoCode);
            $countryValueIndex = array_search($countryId, $this->countryChoices, true);
            if ($countryId !== false && $countryValueIndex !== false) {
                $countryChoices[] = $this->countryChoices[$countryValueIndex];
            }
        }

        return $countryChoices;
    }

    /**
     * Sort payment modules by display name.
     */
    private function sortPaymentModules(array $paymentModules): array
    {
        $sortingBy = [];

        foreach ($paymentModules as $key => $paymentModule) {
            $sortingBy[$key] = $paymentModule->get('displayName');
        }

        array_multisort($sortingBy, \SORT_ASC, $paymentModules);

        return $paymentModules;
    }
}
