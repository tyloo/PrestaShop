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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\ConstraintValidator\IsUrlRewriteValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class IsUrlRewriteValidatorTest extends ConstraintValidatorTestCase
{
    private bool $useAscendedChars;

    /**
     * @var ConfigurationInterface
     */
    private \PHPUnit\Framework\MockObject\MockObject $configurationMockWithAscendingCharsOn;

    protected function setUp(): void
    {
        $this->useAscendedChars = false;

        $this->configurationMockWithAscendingCharsOn = $this
            ->getMockBuilder(ConfigurationInterface::class)
            ->getMock()
        ;

        $this->configurationMockWithAscendingCharsOn
            ->method('get')
            ->with('PS_ALLOW_ACCENTED_CHARS_URL')
            ->willReturn(true)
        ;

        parent::setUp();
    }

    public function testItThrowsUnexpectedTypeExceptionOnIncorrectConstraintProvided(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('valid-value', new NotBlank());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getIncorrectTypeRewriteUrls')]
    public function testItThrowsUnexpectedTypeExceptionOnIncorrectValueTypeProvided(bool|array $incorrectTypeRewriteUrl): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($incorrectTypeRewriteUrl, new IsUrlRewrite());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getIncorrectRewriteUrls')]
    public function testItFindsIncorrectUrlRewritePattern(string $incorrectRewriteUrl): void
    {
        $this->validator->validate($incorrectRewriteUrl, new IsUrlRewrite());

        $this->buildViolation((new IsUrlRewrite())->message)
            ->setParameter('%s', '"' . $incorrectRewriteUrl . '"')
            ->assertRaised()
        ;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getCorrectRewriteUrls')]
    public function testItFindsCorrectUrlRewritePatterns(string $correctRewriteUrl): void
    {
        $this->validator->validate($correctRewriteUrl, new IsUrlRewrite());

        $this->assertNoViolation();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getCorrectRewriteUlrUsingAscendingChars')]
    public function testItFindsCorrectUrlRewritePatternUsingAscendedChars(string $correctRewriteUrl): void
    {
        $this->useAscendedChars = true;

        $validator = $this->createValidator();
        $validator->initialize($this->context);

        $validator->validate($correctRewriteUrl, new IsUrlRewrite());

        $this->assertNoViolation();
    }

    public static function getIncorrectTypeRewriteUrls(): array
    {
        return [
            [
                [],
            ],
            [
                true,
            ],
        ];
    }

    public static function getIncorrectRewriteUrls(): array
    {
        return [
            [
                'test@!',
            ],
            [
                '*test2*',
            ],
            [
                'TęstĄČĘĖ',
            ],
            [
                'tes/t/001',
            ],
        ];
    }

    public static function getCorrectRewriteUrls(): array
    {
        return [
            [
                'my-test',
            ],
            [
                'test',
            ],
            [
                '123-589-test',
            ],
        ];
    }

    public static function getCorrectRewriteUlrUsingAscendingChars(): array
    {
        return [
            [
                'aĮأ',
            ],
            [
                'Šarūnas',
            ],
            [
                '_$',
            ],
        ];
    }

    protected function createValidator()
    {
        $configuration = $this->useAscendedChars ?
             $this->configurationMockWithAscendingCharsOn :
             0
        ;

        return new IsUrlRewriteValidator($configuration);
    }
}
