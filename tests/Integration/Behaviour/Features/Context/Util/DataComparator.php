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

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Exception;

class DataComparator
{
    /**
     * @throws Exception
     */
    public static function assertDataSetsAreIdentical(array $expectedData, array $realData)
    {
        foreach ($expectedData as $key => $expectedElement) {
            if (\array_key_exists($key, $realData) === false) {
                $availableKeys = array_keys($realData);
                throw new Exception(\sprintf('Expected data %s but no such data in real data ; available data is ', $key) . implode(',', $availableKeys));
            }

            $realElement = $realData[$key];
            $realElementType = \gettype($realElement);

            if (($realElementType === 'array') && \array_key_exists('value', $realElement)) {
                $realElement = $realElement['value'];
                $realElementType = \gettype($realElement);
            }

            $isADateTime = (($realElementType === 'object') && ($realElement::class === 'DateTime'));
            if ($isADateTime) {
                $realElementType = 'datetime';
            }

            $castedExpectedElement = PrimitiveUtils::castElementInType($expectedElement, $realElementType);

            if (PrimitiveUtils::isIdentical($castedExpectedElement, $realElement) === false) {
                if ($realElementType === 'boolean') {
                    $realAsString = ($realElement) ? 'true' : 'false';
                    $expectedAsString = ($castedExpectedElement) ? 'true' : 'false';

                    throw new Exception(\sprintf('Real %s is ', $key) . $realAsString . ' / expected ' . $expectedAsString);
                }

                if ($realElementType === 'array') {
                    sort($realElement);
                    sort($castedExpectedElement);

                    $realAsString = implode('; ', $realElement);
                    $expectedAsString = implode('; ', $castedExpectedElement);

                    if ($realAsString === '') {
                        $realAsString = 'empty';
                    }

                    if ($expectedAsString === '') {
                        $expectedAsString = 'empty';
                    }

                    throw new Exception(\sprintf('Real %s is %s / expected %s', $key, $realAsString, $expectedAsString));
                } elseif ($realElementType === 'datetime') {
                    $realAsString = $realElement->format('Y/m/d H:i:s');
                    $expectedAsString = $castedExpectedElement->format('Y/m/d H:i:s');

                    throw new Exception(\sprintf('Real %s is %s / expected %s', $key, $realAsString, $expectedAsString));
                } else {
                    throw new Exception(\sprintf('Real %s is ', $key) . $realElement . ' / expected ' . $castedExpectedElement);
                }
            }
        }
    }
}
