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

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AddRootCategoryCommand adds new root category.
 */
class AddRootCategoryCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedLinkRewrites;

    /**
     * @var string[]
     */
    private ?array $localizedDescriptions = null;

    /**
     * @var string[]|null
     */
    private ?array $localizedAdditionalDescriptions = null;

    private bool $isActive;

    /**
     * @var string[]
     */
    private ?array $localizedMetaTitles = null;

    /**
     * @var string[]
     */
    private ?array $localizedMetaDescriptions = null;

    /**
     * @var int[]
     */
    private ?array $associatedGroupIds = null;

    /**
     * @var int[]
     */
    private ?array $associatedShopIds = null;

    private ?UploadedFile $coverImage = null;

    private ?UploadedFile $thumbnailImage = null;

    private ?RedirectOption $redirectOption = null;

    /**
     * @param string[] $name
     * @param string[] $linkRewrite
     * @param bool     $isActive
     *
     * @throws CategoryConstraintException
     */
    public function __construct(array $name, array $linkRewrite, $isActive)
    {
        $this
            ->setLocalizedNames($name)
            ->setLocalizedLinkRewrites($linkRewrite)
            ->setIsActive($isActive);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLocalizedNames(array $localizedNames): static
    {
        if ($localizedNames === []) {
            throw new CategoryConstraintException('Category name cannot be empty', CategoryConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLinkRewrites()
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites): static
    {
        if ($localizedLinkRewrites === []) {
            throw new CategoryConstraintException('Category link rewrite cannot be empty', CategoryConstraintException::EMPTY_LINK_REWRITE);
        }

        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return $this
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): static
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAdditionalDescriptions(): ?array
    {
        return $this->localizedAdditionalDescriptions;
    }

    /**
     * @param string[] $localizedAdditionalDescriptions
     *
     * @return $this
     */
    public function setLocalizedAdditionalDescriptions(array $localizedAdditionalDescriptions): self
    {
        $this->localizedAdditionalDescriptions = $localizedAdditionalDescriptions;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setIsActive($isActive): static
    {
        if (! \is_bool($isActive)) {
            throw new CategoryConstraintException('Invalid Category status supplied', CategoryConstraintException::INVALID_STATUS);
        }

        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles
     *
     * @return $this
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): static
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions
     *
     * @return $this
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): static
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAssociatedGroupIds(): ?array
    {
        return $this->associatedGroupIds;
    }

    /**
     * @param int[] $associatedGroupIds
     *
     * @return $this
     */
    public function setAssociatedGroupIds(array $associatedGroupIds): static
    {
        $this->associatedGroupIds = $associatedGroupIds;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): ?array
    {
        return $this->associatedShopIds;
    }

    /**
     * @param int[] $associatedShopIds
     *
     * @return $this
     */
    public function setAssociatedShopIds(array $associatedShopIds): static
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }

    public function getCoverImage(): ?UploadedFile
    {
        return $this->coverImage;
    }

    public function setCoverImage(?UploadedFile $coverImage): void
    {
        $this->coverImage = $coverImage;
    }

    public function getThumbnailImage(): ?UploadedFile
    {
        return $this->thumbnailImage;
    }

    public function setThumbnailImage(?UploadedFile $thumbnailImage): void
    {
        $this->thumbnailImage = $thumbnailImage;
    }

    public function getRedirectOption(): ?RedirectOption
    {
        return $this->redirectOption;
    }

    public function setRedirectOption(?RedirectOption $redirectOption): void
    {
        $this->redirectOption = $redirectOption;
    }
}
