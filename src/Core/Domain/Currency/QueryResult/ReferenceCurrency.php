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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult;

class ReferenceCurrency
{
    public function __construct(
        private readonly string $isoCode,
        private readonly string $numericIsoCode,
        /**
         * @var string[]
         */
        private readonly array $names,
        /**
         * @var string[]
         */
        private readonly array $symbols,
        /**
         * @var string[]
         */
        private readonly array $patterns,
        private readonly int $precision,
    ) {
    }

    /**
     * Currency ISO code
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * Currency numeric ISO code
     */
    public function getNumericIsoCode(): string
    {
        return $this->numericIsoCode;
    }

    /**
     * Currency's names, indexed by language id.
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * Currency's names, indexed by language id.
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * Currency's patterns, indexed by language id.
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * Currency decimal precision
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }
}
