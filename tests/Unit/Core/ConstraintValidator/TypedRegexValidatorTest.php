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

use Generator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class TypedRegexValidatorTest extends ConstraintValidatorTestCase
{
    protected const ALLOW_ACCENTED_CHARS_CONFIG_NAME = 'PS_ALLOW_ACCENTED_CHARS_URL';

    /**
     * Modify this configuration data before creating new validator when needed to inject different configuration values into validator
     */
    protected $configurationData = [
        self::ALLOW_ACCENTED_CHARS_CONFIG_NAME => '0',
    ];

    public function testItSucceedsForNameTypeWhenValidCharactersGiven(): void
    {
        $value = 'goodname';
        $this->validator->validate($value, new TypedRegex(['type' => 'name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForNameType
     */
    public function testItFailsForNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'name']), $invalidChar);
    }

    public function testItSucceedsForCatalogNameTypeWhenValidCharactersGiven(): void
    {
        $value = 'catalog name';
        $this->validator->validate($value, new TypedRegex(['type' => 'catalog_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForCatalogNameType
     */
    public function testItFailsForCatalogNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'catalog_name']), $invalidChar);
    }

    public function testItSucceedsForGenericNameTypeWhenValidCharactersGiven(): void
    {
        $value = 'good generic name /';
        $this->validator->validate($value, new TypedRegex(['type' => 'generic_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForGenericNameType
     */
    public function testItFailsForGenericNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'generic_name']), $invalidChar);
    }

    public function testItSucceedsForCityNameTypeWhenValidCharactersGiven(): void
    {
        $value = 'London';
        $this->validator->validate($value, new TypedRegex(['type' => 'city_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForCityNameType
     */
    public function testItFailsForCityNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'city_name']), $invalidChar);
    }

    public function testItSucceedsForAddressTypeWhenValidCharactersGiven(): void
    {
        $value = '3197 Hillview Drive';
        $this->validator->validate($value, new TypedRegex(['type' => 'address']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForAddressType
     */
    public function testItFailsForAddressTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'address']), $invalidChar);
    }

    public function testItSucceedsForPostCodeTypeWhenValidCharactersGiven(): void
    {
        $value = '94103';
        $this->validator->validate($value, new TypedRegex(['type' => 'post_code']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForPostCodeType
     */
    public function testItFailsForPostCodeTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'post_code']), $invalidChar);
    }

    public function testItSucceedsForPhoneNumberTypeWhenValidCharactersGiven(): void
    {
        $value = '707-216-7924';
        $this->validator->validate($value, new TypedRegex(['type' => 'phone_number']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForPhoneNumberType
     */
    public function testItFailsForPhoneNumberTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'phone_number']), $invalidChar);

        $this->buildViolation((new TypedRegex(['type' => 'phone_number']))->message)
            ->setParameter('%s', '"' . $invalidChar . '"')
            ->assertRaised();
    }

    public function testItSucceedsForMessageTypeWhenValidCharactersGiven(): void
    {
        $value = 'some random message #)F@$. ';
        $this->validator->validate($value, new TypedRegex(['type' => 'message']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForMessageType
     */
    public function testItFailsForMessageTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'message']), $invalidChar);
    }

    public function testItSucceedsForLanguageIsoCodeTypeWhenValidCharactersGiven(): void
    {
        $value = 'US';
        $this->validator->validate($value, new TypedRegex(['type' => 'language_iso_code']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForLanguageIsoCodeType
     */
    public function testItFailsForLanguageIsoCodeTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'language_iso_code']), $invalidChar);
    }

    public function testItSucceedsForLanguageCodeTypeWhenValidCharactersGiven(): void
    {
        $value = 'lt-LT';
        $this->validator->validate($value, new TypedRegex(['type' => 'language_code']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForLanguageCodeType
     */
    public function testItFailsForLanguageCodeTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'language_code']), $invalidChar);
    }

    public function testItSucceedsForUpcTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('12345678901', new TypedRegex('upc'));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForUpc
     */
    public function testItFailsForUpcTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('upc'), $invalidChar);
    }

    public function testItSucceedsForEan13TypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('1780201379629', new TypedRegex('ean_13'));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForEan13
     */
    public function testItFailsForEan13TypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('ean_13'), $invalidChar);
    }

    public function testItSucceedsForIsbnTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('0-8044-2957-X', new TypedRegex('isbn'));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForIsbn
     */
    public function testItFailsForIsbnTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('isbn'), $invalidChar);
    }

    /**
     * @dataProvider getInvalidCharactersForReference
     */
    public function testItFailsForReferenceTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('reference'), $invalidChar);
    }

    public function testItSucceedsForReferenceTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('product1', new TypedRegex('reference'));

        $this->assertNoViolation();
    }

    public function testItSucceedsForUrlTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('test.com', new TypedRegex(['type' => 'url']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForUrl
     */
    public function testItFailsForUrlTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'url']), $invalidChar);
    }

    public function testItSucceedsForModuleNameTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('my-name', new TypedRegex(['type' => 'module_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForModuleName
     */
    public function testItFailsForModuleNameTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'module_name']), $invalidChar);
    }

    public function testItSucceedsForWebserviceKeyTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('22XRNQR7X4RLAGCBSSNQIVPXQ271ZIKE', new TypedRegex(['type' => TypedRegex::TYPE_WEBSERVICE_KEY]));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForWebserviceKey
     */
    public function testItFailsForWebserviceKeyTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => TypedRegex::TYPE_WEBSERVICE_KEY]), $invalidChar);
    }

    public function testItSucceedsForZipCodeFormatTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('LLLNNNNCCClllnnnccc-1234567890', new TypedRegex(['type' => TypedRegex::TYPE_WEBSERVICE_KEY]));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidZipCodeFormats
     */
    public function testItFailsForZipCodeFormatTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => TypedRegex::TYPE_ZIP_CODE_FORMAT]), $invalidChar);
    }

    public function testItSucceedsForStateIsoCodeTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('FRA', new TypedRegex(['type' => TypedRegex::TYPE_STATE_ISO_CODE]));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForStateIsoCode
     */
    public function testItFailsForStateIsoCodeTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => TypedRegex::TYPE_STATE_ISO_CODE]), $invalidChar);
    }

    /**
     * @dataProvider getInvalidCharactersForDiscountCode
     */
    public function testItFailsForDiscountCodeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => TypedRegex::TYPE_DISCOUNT_CODE]), $invalidChar);
    }

    /**
     * @dataProvider getDataForDiscountCodeTest
     */
    public function testDiscountCode(string $value, bool $expectSuccess): void
    {
        $constraint = new TypedRegex(TypedRegex::TYPE_DISCOUNT_CODE);
        $this->validator->validate($value, $constraint);

        if ($expectSuccess) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->message)
                ->setParameter('%s', '"' . $value . '"')
                ->assertRaised();
        }
    }

    /**
     * @dataProvider getDataForLinkRewriteTest
     */
    public function testLinkRewriteType(string $value, string $allowAccentedChars, bool $expectSuccess): void
    {
        $this->configurationData[self::ALLOW_ACCENTED_CHARS_CONFIG_NAME] = $allowAccentedChars;
        $this->reinitializeValidator([
            self::ALLOW_ACCENTED_CHARS_CONFIG_NAME => $allowAccentedChars,
        ]);

        $constraint = new TypedRegex(TypedRegex::TYPE_LINK_REWRITE);
        $this->validator->validate($value, $constraint);

        if ($expectSuccess) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->message)
                ->setParameter('%s', '"' . $value . '"')
                ->assertRaised();
        }
    }

    /**
     * @dataProvider getDataForTestHTMLType
     */
    public function testHTMLType(string $value, bool $allowIframe, bool $expectSuccess): void
    {
        if ($allowIframe) {
            $type = TypedRegex::CLEAN_HTML_ALLOW_IFRAME;
        } else {
            $type = TypedRegex::CLEAN_HTML_NO_IFRAME;
        }

        $constraint = new TypedRegex($type);

        $this->validator->validate($value, $constraint);

        if ($expectSuccess) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->message)
                ->setParameter('%s', '"' . $value . '"')
                ->assertRaised();
        }
    }

    public function getDataForTestHTMLType(): iterable
    {
        yield ['whatever', false, true];
        yield ['whatever', true, true];
        yield ['<html></html>', true, true];
        yield ['<html></html>', false, true];
        yield ['<div></div>', true, true];
        yield ['<div></div>', false, true];
        yield ['<iframe>', true, true];
        yield ['<iframe>', false, false];
        yield ['<script>', true, false];
        yield ['<script>', false, false];

        $events = [
            'onmousedown', 'onmousemove', 'onmmouseup', 'onmouseover', 'onmouseout', 'onload', 'onunload', 'onfocus',
            'onblur', 'onchange', 'onsubmit', 'ondblclick', 'onclick', 'onkeydown', 'onkeyup', 'onkeypress', 'onmouseenter',
            'onmouseleave', 'onerror', 'onselect', 'onreset', 'onabort', 'ondragdrop', 'onresize', 'onactivate', 'onafterprint',
            'onmoveend', 'onafterupdate', 'onbeforeactivate', 'onbeforecopyonbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus',
            'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onmove', 'onbounce', 'oncellchange',
            'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete',
            'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'onmousewheel', 'ondragleave', 'ondragover', 'ondragstart',
            'ondrop', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocusin', 'onfocusout', 'onhashchange', 'onhelp',
            'oninput', 'onlosecapture', 'onmessage', 'onmouseup', 'onmovestart', 'onoffline', 'ononline', 'onpaste', 'onpropertychange',
            'onreadystatechange', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted',
            'onscroll', 'onsearch', 'onselectionchange', 'onselectstart', 'onstart',
        ];

        foreach ($events as $event) {
            yield [\sprintf('<div %s="whatever">', $event), true, false];
            yield [\sprintf('<div %s="whatever">', $event), false, false];
        }
    }

    /**
     * @return array[]
     */
    public function getDataForLinkRewriteTest(): array
    {
        return [
            ['okay', '0', true],
            ['Notebook-13', '0', true],
            ['Notebook_3', '0', true],
            ['notebook-ė', '0', false],
            ['notebook-ė', '1', true],
            ['notebook with spaces', '1', false],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForNameType(): array
    {
        return [
            ['0'], ['2'], ['<'], ['>'], ['?'], ['#'], ['%'], [','], [';'], ['+'], ['¤'], [':'], ['!'], ['='], ['#'],
            ['"'], ['$'], ['}'], ['{'], ['@'], ['|'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForCatalogNameType(): array
    {
        return [
            ['<'], ['>'], ['{'], ['}'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForGenericNameType(): array
    {
        return [
            ['<'], ['>'], ['{'], ['}'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForCityNameType(): array
    {
        return [
            ['!'], ['>'], ['<'], [';'], ['?'], ['='], ['+'], ['@'], ['#'], ['"'], ['°'], ['{'], ['}'], ['_'],
            ['$'], ['%'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForAddressType(): array
    {
        return [
            ['!'], ['>'], ['<'], ['?'], ['='], ['+'], ['@'], ['{'], ['}'], ['_'], ['$'], ['%'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForPostCodeType(): array
    {
        return [
            ['<'], ['>'], ['?'], ['#'], ['%'], [','], [';'], ['+'], ['¤'], [':'], ['!'], ['='], ['#'],
            ['"'], ['$'], ['}'], ['{'], ['@'], ['|'], ['ž'], ['Š'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForPhoneNumberType(): array
    {
        return [
            ['<'], ['>'], ['?'], ['#'], ['%'], [','], [';'], ['¤'], [':'], ['!'], ['='], ['#'],
            ['"'], ['$'], ['}'], ['{'], ['@'], ['|'], ['ž'], ['Š'], ['r'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForMessageType(): array
    {
        return [
            ['<'], ['>'], ['{'], ['}'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForLanguageIsoCodeType(): array
    {
        return [
            ['a'], ['ž'], ['abcd'], ['2'], ['26'], ['ABCE'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForLanguageCodeType(): array
    {
        return [
            ['az-acc'], ['1'], ['12-22'], ['ži-as'],
        ];
    }

    public function getInvalidCharactersForUpc(): Generator
    {
        yield ['1234567890013'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['-'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ["'"];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    public function getInvalidCharactersForEan13(): Generator
    {
        yield ['10000000000014'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['-'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ["'"];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    public function getInvalidCharactersForIsbn(): Generator
    {
        yield ['12345678901234567890123412345678901234-33'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ["'"];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    public function getInvalidCharactersForReference(): Generator
    {
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['<'];
        yield ['>'];
        yield [';'];
    }

    public function getInvalidCharactersForUrl(): Generator
    {
        yield ['!'];
        yield ['"'];
        yield ["'"];
        yield ['*'];
        yield ['§'];
        yield ['{'];
        yield ['['];
        yield [']'];
        yield ['}'];
        yield ['\\'];
        yield [';'];
    }

    public function getInvalidCharactersForModuleName(): Generator
    {
        yield ['~'];
        yield ['ˇ'];
        yield ['"'];
        yield ['@'];
        yield ['#'];
        yield ['€'];
        yield ['$'];
        yield ['£'];
        yield ['%'];
        yield ['&'];
        yield ['§'];
        yield ['/'];
        yield ['('];
        yield [')'];
        yield ['='];
        yield ['?'];
        yield ['`'];
        yield ['\\'];
        yield ['}'];
        yield [']'];
        yield ['['];
        yield ['{'];
        yield ["'"];
        yield ['*'];
        yield ['.'];
        yield [','];
        yield [':'];
        yield [';'];
        yield ['<'];
        yield ['>'];
        yield ['|'];
    }

    public function getInvalidCharactersForStateIsoCode(): Generator
    {
        yield ['FRANC'];
        yield ['~'];
        yield ['ˇ'];
        yield ['"'];
        yield ['@'];
        yield ['#'];
        yield ['€'];
        yield ['$'];
        yield ['£'];
        yield ['%'];
        yield ['&'];
        yield ['§'];
        yield ['/'];
        yield ['('];
        yield [')'];
        yield ['='];
        yield ['?'];
        yield ['`'];
        yield ['\\'];
        yield ['}'];
        yield [']'];
        yield ['['];
        yield ['{'];
        yield ["'"];
        yield ['*'];
        yield ['.'];
        yield [','];
        yield [':'];
        yield [';'];
        yield ['<'];
        yield ['>'];
        yield ['|'];
    }

    public function getInvalidCharactersForWebServiceKey(): Generator
    {
        yield ['~'];
        yield ['ˇ'];
        yield ['"'];
        yield ['€'];
        yield ['$'];
        yield ['£'];
        yield ['%'];
        yield ['&'];
        yield ['§'];
        yield ['/'];
        yield ['('];
        yield [')'];
        yield ['='];
        yield ['`'];
        yield ['\\'];
        yield ["'"];
        yield ['}'];
        yield [']'];
        yield ['['];
        yield ['{'];
        yield ['*'];
        yield ['.'];
        yield [','];
        yield [':'];
        yield [';'];
        yield ['<'];
        yield ['>'];
        yield ['|'];
        yield [' '];
    }

    public function getInvalidZipCodeFormats(): Generator
    {
        yield ['A'];
        yield ['NNA'];
        yield ['1QER'];
        yield ['123QDQ'];
        yield ['LA'];
        yield ['£'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ["'"];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    public function getInvalidCharactersForDiscountCode(): Generator
    {
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ["'"];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    /**
     * @return array[]
     */
    public function getDataForDiscountCodeTest(): array
    {
        return [
            ['CODE123', true],
            ['code123', true],
            ['CODE-123', true],
            ['CODE_123', true],
            ['CODE[]123', false],
            ['CODE{}123', false],
            ['CODE)(123', false],
        ];
    }

    protected function createValidator(): TypedRegexValidator
    {
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('get')
            ->willReturnMap(
                [
                    ['PS_ALLOW_ACCENTED_CHARS_URL', $this->configurationData['PS_ALLOW_ACCENTED_CHARS_URL']],
                ]
            );

        return new TypedRegexValidator($configurationMock);
    }

    private function assertViolationIsRaised(TypedRegex $constraint, string $invalidChar): void
    {
        $this->validator->validate($invalidChar, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('%s', '"' . $invalidChar . '"')
            ->assertRaised();
    }

    /**
     * Reinitialize validator when custom configuration data needs to be injected
     */
    private function reinitializeValidator(array $configurationData): void
    {
        $this->configurationData = $configurationData;
        $this->validator = $this->createValidator();
        $this->validator->initialize($this->context);
    }
}
