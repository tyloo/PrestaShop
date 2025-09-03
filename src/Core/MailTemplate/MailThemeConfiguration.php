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

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Class MailThemeConfiguration is used to save Mail Theme settings
 * in "Design > Mail Theme" page.
 */
final class MailThemeConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly ThemeCatalogInterface $themeCatalog,
    ) {
    }

    public function getConfiguration()
    {
        return [
            'defaultTheme' => $this->configuration->get('PS_MAIL_THEME'),
        ];
    }

    /**
     * @return list<string>
     */
    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        try {
            $this->validateConfiguration($configuration);
            $this->configuration->set('PS_MAIL_THEME', $configuration['defaultTheme']);
        } catch (CoreException $coreException) {
            $errors[] = $coreException->getMessage();
        }

        return $errors;
    }

    public function validateConfiguration(array $configuration)
    {
        if (empty($configuration['defaultTheme'])) {
            throw new InvalidArgumentException('Default theme can not be empty');
        }

        return $this->themeCatalog->getByName($configuration['defaultTheme']) !== null;
    }
}
