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

namespace PrestaShop\PrestaShop\Adapter;

use Image;
use ImageManager as LegacyImageManager;

/**
 * Class responsible of finding images and creating thumbnails.
 */
class ImageManager
{
    public function __construct(
        private readonly LegacyContext $legacyContext,
    ) {
    }

    /**
     * Old legacy way to generate a thumbnail.
     *
     * Use it upon a new Image management system is available.
     *
     * @param int    $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string The HTML < img > tag
     */
    public function getThumbnailForListing($imageId, $imageType = 'jpg', $tableName = 'product', $imageDir = _PS_PRODUCT_IMG_DIR_): ?string
    {
        $thumbPath = $this->getThumbnailTag($imageId, $imageType, $tableName, $imageDir);

        // because legacy uses relative path to reach a directory under root directory...
        $replacement = 'src="' . $this->legacyContext->getRootUrl();

        return preg_replace('/src="(\\.\\.\\/)+/', $replacement, $thumbPath);
    }

    /**
     * @param int $imageId
     *
     * @return string
     */
    public function getThumbnailPath($imageId)
    {
        $imageType = 'jpg';
        $tableName = 'product';
        $imageDir = _PS_PRODUCT_IMG_DIR_;

        $imagePath = $this->getImagePath($imageId, $imageType, $tableName, $imageDir);
        $thumbnailCachedImageName = $this->makeCachedImageName($imageId, $imageType, $tableName);
        LegacyImageManager::thumbnail(
            $imagePath,
            $thumbnailCachedImageName,
            45,
            $imageType
        );

        return LegacyImageManager::getThumbnailPath($thumbnailCachedImageName, false);
    }

    /**
     * @param int    $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string
     */
    private function getThumbnailTag($imageId, $imageType, $tableName, $imageDir)
    {
        $imagePath = $this->getImagePath($imageId, $imageType, $tableName, $imageDir);

        return LegacyImageManager::thumbnail(
            $imagePath,
            $this->makeCachedImageName($imageId, $imageType, $tableName),
            45,
            $imageType
        );
    }

    /**
     * @param int    $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     */
    private function getImagePath($imageId, $imageType, $tableName, $imageDir): string
    {
        if ($tableName === 'product') {
            $image = new Image($imageId);

            return $imageDir . $image->getExistingImgPath() . '.' . $imageType;
        }

        return $imageDir . $imageId . '.' . $imageType;
    }

    /**
     * @param int    $imageId
     * @param string $imageType
     * @param string $tableName
     */
    private function makeCachedImageName($imageId, $imageType, $tableName): string
    {
        return $tableName . '_mini_' . $imageId . '.' . $imageType;
    }
}
