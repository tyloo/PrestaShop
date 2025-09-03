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

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

/**
 * Class ImportConfig defines import configuration.
 */
final class ImportConfig implements ImportConfigInterface
{
    /**
     * @param string $fileName
     * @param int    $entityType
     * @param string $languageIso
     * @param string $separator
     * @param string $multipleValueSeparator
     * @param bool   $truncate
     * @param bool   $skipThumbnailRegeneration
     * @param bool   $matchReferences
     * @param bool   $forceIds
     * @param bool   $sendEmail
     * @param int    $skipRows
     */
    public function __construct(
        private $fileName,
        private $entityType,
        private $languageIso,
        private $separator,
        private $multipleValueSeparator,
        private $truncate,
        private $skipThumbnailRegeneration,
        private $matchReferences,
        private $forceIds,
        private $sendEmail,
        private $skipRows = 0,
    ) {
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function getLanguageIso()
    {
        return $this->languageIso;
    }

    public function getSeparator()
    {
        return $this->separator;
    }

    public function getMultipleValueSeparator()
    {
        return $this->multipleValueSeparator;
    }

    public function truncate()
    {
        return $this->truncate;
    }

    public function skipThumbnailRegeneration()
    {
        return $this->skipThumbnailRegeneration;
    }

    public function matchReferences()
    {
        return $this->matchReferences;
    }

    public function forceIds()
    {
        return $this->forceIds;
    }

    public function sendEmail()
    {
        return $this->sendEmail;
    }

    public function getNumberOfRowsToSkip()
    {
        return $this->skipRows;
    }
}
