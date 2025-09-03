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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Transfers cms page data for editing
 */
class EditableCmsPage
{
    /**
     * @var CmsPageId
     */
    private $cmsPageId;

    /**
     * @var CmsPageCategoryId
     */
    private $cmsPageCategoryId;

    /**
     * @param int      $cmsPageId
     * @param int      $cmsPageCategoryId
     * @param string[] $localizedTitle
     * @param string[] $localizedMetaTitle
     * @param string[] $localizedMetaDescription
     * @param string[] $localizedFriendlyUrl
     * @param string[] $localizedContent
     * @param bool     $indexedForSearch
     * @param bool     $displayed
     * @param string   $previewUrl
     *
     * @throws CmsPageCategoryException
     * @throws CmsPageException
     */
    public function __construct(
        $cmsPageId,
        $cmsPageCategoryId,
        private readonly array $localizedTitle,
        private readonly array $localizedMetaTitle,
        private readonly array $localizedMetaDescription,
        private readonly array $localizedFriendlyUrl,
        private readonly array $localizedContent,
        private $indexedForSearch,
        private $displayed,
        private readonly array $shopAssociation,
        /**
         * Url for opening FO page on save and preview action
         */
        private $previewUrl,
    ) {
        $this->cmsPageId = new CmsPageId($cmsPageId);
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
    }

    /**
     * @return CmsPageId
     */
    public function getCmsPageId()
    {
        return $this->cmsPageId;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getCmsPageCategoryId()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedTitle(): array
    {
        return $this->localizedTitle;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitle(): array
    {
        return $this->localizedMetaTitle;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescription(): array
    {
        return $this->localizedMetaDescription;
    }

    /**
     * @return string[]
     */
    public function getLocalizedFriendlyUrl(): array
    {
        return $this->localizedFriendlyUrl;
    }

    /**
     * @return string[]
     */
    public function getLocalizedContent(): array
    {
        return $this->localizedContent;
    }

    /**
     * @return bool
     */
    public function isIndexedForSearch()
    {
        return $this->indexedForSearch;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->displayed;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->previewUrl;
    }
}
