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

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Version;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Appends sql upgrade file with the sql which can be used to create new hooks.
 *
 * The command compares the current hook.xml fixture file with the previous one (you need to specify
 * the previous version to define the base to compare to).
 *
 * Thanks to the comparison we get new and obsolete hooks, then two SQL queries are generated and
 * appended in the autoupgrade file (you must specify its local path), the upgrade file matching the
 * current version will be appended with these two SQL queries.
 *
 * No check of previous request in the file is done you must check manually that there are no duplicates.
 */
class AppendHooksListForSqlUpgradeFileCommand extends Command
{
    public function __construct(
        private readonly string $env,
        private readonly LegacyContext $legacyContext,
        private readonly HttpClientInterface $httpClient,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:update:sql-upgrade-file-hooks-listing')
            ->setDescription(
                'Adds sql to sql upgrade file which contains hook insert operation'
            )
            ->addArgument(
                'previous-ps-version',
                InputArgument::REQUIRED,
                'The previous prestashop version based on which we know the previous existing hooks'
            )
            ->addArgument(
                'autoupgrade-path',
                InputArgument::REQUIRED,
                'The path to the autoupgrade module path which contains the upgrade scripts'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initContext();

        $io = new SymfonyStyle($input, $output);

        if (! \in_array($this->env, ['dev', 'test'], true)) {
            $io->warning('Dev or test environment is required to fully list all the hooks');

            return 1;
        }

        $currentHooks = $this->getCurrentHooks();
        $previousHooks = $this->getPreviousHooks($input->getArgument('previous-ps-version'));

        $newHooks = array_diff($currentHooks, $previousHooks);
        $removedHooks = array_diff($previousHooks, $currentHooks);

        if ($newHooks === [] && $removedHooks === []) {
            $io->note('No hooks modification found.');

            return 0;
        }

        // Get SQL upgrade file from module for the current version
        try {
            $sqlUpgradeFile = $this->getSqlUpgradeFileByPrestaShopVersion(
                Version::VERSION,
                $input->getArgument('autoupgrade-path')
            );
        } catch (FileNotFoundException $fileNotFoundException) {
            $io->error($fileNotFoundException->getMessage());

            return 1;
        }

        if ($sqlUpgradeFile === '' || $sqlUpgradeFile === '0') {
            return 1;
        }

        // First add new hooks to SQL file
        if ($newHooks !== []) {
            $hookDescriptions = $this->extractHookDescriptions($newHooks);

            $sqlInsertStatement = $this->getSqlInsertStatement($hookDescriptions, Version::VERSION);
            $this->appendSqlToFile($sqlUpgradeFile, $sqlInsertStatement);
            $io->success(
                \sprintf(
                    'All new %s hooks have been listed to file %s',
                    \count($newHooks),
                    $sqlUpgradeFile
                )
            );
        }

        // Now delete removed hooks
        if ($removedHooks !== []) {
            $sqlDeleteStatement = $this->getSqlDeleteStatement($removedHooks, Version::VERSION);
            $this->appendSqlToFile($sqlUpgradeFile, $sqlDeleteStatement);
            $io->success(
                \sprintf(
                    'All obsolete %s hooks have been removed in file %s',
                    \count($removedHooks),
                    $sqlUpgradeFile
                )
            );
        }

        return 0;
    }

    /**
     * Initialize PrestaShop Context
     */
    private function initContext(): void
    {
        // We need to have an employee or the listing hooks don't work
        // see LegacyHookSubscriber
        if (! $this->legacyContext->getContext()->employee) {
            // Even a non existing employee is fine
            $this->legacyContext->getContext()->employee = new Employee();
        }
    }

    /**
     * Gets sql upgrade file by PrestaShop version.
     */
    private function getSqlUpgradeFileByPrestaShopVersion(string $version, $autoUpgradeModulePath): string
    {
        $sqlUpgradeFile = \sprintf('%s/upgrade/sql/%s.sql', $autoUpgradeModulePath, $version);

        if (! file_exists($sqlUpgradeFile)) {
            throw new FileNotFoundException(\sprintf('File %s has not been found', $sqlUpgradeFile));
        }

        return $sqlUpgradeFile;
    }

    /**
     * Gets sql insert statement.
     *
     * @param HookDescription[] $hookDescriptions
     */
    private function getSqlInsertStatement(array $hookDescriptions, string $prestashopVersion): string
    {
        $valuesToInsert = [];
        foreach ($hookDescriptions as $hookDescription) {
            $valuesToInsert[] = \sprintf(
                "  (NULL, '%s', '%s', '%s', '1')",
                pSQL($hookDescription->getName()),
                pSQL($hookDescription->getTitle()),
                pSQL($hookDescription->getDescription())
            );
        }

        if ($valuesToInsert === []) {
            return '';
        }

        $insertSQL = \PHP_EOL . \sprintf('/* Auto generated hooks added for version %s */', $prestashopVersion) . \PHP_EOL;
        $insertSQL .= 'INSERT INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES' . \PHP_EOL;
        $insertSQL .= implode(',' . \PHP_EOL, $valuesToInsert);

        return $insertSQL . (\PHP_EOL . 'ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `description` = VALUES(`description`);' . \PHP_EOL);
    }

    private function getSqlDeleteStatement(array $removedHooks, string $prestashopVersion): string
    {
        if ($removedHooks === []) {
            return '';
        }

        $deleteSQL = \PHP_EOL . \sprintf('/* Auto generated hooks removed for version %s */', $prestashopVersion) . \PHP_EOL;
        $deleteSQL .= 'DELETE FROM `PREFIX_hook` WHERE `name` IN (' . \PHP_EOL;
        $deleteSQL .= implode(',' . \PHP_EOL, array_map(fn (string $hookName): string => \sprintf("  '%s'", $hookName), $removedHooks));
        $deleteSQL .= \PHP_EOL . ');' . \PHP_EOL;
        $deleteSQL .= '/* Clean hook registrations related to removed hooks */' . \PHP_EOL;
        $deleteSQL .= 'DELETE FROM `PREFIX_hook_module` WHERE `id_hook` NOT IN (SELECT id_hook FROM `PREFIX_hook`);' . \PHP_EOL;

        return $deleteSQL . ('DELETE FROM `PREFIX_hook_module_exceptions` WHERE `id_hook` NOT IN (SELECT id_hook FROM `PREFIX_hook`);' . \PHP_EOL);
    }

    /**
     * Appends new content to the given file.
     */
    private function appendSqlToFile(string $pathToFile, string $content): void
    {
        $fileSystem = new Filesystem();

        $fileSystem->appendToFile($pathToFile, $content);
    }

    private function getCurrentHooks(): array
    {
        $currentHookXml = file_get_contents($this->projectDir . '/install-dev/data/xml/hook.xml');

        return $this->extractHookNamesFromXML($currentHookXml);
    }

    private function getPreviousHooks(string $previousVersion): array
    {
        $previousHookFile = \sprintf('https://raw.githubusercontent.com/PrestaShop/PrestaShop/refs/tags/%s/install-dev/data/xml/hook.xml', $previousVersion);
        $response = $this->httpClient->request('GET', $previousHookFile);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException('Could not get previous hook information ' . $previousHookFile);
        }

        return $this->extractHookNamesFromXML($response->getContent());
    }

