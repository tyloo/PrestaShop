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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Edits cms page category.
 */
class EditCmsPageCategoryCommand extends AbstractCmsPageCategoryCommand
{
    private readonly CmsPageCategoryId $cmsPageCategoryId;

    private ?array $localisedName = null;

    private ?array $localisedFriendlyUrl = null;

    private ?CmsPageCategoryId $parentId = null;

    /**
     * @var bool
     */
    private $isDisplayed;

    /**
     * @var string[]
     */
    private ?array $localisedDescription = null;

    /**
     * @var string[]
     */
    private ?array $localisedMetaTitle = null;

    /**
     * @var string[]
     */
    private ?array $localisedMetaDescription = null;

    /**
     * @var int[]
     */
    private ?array $shopAssociation = null;

    /**
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($cmsPageCategoryId)
    {
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
    }

    public function getCmsPageCategoryId(): CmsPageCategoryId
    {
        return $this->cmsPageCategoryId;
    }

    public function getLocalisedName(): ?array
    {
        return $this->localisedName;
    }

    /**
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedName(array $localisedName): static
    {
        $this->assertCategoryName($localisedName);
        $this->localisedName = $localisedName;

        return $this;
    }

    public function getLocalisedFriendlyUrl(): ?array
    {
        return $this->localisedFriendlyUrl;
    }

    public function setLocalisedFriendlyUrl(array $localisedFriendlyUrl): static
    {
        $this->localisedFriendlyUrl = $localisedFriendlyUrl;

        return $this;
    }

    public function getParentId(): ?CmsPageCategoryId
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     *
     * @throws CmsPageCategoryException
     */
    public function setParentId($parentId): static
    {
        $this->parentId = new CmsPageCategoryId($parentId);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * @param bool $isDisplayed
     */
    public function setIsDisplayed($isDisplayed): static
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription(): ?array
    {
        return $this->localisedDescription;
    }

    /**
     * @param string[] $localisedDescription
     */
    public function setLocalisedDescription(array $localisedDescription): static
    {
        $this->localisedDescription = $localisedDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaTitle(): ?array
    {
        return $this->localisedMetaTitle;
    }

    /**
     * @param string[] $localisedMetaTitle
     *
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedMetaTitle(array $localisedMetaTitle): static
    {
        $this->assertIsGenericNameForMetaTitle($localisedMetaTitle);
        $this->localisedMetaTitle = $localisedMetaTitle;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaDescription(): ?array
    {
        return $this->localisedMetaDescription;
    }

    /**
     * @param string[] $localisedMetaDescription
     *
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedMetaDescription(array $localisedMetaDescription): static
    {
        $this->assertIsGenericNameForMetaDescription($localisedMetaDescription);
        $this->localisedMetaDescription = $localisedMetaDescription;

        return $this;
    }

    /**
     * @return int[]
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
