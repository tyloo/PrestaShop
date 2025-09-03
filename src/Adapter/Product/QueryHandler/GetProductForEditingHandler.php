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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Customization;
use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Adapter\SEO\RedirectTargetProvider;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Category\NameBuilder\CategoryDisplayNameBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\CategoriesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\CategoryInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductBasicInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSeoOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductStockInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractorException;
use Product;
use Tag;

/**
 * Handles the query @see GetProductForEditing using legacy ObjectModel
 */
#[AsQueryHandler]
class GetProductForEditingHandler implements GetProductForEditingHandlerInterface
{
    public function __construct(
        private readonly NumberExtractor $numberExtractor,
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly StockAvailableRepository $stockAvailableRepository,
        private readonly VirtualProductFileRepository $virtualProductFileRepository,
        private readonly ProductImageRepository $productImageRepository,
        private readonly AttachmentRepository $attachmentRepository,
        private readonly TaxComputer $taxComputer,
        private readonly int $countryId,
        private readonly RedirectTargetProvider $targetProvider,
        private readonly ProductImagePathFactory $productImageUrlFactory,
        private readonly SpecificPriceRepository $specificPriceRepository,
        private readonly Configuration $configuration,
        private readonly CategoryDisplayNameBuilder $categoryDisplayNameBuilder,
    ) {
    }

    public function handle(GetProductForEditing $query): ProductForEditing
    {
        $product = $this->productRepository->getByShopConstraint(
            $query->getProductId(),
            $query->getShopConstraint()
        );

        return new ProductForEditing(
            (int) $product->id,
            $product->getProductType(),
            (bool) $product->active,
            $this->getCustomizationOptions($product),
            $this->getBasicInformation($product),
            $this->getCategoriesInformation($product, $query->getDisplayLanguageId()),
            $this->getPricesInformation($product, $query->getShopConstraint()),
            $this->getOptions($product),
            $this->getDetails($product),
            $this->getShippingInformation($product),
            $this->getSeoOptions($product),
            $this->getAttachments($query->getProductId()),
            $this->getProductStockInformation($product),
            $this->getVirtualProductFile($product),
            $this->getCover($query->getProductId(), $product->getShopId()),
            array_map(fn (ShopId $shopId): int => $shopId->getValue(), $this->productRepository->getAssociatedShopIds($query->getProductId()))
        );
    }

    /**
     * @return AttachmentInformation[]
     */
    private function getAttachments(ProductId $productId): array
    {
        $attachments = $this->attachmentRepository->getProductAttachments($productId);

        $attachmentsInfo = [];
        foreach ($attachments as $attachment) {
            $attachmentsInfo[] = new AttachmentInformation(
                (int) $attachment['id_attachment'],
                $attachment['name'],
                $attachment['description'],
                $attachment['file_name'],
                $attachment['mime'],
                (int) $attachment['file_size']
            );
        }

        return $attachmentsInfo;
    }

    private function getBasicInformation(Product $product): ProductBasicInformation
    {
        return new ProductBasicInformation(
            $product->name,
            $product->description,
            $product->description_short,
            $this->getLocalizedTagsList((int) $product->id)
        );
    }

    private function getCategoriesInformation(Product $product, LanguageId $languageId): CategoriesInformation
    {
        $shopId = new ShopId($product->getShopId());
        $productId = new ProductId((int) $product->id);

        $categoryIds = $this->categoryRepository->getProductCategoryIds($productId, ShopConstraint::shop($shopId->getValue()));
        $defaultCategoryId = (int) $product->id_category_default;

        $categoryNames = $this->categoryRepository->getLocalizedNames($categoryIds);

        $categoriesInformation = [];
        foreach (array_keys($categoryNames) as $categoryId) {
            $categoryName = $categoryNames[$categoryId][$languageId->getValue()];
            $displayName = $this->categoryDisplayNameBuilder->build(
                $categoryName,
                $shopId,
                $languageId,
                new CategoryId($categoryId)
            );
            $categoriesInformation[] = new CategoryInformation(
                $categoryId,
                $categoryName,
                $displayName
            );
        }

        return new CategoriesInformation($categoriesInformation, $defaultCategoryId);
    }