    private function extractHookNamesFromXML(string $xmlContent): array
    {
        $xmlFileContent = new SimpleXMLElement($xmlContent);

        if ((! property_exists($xmlFileContent, 'entities') || $xmlFileContent->entities === null) && (! property_exists($xmlFileContent->entities, 'hook') || $xmlFileContent->entities->hook === null)) {
            throw new RuntimeException('Invalid hook fixtures files could not find hooks node');
        }

        $hookNames = [];
        foreach ($xmlFileContent->entities->hook as $hook) {
            if (! property_exists($hook, 'name') || $hook->name === null) {
                continue;
            }

            $hookNames[] = $hook->name->__toString();
        }

        return $hookNames;
    }

    private function extractHookDescriptions(array $extractedHooks): array
    {
        $currentHookXml = file_get_contents($this->projectDir . '/install-dev/data/xml/hook.xml');
        $xmlFileContent = new SimpleXMLElement($currentHookXml);

        if ((! property_exists($xmlFileContent, 'entities') || $xmlFileContent->entities === null) && (! property_exists($xmlFileContent->entities, 'hook') || $xmlFileContent->entities->hook === null)) {
            throw new RuntimeException('Invalid hook fixtures files could not find hooks node');
        }

        $hookNames = [];
        foreach ($xmlFileContent->entities->hook as $hook) {
            if (! property_exists($hook, 'name') || $hook->name === null) {
                continue;
            }

            $hookName = $hook->name->__toString();
            if (! \in_array($hookName, $extractedHooks, true)) {
                continue;
            }

            $hookNames[] = new HookDescription(
                $hook->name->__toString(),
                $hook->title->__toString(),
                $hook->description->__toString()
            );
        }

        return $hookNames;
    }
}
