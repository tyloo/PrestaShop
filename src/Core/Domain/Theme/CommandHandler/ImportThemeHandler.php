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

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\Exception\ThemeAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeUploaderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\ImportThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\ImportedThemeAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeImportSource;
use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeName;

#[AsCommandHandler]
final class ImportThemeHandler implements ImportThemeHandlerInterface
{
    public function __construct(
        private readonly ThemeUploaderInterface $themeUploader,
        private readonly ThemeManager $themeManager,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function handle(ImportThemeCommand $command)
    {
        $type = $command->getImportSource()->getSourceType();
        $source = $command->getImportSource()->getSource();

        $themePath = '';
        if ($type === ThemeImportSource::FROM_ARCHIVE) {
            $themePath = $this->themeUploader->upload($source);
        } elseif ($type === ThemeImportSource::FROM_WEB) {
            $themePath = $source;
        } elseif ($type === ThemeImportSource::FROM_FTP) {
            $themePath = $this->configuration->get('_PS_ALL_THEMES_DIR_') . $source;
        }

        try {
            $this->themeManager->install($themePath);
        } catch (ThemeAlreadyExistsException $themeAlreadyExistsException) {
            throw new ImportedThemeAlreadyExistsException(new ThemeName($themeAlreadyExistsException->getThemeName()), \sprintf('Imported theme "%s" already exists.', $themeAlreadyExistsException->getThemeName()), 0, $themeAlreadyExistsException);
        } finally {
            if ($type === ThemeImportSource::FROM_ARCHIVE) {
                @unlink($themePath);
            }
        }
    }
}