    /**
     * @throws NumberExtractorException
     */
    private function getPricesInformation(Product $product, ShopConstraint $shopConstraint): ProductPricesInformation
    {
        $productId = new ProductId((int) $product->id);

        $taxEnabled = (bool) $this->configuration->get('PS_TAX', null, $shopConstraint);
        $ecotaxEnabled = (bool) $this->configuration->get('PS_USE_ECOTAX', null, $shopConstraint);
        $ecoTaxGroupId = (int) $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID', null, $shopConstraint);
        $priceTaxExcluded = $this->numberExtractor->extract($product, 'price');
        $unitPriceTaxExcluded = $this->numberExtractor->extract($product, 'unit_price');
        $ecotaxTaxExcluded = $this->numberExtractor->extract($product, 'ecotax');

        if ($taxEnabled) {
            $priceTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                $priceTaxExcluded,
                new TaxRulesGroupId((int) $product->id_tax_rules_group),
                new CountryId($this->countryId)
            );
            $unitPriceTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                $unitPriceTaxExcluded,
                new TaxRulesGroupId((int) $product->id_tax_rules_group),
                new CountryId($this->countryId)
            );
            $ecotaxTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                $ecotaxTaxExcluded,
                new TaxRulesGroupId($ecoTaxGroupId),
                new CountryId($this->countryId)
            );
        } else {
            $priceTaxIncluded = $priceTaxExcluded;
            $unitPriceTaxIncluded = $unitPriceTaxExcluded;
            $ecotaxTaxIncluded = $ecotaxTaxExcluded;
        }

        // Ecotax is applied independently of tax enabled
        if ($ecotaxEnabled) {
            $priceTaxIncluded = $priceTaxIncluded->plus($ecotaxTaxIncluded);
        }

        return new ProductPricesInformation(
            $priceTaxExcluded,
            $priceTaxIncluded,
            $ecotaxTaxExcluded,
            $ecotaxTaxIncluded,
            (int) $product->id_tax_rules_group,
            (bool) $product->on_sale,
            $this->numberExtractor->extract($product, 'wholesale_price'),
            $unitPriceTaxExcluded,
            $unitPriceTaxIncluded,
            (string) $product->unity,
            $this->numberExtractor->extract($product, 'unit_price_ratio'),
            $this->specificPriceRepository->findPrioritiesForProduct($productId)
        );
    }

    private function getOptions(Product $product): ProductOptions
    {
        return new ProductOptions(
            $product->visibility,
            (bool) $product->available_for_order,
            (bool) $product->online_only,
            (bool) $product->show_price,
            $product->condition,
            (bool) $product->show_condition,
            (int) $product->id_manufacturer
        );
    }

    private function getDetails(Product $product): ProductDetails
    {
        return new ProductDetails(
            $product->isbn,
            $product->upc,
            $product->ean13,
            $product->mpn,
            $product->reference
        );
    }

    /**
     * @throws NumberExtractorException
     */
    private function getShippingInformation(Product $product): ProductShippingInformation
    {
        $carrierReferences = array_map(fn (array $carrier): int => (int) $carrier['id_reference'], $product->getCarriers());

        return new ProductShippingInformation(
            $this->numberExtractor->extract($product, 'width'),
            $this->numberExtractor->extract($product, 'height'),
            $this->numberExtractor->extract($product, 'depth'),
            $this->numberExtractor->extract($product, 'weight'),
            $this->numberExtractor->extract($product, 'additional_shipping_cost'),
            $carrierReferences,
            (int) $product->additional_delivery_times,
            $product->delivery_in_stock,
            $product->delivery_out_stock
        );
    }

    /**
     * @return LocalizedTags[]
     */
    private function getLocalizedTagsList(int $productId): array
    {
        $tags = Tag::getProductTags($productId);

        if (! $tags) {
            return [];
        }

        $localizedTagsList = [];

        foreach ($tags as $langId => $localizedTags) {
            $localizedTagsList[] = new LocalizedTags((int) $langId, $localizedTags);
        }

        return $localizedTagsList;
    }

    private function getCustomizationOptions(Product $product): ProductCustomizationOptions
    {
        if (! Customization::isFeatureActive()) {
            return ProductCustomizationOptions::createNotCustomizable();
        }

        $textFieldsCount = (int) $product->text_fields;
        $fileFieldsCount = (int) $product->uploadable_files;

        return match ((int) $product->customizable) {
            ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION => ProductCustomizationOptions::createAllowsCustomization($textFieldsCount, $fileFieldsCount),
            ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION => ProductCustomizationOptions::createRequiresCustomization($textFieldsCount, $fileFieldsCount),
            default => ProductCustomizationOptions::createNotCustomizable(),
        };
    }

    private function getSeoOptions(Product $product): ProductSeoOptions
    {
        $redirectTarget = $this->targetProvider->getRedirectTarget(
            $product->redirect_type,
            (int) $product->id_type_redirected
        );

        return new ProductSeoOptions(
            $product->meta_title,
            $product->meta_description,
            $product->link_rewrite,
            $product->redirect_type,
            $redirectTarget
        );
    }

    /**
     * Returns the product stock infos, it's important that the Product is fetched with stock data
     */
    private function getProductStockInformation(Product $product): ProductStockInformation
    {
        try {
            $stockAvailable = $this->stockAvailableRepository->getForProduct(new ProductId($product->id), new ShopId($product->getShopId()));
        } catch (StockAvailableNotFoundException) {
            $stockAvailable = $this->stockAvailableRepository->createStockAvailable(new ProductId($product->id), new ShopId($product->getShopId()));
        }

        return new ProductStockInformation(
            (int) $product->pack_stock_type,
            (int) $stockAvailable->out_of_stock,
            (int) $stockAvailable->quantity,
            (int) $product->minimal_quantity,
            (int) $product->low_stock_threshold,
            (bool) $product->low_stock_alert,
            $product->available_now,
            $product->available_later,
            $stockAvailable->location,
            DateTimeUtil::buildDateTimeOrNull($product->available_date)
        );
    }

    /**
     * Legacy object ProductDownload is referred as VirtualProductFile in Core
     */
    private function getVirtualProductFile(Product $product): ?VirtualProductFileForEditing
    {
        try {
            $virtualProductFile = $this->virtualProductFileRepository->findByProductId(new ProductId($product->id));
        } catch (VirtualProductFileNotFoundException) {
            return null;
        }

        return new VirtualProductFileForEditing(
            (int) $virtualProductFile->id,
            $virtualProductFile->filename,
            $virtualProductFile->display_filename,
            (int) $virtualProductFile->nb_days_accessible,
            (int) $virtualProductFile->nb_downloadable,
            DateTimeUtil::buildDateTimeOrNull($virtualProductFile->date_expiration)
        );
    }

    private function getCover(ProductId $productId, int $shopId): string
    {
        $idOfCoverImage = $this->productImageRepository->findCoverImageId($productId, new ShopId($shopId));

        if ($idOfCoverImage instanceof \PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId) {
            return $this->productImageUrlFactory->getPathByType($idOfCoverImage, ProductImagePathFactory::IMAGE_TYPE_CART_DEFAULT);
        }

        return $this->productImageUrlFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_CART_DEFAULT);
    }
}
