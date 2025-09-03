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

namespace PrestaShopBundle\Translation\Exporter;

use Exception;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use PrestaShopBundle\Utils\ZipManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeExporter
{
    /**
     * @var string the cache directory path
     */
    public $cacheDir;

    /**
     * @var string the export directory path
     */
    public $exportDir;

    /**
     * @var ThemeProvider the theme provider
     */
    private readonly ThemeProvider $themeProvider;

    public function __construct(
        /**
         * @var ThemeExtractor the theme extractor
         */
        private readonly ThemeExtractor $themeExtractor,
        ThemeProvider $themeProvider,
        /**
         * @var ThemeRepository the theme repository
         */
        private readonly ThemeRepository $themeRepository,
        /**
         * @var XliffFileDumper the Xliff dumper
         */
        private readonly XliffFileDumper $dumper,
        /**
         * @var ZipManager the zip manager
         */
        private readonly ZipManager $zipManager,
        /**
         * @var Filesystem the Filesystem
         */
        private readonly Filesystem $filesystem,
    ) {
        $this->themeExtractor
            ->setThemeProvider($themeProvider);

        $this->themeProvider = $themeProvider;
    }

    /**
     * @param string $themeName
     * @param bool   $rootDir
     */
    public function createZipArchive($themeName, string $locale, $rootDir = false): string
    {
        $archiveParentDirectory = $this->exportCatalogues($themeName, $locale, $rootDir);
        $zipFilename = $this->makeZipFilename($themeName, $locale);
        $this->zipManager->createArchive($zipFilename, $archiveParentDirectory);

        return $zipFilename;
    }

    /**
     * @param string $themeName
     * @param bool   $rootDir
     */
    public function exportCatalogues($themeName, string $locale, $rootDir = false): string
    {
        $this->themeProvider->setLocale($locale);
        $this->themeProvider->setThemeName($themeName);

        $mergedTranslations = $this->getCatalogueExtractedFromTemplates($themeName, $locale, $rootDir);

        try {
            $themeCatalogue = $this->themeProvider->getThemeCatalogue();
        } catch (Exception) {
            $themeCatalogue = new MessageCatalogue($locale, []);
        }

        $databaseCatalogue = $this->themeProvider->getDatabaseCatalogue($themeName);
        $databaseCatalogue = $this->addLocaleToDomain($locale, $databaseCatalogue);

        $mergedTranslations->addCatalogue($themeCatalogue);
        $mergedTranslations->addCatalogue($databaseCatalogue);

        $this->updateCatalogueMetadata($mergedTranslations);

        $archiveParentDirectory = $this->makeArchiveParentDirectory($themeName, $locale);

        if ($this->ensureFileBelongsToExportDirectory($archiveParentDirectory)) {
            // Clean up previously exported archives
            $this->filesystem->remove($archiveParentDirectory);
        }

        $this->filesystem->mkdir($archiveParentDirectory);

        $this->dumper->dump($mergedTranslations, [
            'path' => $archiveParentDirectory,
            'default_locale' => $locale,
            'root_dir' => $rootDir,
            'split_files' => false,
        ]);

        $this->renameCatalogues($locale, $archiveParentDirectory);

        return $archiveParentDirectory;
    }

    /**
     * @param string $exportDir
     */
    public function setExportDir($exportDir): void
    {
        $this->exportDir = str_replace('/export', \DIRECTORY_SEPARATOR . 'export', $exportDir);
    }

    public function cleanArtifacts(string $themeName): void
    {
        $this->filesystem->remove($this->getFlattenizationFolder($themeName));
        $this->filesystem->remove($this->getTemporaryExtractionFolder($themeName));
    }

    /**
     * @param string $filePath
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function ensureFileBelongsToExportDirectory($filePath)
    {
        if (! $this->filesystem->exists($filePath)) {
            return false;
        }

        $validFileLocation = str_starts_with(realpath($filePath), realpath($this->exportDir));

        if (! $validFileLocation) {
            throw new Exception('Invalid file location. This file should belong to the export directory');
        }

        return $validFileLocation;
    }

    /**
     * @param string $themeName
     * @param bool   $rootDir
     *
     * @return MessageCatalogue
     */
    protected function getCatalogueExtractedFromTemplates($themeName, string $locale, $rootDir = false)
    {
        $tmpFolderPath = $this->getTemporaryExtractionFolder($themeName);
        $folderPath = $this->getFlattenizationFolder($themeName);

        $this->filesystem->remove($folderPath);
        $this->filesystem->remove($tmpFolderPath);

        $this->filesystem->mkdir($folderPath);
        $this->filesystem->mkdir($tmpFolderPath);

        $theme = $this->themeRepository->getInstanceByName($themeName);
        $this->themeExtractor
            ->setOutputPath($tmpFolderPath)
            ->extract($theme, $locale, $rootDir);

        Flattenizer::flatten($tmpFolderPath . \DIRECTORY_SEPARATOR . $locale, $folderPath . \DIRECTORY_SEPARATOR . $locale, $locale);

        return $this->themeProvider->getCatalogueFromPaths([$folderPath], $locale, '*');
    }

    protected function renameCatalogues(string $locale, string $archiveParentDirectory)
    {
        $finder = Finder::create();

        foreach ($finder->in($archiveParentDirectory . \DIRECTORY_SEPARATOR . $locale)->files() as $file) {
            $parentDirectoryParts = explode(\DIRECTORY_SEPARATOR, \dirname($file));
            $destinationFilenameParts = [
                $archiveParentDirectory,
                $parentDirectoryParts[\count($parentDirectoryParts) - 1] . '.' . $locale . '.xlf',
            ];
            $destinationFilename = implode(\DIRECTORY_SEPARATOR, $destinationFilenameParts);
            if ($this->filesystem->exists($destinationFilename)) {
                $this->filesystem->remove($destinationFilename);
            }

            $this->filesystem->rename($file->getPathname(), $destinationFilename);
        }

        $this->filesystem->remove($archiveParentDirectory . \DIRECTORY_SEPARATOR . $locale);
    }

    protected function getTemporaryExtractionFolder(string $themeName): string
    {
        return $this->cacheDir . \DIRECTORY_SEPARATOR . $themeName . '-tmp';
    }

    protected function getFlattenizationFolder(string $themeName): string
    {
        return $this->cacheDir . \DIRECTORY_SEPARATOR . $themeName;
    }

    protected function getExportDir(string $themeName): string
    {
        return $this->exportDir . \DIRECTORY_SEPARATOR . $themeName;
    }

    protected function makeZipFilename(string $themeName, string $locale): string
    {
        if (! file_exists($this->exportDir)) {
            mkdir($this->exportDir);
        }

        $zipFilenameParts = [
            $this->exportDir,
            $themeName,
            $locale,
            $themeName . '.' . $locale . '.zip',
        ];

        return implode(\DIRECTORY_SEPARATOR, $zipFilenameParts);
    }

    /**
     * @throws Exception
     */
    protected function makeArchiveParentDirectory(string $themeName, string $locale): string
    {
        $zipFilename = $this->makeZipFilename($themeName, $locale);

        return \dirname($zipFilename);
    }

    protected function updateCatalogueMetadata(MessageCatalogue $catalogue)
    {
        foreach ($catalogue->all() as $domain => $messages) {
            $this->ensureCatalogueHasRequiredMetadata($catalogue, $messages, $domain);
        }
    }

    protected function ensureCatalogueHasRequiredMetadata(
        MessageCatalogue $catalogue,
        array $messages,
        string $domain,
    ) {
        foreach (array_keys($messages) as $id) {
            $metadata = $catalogue->getMetadata($id, $domain);
            if ($this->shouldAddFileMetadata($metadata)) {
                $catalogue->setMetadata($id, $this->parseMetadataNotes($metadata), $domain);
            }
        }
    }

    protected function metadataContainNotes(?array $metadata = null): bool
    {
        return $metadata !== null && \array_key_exists('notes', $metadata) && \is_array($metadata['notes'])
            && \array_key_exists(0, $metadata['notes']) && \is_array($metadata['notes'][0])
            && \array_key_exists('content', $metadata['notes'][0]);
    }

    protected function shouldAddFileMetadata(?array $metadata = null): bool
    {
        return $metadata === null || ! \array_key_exists('file', $metadata);
    }

    protected function addLocaleToDomain(string $locale, MessageCatalogue $sourceCatalogue): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale, []);
        foreach ($sourceCatalogue->all() as $domain => $messages) {
            $catalogue->add($messages, $domain . '.' . $locale);
        }

        return $catalogue;
    }

    protected function parseMetadataNotes(?array $metadata = null): array
    {
        $defaultMetadata = ['file' => '', 'line' => ''];

        if (! $this->metadataContainNotes($metadata)) {
            return $defaultMetadata;
        }

        $notes = $metadata['notes'][0]['content'];
        if (preg_match('/(?<file>\S+):(?<line>\S+)/m', (string) $notes, $matches) !== 1) {
            return $defaultMetadata;
        }

        return [
            'file' => $matches['file'],
            'line' => $matches['line'],
        ];
    }
}
