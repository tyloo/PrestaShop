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

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

/**
 * Adds new language with given data
 */
class AddLanguageCommand
{
    /**
     * @var IsoCode Two-letter (639-1) language ISO code, e.g. FR, EN
     */
    private readonly IsoCode $isoCode;

    /**
     * @var TagIETF IETF language tag, e.g. en-US
     */
    private readonly TagIETF $tagIETF;

    /**
     * @param string $name
     * @param string $isoCode
     * @param string $tagIETF
     * @param string $shortDateFormat
     * @param string $fullDateFormat
     * @param string $flagImagePath
     * @param string $noPictureImagePath
     * @param bool   $isRtl
     * @param bool   $isActive
     * @param int[]  $shopAssociation
     */
    public function __construct(
        private $name,
        $isoCode,
        $tagIETF,
        private $shortDateFormat,
        private $fullDateFormat,
        private $flagImagePath,
        private $noPictureImagePath,
        private $isRtl,
        private $isActive,
        private readonly array $shopAssociation,
    ) {
        $this->isoCode = new IsoCode($isoCode);
        $this->tagIETF = new TagIETF($tagIETF);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getIsoCode(): IsoCode
    {
        return $this->isoCode;
    }

    public function getTagIETF(): TagIETF
    {
        return $this->tagIETF;
    }

    /**
     * @return string
     */
    public function getShortDateFormat()
    {
        return $this->shortDateFormat;
    }

    /**
     * @return string
     */
    public function getFullDateFormat()
    {
        return $this->fullDateFormat;
    }

    /**
     * @return string
     */
    public function getFlagImagePath()
    {
        return $this->flagImagePath;
    }

    /**
     * @return string
     */
    public function getNoPictureImagePath()
    {
        return $this->noPictureImagePath;
    }

    /**
     * @return bool
     */
    public function isRtl()
    {
        return $this->isRtl;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
