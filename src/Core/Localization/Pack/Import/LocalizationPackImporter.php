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

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

use PrestaShop\PrestaShop\Core\Localization\Pack\Factory\LocalizationPackFactoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LocalizationPackImporter is responsible for importing localization pack.
 */
final class LocalizationPackImporter implements LocalizationPackImporterInterface
{
    public function __construct(
        private readonly LocalizationPackLoaderInterface $remoteLocalizationPackLoader,
        private readonly LocalizationPackLoaderInterface $localLocalizationPackLoader,
        private readonly LocalizationPackFactoryInterface $localizationPackFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function import(LocalizationPackImportConfig $config)
    {
        $errors = $this->checkConfig($config);
        if ($errors !== []) {
            return $errors;
        }

        $pack = null;

        if ($config->shouldDownloadPackData()) {
            $pack = $this->remoteLocalizationPackLoader->getLocalizationPack(
                $config->getCountryIsoCode()
            );
        }

        if ($pack === null) {
            $pack = $this->localLocalizationPackLoader->getLocalizationPack(
                $config->getCountryIsoCode()
            );

            if ($pack === null) {
                $error = $this->trans('Cannot load the localization pack.', 'Admin.International.Notification');

                return [$error];
            }
        }

        $localizationPack = $this->localizationPackFactory->createNew();

        $localizationPack->loadLocalisationPack(
            $pack,
            $config->getContentToImport(),
            false,
            $config->getCountryIsoCode()
        );

        return $localizationPack->getErrors();
    }

    /**
     * Check if configuration is valid.
     *
     * @return array Errors if any
     */
    private function checkConfig(LocalizationPackImportConfig $config): array
    {
        if (\in_array($config->getCountryIsoCode(), ['', '0'], true)) {
            $error = $this->trans('Invalid selection', 'Admin.Notifications.Error');

            return [$error];
        }

        if ($config->getContentToImport() === []) {
            $error = $this->trans('Please select at least one item to import.', 'Admin.International.Notification');

            return [$error];
        }

        $contentItems = [
            LocalizationPackImportConfigInterface::CONTENT_STATES,
            LocalizationPackImportConfigInterface::CONTENT_TAXES,
            LocalizationPackImportConfigInterface::CONTENT_CURRENCIES,
            LocalizationPackImportConfigInterface::CONTENT_LANGUAGES,
            LocalizationPackImportConfigInterface::CONTENT_UNITS,
            LocalizationPackImportConfigInterface::CONTENT_GROUPS,
        ];

        foreach ($config->getContentToImport() as $contentItem) {
            if (! \in_array($contentItem, $contentItems, true)) {
                $error = $this->trans('Invalid selection', 'Admin.Notifications.Error');

                return [$error];
            }
        }

        return [];
    }

    /**
     * Translate message.
     *
     * @param string $message
     * @param string $domain
     */
    private function trans($message, $domain, array $params = []): string
    {
        return $this->translator->trans($message, $params, $domain);
    }
}
