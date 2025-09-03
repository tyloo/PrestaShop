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

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector;

return RectorConfig::configure()
    ->withPaths([
        // __DIR__ . '/admin-dev',
        // __DIR__ . '/app',
        // __DIR__ . '/classes',
        // __DIR__ . '/config',
        // __DIR__ . '/controllers',
        // __DIR__ . '/install-dev',
        __DIR__ . '/src/Adapter',
        //__DIR__ . '/src/Core',
        //__DIR__ . '/src/PrestaShopBundle',
        //__DIR__ . '/tests',
        // __DIR__ . '/tools',
        // __DIR__ . '/webservice',
    ])
    ->withPhpSets(php81: true)
    ->withComposerBased(
        twig: true,
        doctrine: true,
        phpunit: true,
        symfony: true,
    )
    ->withAttributesSets(
        symfony: true,
        doctrine: true,
    )
    ->withTypeCoverageLevel(0) // max = 53
    ->withDeadCodeLevel(20) // max = 51
    ->withCodeQualityLevel(20) // max = 74
    ->withCodingStyleLevel(20) // max = 25
    ->withSkip([
        UnwrapFutureCompatibleIfPhpVersionRector::class,
    ])
    ->withParallel()
;
