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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use ImageType;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\DeleteCategoryCoverImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles category cover image deleting command.
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteCategoryCoverImageHandler implements DeleteCategoryCoverImageHandlerInterface
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function handle(DeleteCategoryCoverImageCommand $command): void
    {
        $categoryId = $command->getCategoryId();
        $category = new Category($categoryId->getValue());

        $this->assertCategoryExists($categoryId, $category);

        $this->deleteCoverImage($category);
        $this->deleteTemporaryThumbnailImage($category);
        $this->deleteImagesForAllTypes($category);
    }

    /**
     * @throws CategoryNotFoundException
     */
    private function assertCategoryExists(CategoryId $categoryId, Category $category): void
    {
        if ($category->id !== $categoryId->getValue()) {
            throw new CategoryNotFoundException($categoryId, \sprintf('Category with id "%s" was not found.', $categoryId->getValue()));
        }
    }

    /**
     * @throws CannotDeleteImageException
     */
    private function deleteCoverImage(Category $category): void
    {
        if ($category->deleteImage(true) === false) {
            throw new CannotDeleteImageException(\sprintf('Cannot delete cover image for category with id "%s"', $category->id), CannotDeleteImageException::COVER_IMAGE);
        }
    }

    /**
     * @throws CannotDeleteImageException
     */
    private function deleteTemporaryThumbnailImage(Category $category): void
    {
        $temporaryThumbnailPath = $this->configuration->get('_PS_TMP_IMG_DIR_') . 'category_' . $category->id . '-thumb.jpg';

        try {
            if ($this->filesystem->exists($temporaryThumbnailPath)) {
                $this->filesystem->remove($temporaryThumbnailPath);
            }
        } catch (IOException $ioException) {
            throw new CannotDeleteImageException(\sprintf('Cannot delete thumbnail image for category with id "%s"', $category->id), CannotDeleteImageException::THUMBNAIL_IMAGE, $ioException);
        }
    }

    /**
     * @throws CannotDeleteImageException
     */
    private function deleteImagesForAllTypes(Category $category): void
    {
        $imageTypes = ImageType::getImagesTypes('categories');
        $categoryImageDir = $this->configuration->get('_PS_CAT_IMG_DIR_');

        try {
            foreach ($imageTypes as $imageType) {
                foreach (ImageFormatConfiguration::SUPPORTED_FORMATS as $imageFormat) {
                    $imagePath = $categoryImageDir . $category->id . '-' . $imageType['name'] . '.' . $imageFormat;
                    if ($this->filesystem->exists($imagePath)) {
                        $this->filesystem->remove($imagePath);
                    }
                }
            }
        } catch (IOException $ioException) {
            throw new CannotDeleteImageException(\sprintf('Cannot delete image with type "%s" for category with id "%s"', isset($imageType) ? $imageType['name'] : '', $category->id), CannotDeleteImageException::COVER_IMAGE, $ioException);
        }
    }
}
