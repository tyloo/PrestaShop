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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SEO;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryPreviewRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductPreviewRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectType as CategoryRedirectType;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType as ProductRedirectType;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

/**
 * Build details on the product target based on the configuration (redirection type and entity id)
 */
class RedirectTargetProvider
{
    /**
     * @param ProductPreviewRepository $productPreviewRepository
     * @param CategoryPreviewRepository $categoryPreviewRepository
     * @param LegacyContext $legacyContext
     */
    public function __construct(private readonly ProductPreviewRepository $productPreviewRepository, private readonly CategoryPreviewRepository $categoryPreviewRepository, private readonly LegacyContext $legacyContext)
    {
    }

    /**
     * @param string $redirectType
     * @param int $redirectTargetId
     *
     * @return RedirectTargetInformation|null
     *
     * @throws CategoryNotFoundException
     * @throws ProductNotFoundException
     */
    public function getRedirectTarget(
        string $redirectType,
        int $redirectTargetId
    ): ?RedirectTargetInformation {
        if (empty($redirectTargetId)) {
            return null;
        }

        return match ($redirectType) {
            ProductRedirectType::TYPE_PRODUCT_TEMPORARY, ProductRedirectType::TYPE_PRODUCT_PERMANENT => $this->getProductTarget($redirectTargetId),
            ProductRedirectType::TYPE_CATEGORY_TEMPORARY, ProductRedirectType::TYPE_CATEGORY_PERMANENT, CategoryRedirectType::TYPE_TEMPORARY, CategoryRedirectType::TYPE_PERMANENT => $this->getCategoryTarget($redirectTargetId),
            default => null,
        };
    }

    /**
     * @param int $redirectTargetId
     *
     * @return RedirectTargetInformation
     *
     * @throws ProductNotFoundException
     */
    private function getProductTarget(int $redirectTargetId): RedirectTargetInformation
    {
        $languageId = $this->legacyContext->getLanguage()->id;
        $productPreview = $this->productPreviewRepository->getPreview(
            new ProductId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new RedirectTargetInformation(
            $redirectTargetId,
            RedirectTargetInformation::PRODUCT_TYPE,
            $productPreview->getName(),
            $productPreview->getImage()
        );
    }

    /**
     * @param int $redirectTargetId
     *
     * @return RedirectTargetInformation
     *
     * @throws CategoryNotFoundException
     */
    private function getCategoryTarget(int $redirectTargetId): RedirectTargetInformation
    {
        $languageId = (int) $this->legacyContext->getLanguage()->id;
        $category = $this->categoryPreviewRepository->getPreview(
            new CategoryId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new RedirectTargetInformation(
            $redirectTargetId,
            RedirectTargetInformation::CATEGORY_TYPE,
            $category->getBreadcrumb(),
            $category->getImage()
        );
    }
}
