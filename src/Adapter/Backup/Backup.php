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

namespace PrestaShop\PrestaShop\Adapter\Backup;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;
use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Class Backup represents single database backup.
 *
 * @internal
 */
final class Backup implements BackupInterface
{
    /**
     * @var PrestaShopBackup
     */
    private $legacyBackup;

    /**
     * @param string $fileName Backup file name
     */
    public function __construct(
        private $fileName,
    ) {
        $this->legacyBackup = new PrestaShopBackup($this->fileName);
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFilePath(): string
    {
        return $this->legacyBackup->getBackupPath() . $this->getFileName();
    }

    public function getUrl()
    {
        return $this->legacyBackup->getBackupURL();
    }

    public function getSize(): int|false
    {
        return filesize($this->legacyBackup->id);
    }

    public function getAge(): float|int
    {
        return time() - $this->getDate()->getTimestamp();
    }

    public function getDate(): DateTimeImmutable
    {
        [$timestamp] = explode('-', $this->fileName);

        return new DateTimeImmutable('@' . $timestamp);
    }
}
