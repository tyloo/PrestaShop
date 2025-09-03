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

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\ConstraintValidator\CustomerNameValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CustomerNameValidatorTest extends ConstraintValidatorTestCase
{
    public static function getInvalidCharacters(): array
    {
        return [
            ['0'], ['1'], ['2'], ['3'], ['4'],
            ['5'], ['6'], ['7'], ['8'], ['9'],
            ['!'], ['<'], ['>'], [','], [';'],
            ['?'], ['='], ['+'], ['('], [')'],
            ['/'], ['\\'], ['@'], ['#'], ['"'],
            ['°'], ['*'], ['`'], ['{'], ['}'],
            ['_'], ['^'], ['$'], ['%'], [':'],
            ['¤'], ['['], [']'], ['|'], ['.'],
            ['。'], ['.  '], ['。  '],
        ];
    }

    public static function getValidCharactersWithSpaces(): array
    {
        return [
            ['. '], ['。 '],
        ];
    }

    public static function getValidCharacters(): array
    {
        return [
            ['.'], ['。'],
        ];
    }

    public function testIfFailsWhenInputIsOnlyBlank(): void
    {
        $this->validator->validate(' ', new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    /**
     * @param string $invalidChar
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getInvalidCharacters')]
    public function testIfFailsWhenBadCharactersAreGiven($invalidChar): void
    {
        $input = 'AZE' . $invalidChar . 'RTY';
        $this->validator->validate($input, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    /**
     * @param string $invalidChar
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getValidCharactersWithSpaces')]
    public function testIfFailsWhenSpacedPointsAreFinal($invalidChar): void
    {
        $input = 'AZERTY' . $invalidChar;
        $this->validator->validate($input, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    /**
     * @param string $invalidChar
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getValidCharacters')]
    public function testIfFailsWhenDoublePoints($invalidChar): void
    {
        $input = 'AZE' . $invalidChar . 'RTY' . $invalidChar;
        $this->validator->validate($input, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    public function testIfSucceedsWhenNoPoints(): void
    {
        $input = 'AZERTY';
        $this->validator->validate($input, new CustomerName());

        $this->assertNoViolation();
    }

    /**
     * @param string $validChar
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getValidCharacters')]
    public function testIfSucceedsWhenPointsAreFinal($validChar): void
    {
        $input = 'AZERTY' . $validChar;
        $this->validator->validate($input, new CustomerName());

        $this->assertNoViolation();
    }

    /**
     * @param string $validChar
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getValidCharactersWithSpaces')]
    public function testIfSucceedsWhenPointsWithSpacesAreGiven($validChar): void
    {
        $input = 'AZE' . $validChar . 'RTY';
        $this->validator->validate($input, new CustomerName());

        $this->assertNoViolation();
    }

    protected function createValidator()
    {
        return new CustomerNameValidator();
    }
}
