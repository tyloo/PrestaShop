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

namespace PrestaShop\PrestaShop\Adapter\Feature;

use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;

/**
 * Class MultistoreFeature provides data about multishop feature usage.
 *
 * @internal
 */
class MultistoreFeature implements FeatureInterface
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function isUsed()
    {
        // internally it checks if feature is active
        // and at least 2 shops exist
        return Shop::isFeatureActive();
    }

    public function isActive()
    {
        return (bool) $this->configuration->get(MultistoreConfig::FEATURE_STATUS);
    }

    public function enable()
    {
        $this->configuration->set(MultistoreConfig::FEATURE_STATUS, 1);
    }

    public function disable()
    {
        $this->configuration->set(MultistoreConfig::FEATURE_STATUS, 0);
    }

    public function update($status)
    {
        $status ? $this->enable() : $this->disable();
    }
}
