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

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Symfony\Component\VarExporter\VarExporter;

/**
 * Class able to manage configuration stored in Php files.
 */
class PhpParameters
{
    /**
     * @var ArrayFinder the current configuration
     */
    private $configuration;

    /**
     * @var string the PHP filename
     */
    private $filename;

    public function __construct($filename)
    {
        if (! is_readable($filename)) {
            throw new InvalidArgumentException(\sprintf('File %s is not readable for configuration', $filename));
        }

        $this->filename = $filename;
        $phpArray = require $this->filename;
        $this->configuration = new ArrayFinder($phpArray);
    }

    /**
     * @return array return the complete configuration
     */
    public function getConfiguration()
    {
        return $this->configuration->get();
    }

    /**
     * Insert a value into configuration at the specified path.
     *
     * @param string $propertyPath
     */
    public function setProperty($propertyPath, $value): void
    {
        $this->configuration->set($propertyPath, $value);
    }

    /**
     * Persist the modifications done on the original configuration file.
     */
    public function saveConfiguration(): bool
    {
        try {
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->filename, '<?php return ' . VarExporter::export($this->configuration->get()) . ';' . "\n");

            if (\function_exists('opcache_invalidate')) {
                @opcache_invalidate($this->filename);
            }
        } catch (IOException|ExceptionInterface) {
            return false;
        }

        return true;
    }
}
