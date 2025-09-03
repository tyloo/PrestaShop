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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

class EditableCmsPageCategory
{
    /**
     * @var CmsPageCategoryId
     */
    private $parentId;

    /**
     * @param bool $isDisplayed
     * @param int  $parentId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct(
        private readonly array $localisedName,
        private $isDisplayed,
        $parentId,
        private readonly array $localisedDescription,
        private readonly array $localisedMetaDescription,
        private readonly array $metaTitle,
        private readonly array $localisedFriendlyUrl,
        private readonly array $shopIds,
    ) {
        $this->parentId = new CmsPageCategoryId($parentId);
    }

    public function getLocalisedName(): array
    {
        return $this->localisedName;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    public function getLocalisedDescription(): array
    {
        return $this->localisedDescription;
    }

    public function getLocalisedMetaDescription(): array
    {
        return $this->localisedMetaDescription;
    }

    public function getMetaTitle(): array
    {
        return $this->metaTitle;
    }

    public function getLocalisedFriendlyUrl(): array
    {
        return $this->localisedFriendlyUrl;
    }

    public function getShopIds(): array
    {
        return $this->shopIds;
    }
}
