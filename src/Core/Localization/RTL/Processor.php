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

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

use Exception;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
use PrestaShop\PrestaShop\Core\Localization\RTL\Exception\GenerationException;

/**
 * Processes stylesheets by transforming them to RTL.
 */
class Processor
{
    /**
     * @var string Installed language 2-letter ISO code
     */
    private $languageCode = '';

    /**
     * @var bool Indicates if the BO theme should be processed
     */
    private $processBOTheme = false;

    /**
     * @var string[] Names of the FO themes to process
     */
    private array $processFOThemes = [];

    /**
     * @var array Indicates additional paths to process
     */
    private array $processPaths = [];

    /**
     * @var bool Indicates if the default modules should be processed
     */
    private $processDefaultModules = false;

    /**
     * @param string   $adminDir                Path to PrestaShop's admin directory
     * @param string   $themesDir               Path to the FO themes directory
     * @param string[] $defaultModulesToProcess Path to the default modules to process
     */
    public function __construct(
        private $adminDir,
        private $themesDir,
        private readonly array $defaultModulesToProcess,
    ) {
    }

    /**
     * Specifies the installed language 2-letter ISO code.
     *
     * @param string $languageCode
     */
    public function setLanguageCode($languageCode): static
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * Specifies if the BO theme should be processed.
     *
     * @param bool $processBOTheme
     */
    public function setProcessBOTheme($processBOTheme): static
    {
        $this->processBOTheme = $processBOTheme;

        return $this;
    }

    /**
     * Specifies the names of the FO themes to process.
     *
     * @param string[] $processFOThemes
     */
    public function setProcessFOThemes(array $processFOThemes): static
    {
        $this->processFOThemes = $processFOThemes;

        return $this;
    }

    /**
     * Specifies additional paths to process.
     *
     * @param string[] $processPaths
     */
    public function setProcessPaths(array $processPaths): static
    {
        $this->processPaths = $processPaths;

        return $this;
    }

    /**
     * Specifies if the default modules should be processed.
     *
     * @param bool $processDefaultModules
     */
    public function setProcessDefaultModules($processDefaultModules): static
    {
        $this->processDefaultModules = $processDefaultModules;

        return $this;
    }

    /**
     * @throws GenerationException
     * @throws Exception
     */
    public function process(): void
    {
        if ($this->languageCode) {
            $lang_pack = Language::getLangDetails($this->languageCode);
            if (! $lang_pack['is_rtl']) {
                return;
            }
        }

        $generator = new StylesheetGenerator();
        // generate stylesheets for BO themes
        if ($this->processBOTheme) {
            if (! is_dir($this->adminDir)) {
                throw new GenerationException(\sprintf('Cannot generate BO themes: "%s" is not a directory', $this->adminDir));
            }

            $generator->generateInDirectory($this->adminDir . \DIRECTORY_SEPARATOR . 'themes');
        }

        // generate stylesheets for BO themes
        foreach ($this->processFOThemes as $themeName) {
            $generator->generateInDirectory($this->themesDir . \DIRECTORY_SEPARATOR . $themeName);
        }

        // generate stylesheets for default modules
        if ($this->processDefaultModules) {
            $this->processPaths = array_merge($this->processPaths, $this->defaultModulesToProcess);
        }

        foreach ($this->processPaths as $path) {
            if (! empty($path) && is_dir($path)) {
                $generator->generateInDirectory($path);
            }
        }
    }
}
