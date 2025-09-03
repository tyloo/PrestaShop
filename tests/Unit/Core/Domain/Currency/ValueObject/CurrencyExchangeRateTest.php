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

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;

class CurrencyExchangeRateTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('getIncorrectExchangeRates')]
    public function testItThrowsAnExceptionOnIncorrectExchangeRate(int|string $incorrectExchangeRate): void
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_EXCHANGE_RATE);

        new ExchangeRate($incorrectExchangeRate);
    }

    public static function getIncorrectExchangeRates(): array
    {
        return [
            [
                0,
            ],
            [
                '-1',
            ],
            [
                '4.294.967.295,000',
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getCorrectExchangeRates')]
    public function testItGetsExpectedExchangeRate(float|int $correctRate): void
    {
        $exchangeRate = new ExchangeRate($correctRate);

        $this->assertEquals($correctRate, $exchangeRate->getValue());
    }

    public static function getCorrectExchangeRates(): Generator
    {
        yield [1.55];
        yield [1];
        yield [0.55];
    }
}
