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
 * Class AddCmsPageCategoryCommand is responsible for adding cms page category.
 */
class AddCmsPageCategoryCommand extends AbstractCmsPageCategoryCommand
{
    private readonly array $localisedName;

    private readonly CmsPageCategoryId $parentId;

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
     * @param int  $parentId
     * @param bool $isDisplayed
     *
     * @throws CmsPageCategoryException
     */
    public function __construct(
        array $localisedName,
        private readonly array $localisedFriendlyUrl,
        $parentId,
        private $isDisplayed,
    ) {
        $this->assertCategoryName($localisedName);

        $this->localisedName = $localisedName;
        $this->parentId = new CmsPageCategoryId($parentId);
    }

    public function getLocalisedName(): array
    {
        return $this->localisedName;
    }

    public function getLocalisedFriendlyUrl(): array
    {
        return $this->localisedFriendlyUrl;
    }

    public function getParentId(): CmsPageCategoryId
    {
        return $this->parentId;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
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
