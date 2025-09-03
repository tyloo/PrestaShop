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

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

/**
 * Stores category data needed for editing.
 */
class EditableCategory
{
    /**
     * @param string[] $name
     * @param bool     $isActive
     * @param string[] $description
     * @param int      $parentId
     * @param string[] $metaTitle
     * @param string[] $metaDescription
     * @param string[] $linkRewrite
     * @param int[]    $groupAssociationIds
     * @param int[]    $shopAssociationIds
     * @param bool     $isRootCategory
     * @param string[] $additionalDescription
     */
    public function __construct(
        private readonly CategoryId $id,
        private readonly array $name,
        private $isActive,
        private readonly array $description,
        private $parentId,
        private readonly array $metaTitle,
        private readonly array $metaDescription,
        private readonly array $linkRewrite,
        private string $redirectType,
        private ?RedirectTargetInformation $categoryRedirectTarget,
        private readonly array $groupAssociationIds,
        private readonly array $shopAssociationIds,
        private $isRootCategory,
        private $coverImage = null,
        private $thumbnailImage = null,
        private readonly array $subCategories = [],
        private readonly array $additionalDescription = [],
    ) {
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return string[]
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function getAdditionalDescription(): array
    {
        return $this->additionalDescription;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return string[]
     */
    public function getMetaTitle(): array
    {
        return $this->metaTitle;
    }

    /**
     * @return string[]
     */
    public function getMetaDescription(): array
    {
        return $this->metaDescription;
    }

    /**
     * @return string[]
     */
    public function getLinkRewrite(): array
    {
        return $this->linkRewrite;
    }

    public function getRedirectType(): string
    {
        return $this->redirectType;
    }

    public function setRedirectType(string $redirectType): void
    {
        $this->redirectType = $redirectType;
    }

    public function getRedirectTarget(): ?RedirectTargetInformation
    {
        return $this->categoryRedirectTarget;
    }

    public function setRedirectTarget(?RedirectTargetInformation $categoryRedirectTarget): void
    {
        $this->categoryRedirectTarget = $categoryRedirectTarget;
    }

    /**
     * @return int[]
     */
    public function getGroupAssociationIds(): array
    {
        return $this->groupAssociationIds;
    }

    /**
     * @return int[]
     */
    public function getShopAssociationIds(): array
    {
        return $this->shopAssociationIds;
    }

    public function getCoverImage()
    {
        return $this->coverImage;
    }

    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return $this->isRootCategory;
    }

    public function getSubCategories(): array
    {
        return $this->subCategories;
    }
}
