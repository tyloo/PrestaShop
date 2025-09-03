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

namespace PrestaShop\PrestaShop\Core\Localization;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;

/**
 * Locale entity.
 *
 * This is the main CLDR entry point. For example, Locale is used to format numbers, prices, percentages.
 * To build a Locale instance, use the Locale repository.
 */
class Locale implements LocaleInterface
{
    public const NUMBERING_SYSTEM_LATIN = LocaleInterface::NUMBERING_SYSTEM_LATIN;

    /**
     * @param string           $code
     *                                              The locale code (simplified IETF tag syntax)
     *                                              Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                                              eg: fr-FR, en-US
     * @param NumberInterface  $numberSpecification
     *                                              Number specification used when formatting a number
     * @param NumberCollection $priceSpecifications
     *                                              Collection of Price specifications (one per installed currency)
     * @param NumberFormatter  $numberFormatter
     *                                              This number formatter will use stored number / price specs
     */
    public function __construct(
        protected string $code,
        protected NumberInterface $numberSpecification,
        protected NumberCollection $priceSpecifications,
        protected NumberFormatter $numberFormatter,
    ) {
    }

    /**
     * Get this locale's code (simplified IETF tag syntax)
     * Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     * eg: fr-FR, en-US.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Format a number according to locale rules.
     *
     * @param int|float|string $number
     *                                 The number to be formatted
     *
     * @return string
     *                The formatted number
     *
     * @throws LocalizationException
     */
    public function formatNumber(int|float|string $number): string
    {
        return $this->numberFormatter->format(
            $number,
            $this->numberSpecification
        );
    }

    /**
     * Format a number as a price.
     *
     * @param int|float|string $number
     *                                       Number to be formatted as a price
     * @param string           $currencyCode
     *                                       Currency of the price
     *
     * @return string The formatted price
     *
     * @throws LocalizationException
     */
    public function formatPrice(int|float|string $number, string $currencyCode): string
    {
        return $this->numberFormatter->format(
            $number,
            $this->getPriceSpecification($currencyCode)
        );
    }

    /**
     * @param string $currencyCode Currency of the price
     */
    public function getPriceSpecification(string $currencyCode): NumberInterface
    {
        $priceSpec = $this->priceSpecifications->get($currencyCode);
        if ($priceSpec === null) {
            throw new LocalizationException('Price specification not found for currency: "' . $currencyCode . '"');
        }

        return $priceSpec;
    }

    public function getNumberSpecification(): NumberInterface
    {
        return $this->numberSpecification;
    }
}
