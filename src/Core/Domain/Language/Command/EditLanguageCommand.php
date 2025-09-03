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
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

/**
 * Edits given language with provided data
 */
class EditLanguageCommand
{
    private LanguageId $languageId;

    /**
     * @var string|null
     */
    private $name;

    private ?IsoCode $isoCode = null;

    private ?TagIETF $tagIETF = null;

    /**
     * @var string|null
     */
    private $shortDateFormat;

    /**
     * @var string|null
     */
    private $fullDateFormat;

    /**
     * @var string|null
     */
    private $flagImagePath;

    /**
     * @var string|null
     */
    private $noPictureImagePath;

    /**
     * @var bool|null
     */
    private $isRtl;

    /**
     * @var bool|null
     */
    private $isActive;

    /**
     * @var int[]|null
     */
    private ?array $shopAssociation = null;

    /**
     * @param int $languageId
     */
    public function __construct($languageId)
    {
        $this->languageId = new LanguageId($languageId);
    }

    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    public function setLanguageId(LanguageId $languageId): static
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIsoCode(): ?IsoCode
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     */
    public function setIsoCode($isoCode): static
    {
        $this->isoCode = new IsoCode($isoCode);

        return $this;
    }

    public function getTagIETF(): ?TagIETF
    {
        return $this->tagIETF;
    }

    /**
     * @param string $tagIETF
     */
    public function setTagIETF($tagIETF): static
    {
        $this->tagIETF = new TagIETF($tagIETF);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDateFormat()
    {
        return $this->shortDateFormat;
    }

    /**
     * @param string $shortDateFormat
     */
    public function setShortDateFormat($shortDateFormat): static
    {
        $this->shortDateFormat = $shortDateFormat;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullDateFormat()
    {
        return $this->fullDateFormat;
    }

    /**
     * @param string $fullDateFormat
     */
    public function setFullDateFormat($fullDateFormat): static
    {
        $this->fullDateFormat = $fullDateFormat;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFlagImagePath()
    {
        return $this->flagImagePath;
    }

    /**
     * @param string $flagImagePath
     */
    public function setFlagImagePath($flagImagePath): static
    {
        $this->flagImagePath = $flagImagePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNoPictureImagePath()
    {
        return $this->noPictureImagePath;
    }

    /**
     * @param string $noPictureImagePath
     */
    public function setNoPictureImagePath($noPictureImagePath): static
    {
        $this->noPictureImagePath = $noPictureImagePath;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isRtl()
    {
        return $this->isRtl;
    }

    /**
     * @param bool $isRtl
     */
    public function setIsRtl($isRtl): static
    {
        $this->isRtl = $isRtl;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     */
    public function setShopAssociation(array $shopAssociation): static
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
