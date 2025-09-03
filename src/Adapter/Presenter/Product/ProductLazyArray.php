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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use Category;
use Combination;
use Context;
use Customization;
use DateTime;
use Db;
use Language;
use Link;
use Manufacturer;
use Pack;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Product;
use ReflectionException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * @property string $availability_message
 */
class ProductLazyArray extends AbstractLazyArray
{
    private readonly HookManager $hookManager;

    private readonly Configuration $configuration;

    public function __construct(
        protected ProductPresentationSettings $settings,
        protected array $product,
        private readonly Language $language,
        private readonly ImageRetriever $imageRetriever,
        private readonly Link $link,
        private readonly PriceFormatter $priceFormatter,
        private readonly ProductColorsRetriever $productColorsRetriever,
        private readonly TranslatorInterface $translator,
        ?HookManager $hookManager = null,
        ?Configuration $configuration = null,
    ) {
        $this->hookManager = $hookManager ?? new HookManager();
        $this->configuration = $configuration ?? new Configuration();

        // Load image information right away
        $this->fillImages($this->product, $this->language);

        // Load pricing information right away
        $this->addPriceInformation($this->settings, $this->product);

        // Load quantity information right away
        $this->addQuantityInformation($this->settings, $this->product, $this->language);

        parent::__construct();

        // Make all properties from the provided array available,
        // even if they are not implemented via a specific method.
        $this->appendArray($this->product);
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getId()
    {
        return $this->product['id_product'];
    }

    /**
     * @return array|mixed
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getAttributes()
    {
        return $this->product['attributes'] ?? [];
    }

    /**
     * Returns information, if a customization is required to purchase this product.
     *
     * @return bool
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCustomizationRequired()
    {
        if (! isset($this->product['customization_required'])) {
            $this->product['customization_required'] = false;
            // If customizable property passed here was true and customization feature is enabled,
            // we can further check the fields.
            //  Now, we fetch the required customization fields and if we find some, the product requires customization.
            if (
                ! empty($this->product['customizable']) && Customization::isFeatureActive() && \count(
                    Product::getRequiredCustomizableFieldsStatic(
                        (int) $this->product['id_product']
                    )
                )
            ) {
                // And we cache it
                $this->product['customization_required'] = true;
            }
        }

        return $this->product['customization_required'];
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getShowPrice(): bool
    {
        return $this->shouldShowPrice($this->settings, $this->product);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getWeightUnit(): mixed
    {
        return $this->configuration->get('PS_WEIGHT_UNIT');
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getUrl()
    {
        return $this->getProductURL($this->product, $this->language);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getLink()
    {
        return $this->getProductURL($this->product, $this->language);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCanonicalUrl()
    {
        return $this->getProductURL($this->product, $this->language, true);
    }

    /**
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getAddToCartUrl()
    {
        if (
            $this->shouldEnableAddToCartButton($this->product, $this->settings)
        ) {
            return $this->link->getAddToCartURL(
                $this->product['id_product'],
                $this->product['id_product_attribute']
            );
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCondition(): false|array
    {
        if (empty($this->product['show_condition'])) {
            return false;
        }

        return match ($this->product['condition']) {
            'new' => [
                'type' => 'new',
                'label' => $this->translator->trans(
                    'New',
                    [],
                    'Shop.Theme.Catalog'
                ),
                'schema_url' => 'https://schema.org/NewCondition',
            ],
            'used' => [
                'type' => 'used',
                'label' => $this->translator->trans(
                    'Used',
                    [],
                    'Shop.Theme.Catalog'
                ),
                'schema_url' => 'https://schema.org/UsedCondition',
            ],
            'refurbished' => [
                'type' => 'refurbished',
                'label' => $this->translator->trans(
                    'Refurbished',
                    [],
                    'Shop.Theme.Catalog'
                ),
                'schema_url' => 'https://schema.org/RefurbishedCondition',
            ],
            default => false,
        };
    }

    /**
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getDeliveryInformation()
    {
        $productQuantity =
            $this->product['stock_quantity'] ?? $this->product['quantity'];

        if ($productQuantity >= $this->getQuantityWanted()) {
            $config = $this->configuration->get(
                'PS_LABEL_DELIVERY_TIME_AVAILABLE'
            );

            return $config[$this->language->id] ?? null;
        }

        if (
            $this->shouldEnableAddToCartButton($this->product, $this->settings)
        ) {
            $config = $this->configuration->get(
                'PS_LABEL_DELIVERY_TIME_OOSBOA',
                []
            );

            return $config[$this->language->id] ?? null;
        }

        return null;
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getEmbeddedAttributes(): array
    {
        $whitelist = $this->getProductAttributeWhitelist();
        $embeddedProductAttributes = [];
        foreach ($this->product as $attribute => $value) {
            if (\in_array($attribute, $whitelist, true)) {
                $embeddedProductAttributes[$attribute] = $value;
            }
        }

        return $embeddedProductAttributes;
    }

    /**
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getFileSizeFormatted()
    {
        if (! isset($this->product['attachments'])) {
            return null;
        }

        foreach ($this->product['attachments'] as $attachment) {
            return Tools::formatBytes($attachment['file_size'], 2);
        }

        return null;
    }

    /**
     * @return array
     *
     * @throws ReflectionException
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getAttachments()
    {
        // If this is a first call to this property
        if (! isset($this->product['attachments'])) {
            $this->product['attachments'] = [];

            /*
             * There is an optional cache_has_attachments property, which if passed, informs us if the product
             * has attachments or not in a fast way. We will load attachments only if this property was not passed
             * or is true.
             *
             * This property which needs to be managed every time a file is changed.
             * It can sometimes lead to database inconsistency.
             */
            if (
                ! isset($this->product['cache_has_attachments'])
                || $this->product['cache_has_attachments']
            ) {
                $this->product['attachments'] = Product::getAttachmentsStatic(
                    (int) $this->language->id,
                    $this->product['id_product']
                );

                // Add file sizes to every attachment
                foreach ($this->product['attachments'] as &$attachment) {
                    if (! isset($attachment['file_size_formatted'])) {
                        $attachment['file_size_formatted'] = Tools::formatBytes(
                            $attachment['file_size'],
                            2
                        );
                    }
                }
            }
        }

        return $this->product['attachments'];
    }

    /**
     * @return array|mixed
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getQuantityDiscounts()
    {
        return $this->product['quantity_discounts'] ?? [];
    }

    /**
     * @return mixed|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getReferenceToDisplay()
    {
        $combinationData = $this->getCombinationSpecificData();
        if (
            isset($combinationData['reference'])
            && ! empty($combinationData['reference'])
        ) {
            return $combinationData['reference'];
        }

        if ($this->product['reference'] !== '') {
            return $this->product['reference'];
        }

        return null;
    }

    /**
     * Returns all product features, not grouped yet for performance reasons.
     *
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getFeatures()
    {
        /*
         * If features were not loaded yet, we will ask for them if needed - usually on product page.
         * However, if really hunting performance and you know you will need features in listing for bunch of products,
         * fetch them with one query (in more performant way) and pass them here when constructing this object.
         */
        if (! isset($this->product['features'])) {
            $this->product['features'] = Product::getFrontFeaturesStatic(
                (int) $this->language->id,
                $this->product['id_product']
            );
        }

        return $this->product['features'];
    }

    /**
     * Returns all product feature values nicely grouped by feature name.
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getGroupedFeatures(): array
    {
        return $this->buildGroupedFeatures($this->getFeatures());
    }

    /**
     * See following resources for up-to-date information
     * https://support.google.com/merchants/answer/6324448
     * https://schema.org/ItemAvailability
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getSeoAvailability(): string
    {
        // Availability for displaying discontinued products, if enabled
        if ($this->product['active'] !== 1) {
            return 'https://schema.org/Discontinued';
            // If product is in stock or stock management is disabled (= we have everything in stock)
        }

        if (
            $this->product['quantity'] > 0
            || ! $this->configuration->get('PS_STOCK_MANAGEMENT')
        ) {
            return 'https://schema.org/InStock';
            // If it's not in stock, but available for order
        }

        if (
            $this->product['quantity'] <= 0
            && $this->product['allow_oosp']
        ) {
            return 'https://schema.org/BackOrder';
            // If it's not in stock and not available for order
        }

        return 'https://schema.org/OutOfStock';
    }

    /**
     * @throws InvalidArgumentException
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getLabels(): array
    {
        return [
            'tax_short' => $this->settings->include_taxes
                ? $this->translator->trans(
                    '(tax incl.)',
                    [],
                    'Shop.Theme.Global'
                )
                : $this->translator->trans(
                    '(tax excl.)',
                    [],
                    'Shop.Theme.Global'
                ),
            'tax_long' => $this->settings->include_taxes
                ? $this->translator->trans(
                    'Tax included',
                    [],
                    'Shop.Theme.Global'
                )
                : $this->translator->trans(
                    'Tax excluded',
                    [],
                    'Shop.Theme.Global'
                ),
        ];
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getEcotax(): ?array
    {
        if (isset($this->product['ecotax'])) {
            return [
                'value' => $this->priceFormatter->format(
                    $this->product['ecotax']
                ),
                'amount' => $this->product['ecotax'],
                'rate' => $this->product['ecotax_rate'],
            ];
        }

        return null;
    }

    /**
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getManufacturerName()
    {
        if (! isset($this->product['manufacturer_name'])) {
            // Assign empty value
            $this->product['manufacturer_name'] = null;

            // If we have manufacturer ID, we will try to load it's name and assign it
            if (! empty($this->product['id_manufacturer'])) {
                $manufacturerName = Manufacturer::getNameById(
                    (int) $this->product['id_manufacturer']
                );
                if (! empty($manufacturerName)) {
                    $this->product['manufacturer_name'] = $manufacturerName;
                }
            }
        }

        return $this->product['manufacturer_name'];
    }

    /**
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCategory()
    {
        if (! isset($this->product['category'])) {
            $categoryLinkRewrite = Category::getLinkRewrite(
                (int) $this->product['id_category_default'],
                (int) $this->language->id
            );
            $this->product['category'] = empty($categoryLinkRewrite)
                ? null
                : $categoryLinkRewrite;
        }

        return $this->product['category'];
    }

    /**
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCategoryName()
    {
        if (! isset($this->product['category_name'])) {
            $categoryName = (string) Db::getInstance()->getValue(
                'SELECT name FROM ' .
                    _DB_PREFIX_ .
                    'category_lang
                WHERE id_shop = ' .
                    (int) Context::getContext()->shop->id .
                    ' AND id_lang = ' .
                    (int) $this->language->id .
                    ' AND id_category = ' .
                    (int) $this->product['id_category_default']
            );
            $this->product['category_name'] = $categoryName === '' || $categoryName === '0'
                ? null
                : $categoryName;
        }

        return $this->product['category_name'];
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getVirtual(): bool
    {
        return $this->product['is_virtual'] || ! empty($this->product['virtual']);
    }

    /**
     * @return int
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getNew()
    {
        if (! isset($this->product['new'])) {
            $this->product['new'] = (int) Product::isNewStatic(
                $this->product['id_product']
            );
        }

        return $this->product['new'];
    }

    /**
     * @throws InvalidArgumentException
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getFlags(): array
    {
        $flags = [];

        $show_price = $this->shouldShowPrice($this->settings, $this->product);

        if ($show_price && $this->product['online_only']) {
            $flags['online-only'] = [
                'type' => 'online-only',
                'label' => $this->translator->trans(
                    'Online only',
                    [],
                    'Shop.Theme.Catalog'
                ),
            ];
        }

        if (
            $show_price
            && $this->product['on_sale']
            && ! $this->settings->catalog_mode
        ) {
            $flags['on-sale'] = [
                'type' => 'on-sale',
                'label' => $this->translator->trans(
                    'On sale!',
                    [],
                    'Shop.Theme.Catalog'
                ),
            ];
        }

        if ($show_price && $this->product['reduction']) {
            if ($this->product['discount_type'] === 'percentage') {
                $flags['discount'] = [
                    'type' => 'discount',
                    'label' => $this->product['discount_percentage'],
                ];
            } elseif ($this->product['discount_type'] === 'amount') {
                $flags['discount'] = [
                    'type' => 'discount',
                    'label' => $this->product['discount_amount_to_display'],
                ];
            } else {
                $flags['discount'] = [
                    'type' => 'discount',
                    'label' => $this->translator->trans(
                        'Reduced price',
                        [],
                        'Shop.Theme.Catalog'
                    ),
                ];
            }
        }

        if ($this->getNew()) {
            $flags['new'] = [
                'type' => 'new',
                'label' => $this->translator->trans(
                    'New',
                    [],
                    'Shop.Theme.Global'
                ),
            ];
        }

        if ($this->product['pack']) {
            $flags['pack'] = [
                'type' => 'pack',
                'label' => $this->translator->trans(
                    'Pack',
                    [],
                    'Shop.Theme.Catalog'
                ),
            ];
        }

        if ($this->shouldShowOutOfStockLabel($this->settings, $this->product)) {
            // For the label, we will follow the same logic as for normal stock label,
            // we will try combination label, then product label, then the general label.
            $combinationData = $this->getCombinationSpecificData();
            if (! empty($combinationData['available_later'])) {
                $message = $combinationData['available_later'];
            } elseif (! empty($this->product['available_later'])) {
                $message = $this->product['available_later'];
            } else {
                $config = $this->configuration->get(
                    'PS_LABEL_OOS_PRODUCTS_BOD'
                );
                $message = $config[$this->language->getId()] ?? null;
            }

            $flags['out_of_stock'] = [
                'type' => 'out_of_stock',
                'label' => $message,
            ];
        }

        $this->hookManager->exec('actionProductFlagsModifier', [
            'flags' => &$flags,
            'product' => $this->product,
        ]);

        return $flags;
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getMainVariants(): array
    {
        $colors = $this->productColorsRetriever->getColoredVariants(
            $this->product['id_product']
        );

        if (! \is_array($colors)) {
            return [];
        }

        return array_map(function (array $color) {
            $color['add_to_cart_url'] = $this->link->getAddToCartURL(
                $color['id_product'],
                $color['id_product_attribute']
            );
            $color['url'] = $this->getProductURL($color, $this->language);
            $color['type'] = 'color';
            $color['html_color_code'] = $color['color'];
            unset($color['color']);

            return $color;
        }, $colors);
    }

    /**
     * Returns combination specific data, if assigned. This function should be rewritten because it
     * loads the data from the first attribute found. See ProductController for more info.
     *
     * Also, on product page, $this->product['attributes'] contains a list of combinations, while in cart
     * it contains only attribute pairs like Color-Black etc.
     *
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCombinationSpecificData()
    {
        if (
            ! isset($this->product['attributes'])
            || ! \is_array($this->product['attributes'])
            || empty($this->product['attributes'])
        ) {
            return null;
        }

        return reset($this->product['attributes']);
    }

    /**
     * This function returns current combination references, if set.
     * Otherwise, it returns the base product references.
     *
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getSpecificReferences()
    {
        $specificReferences = null;

        // Get data of this combination, it contains other stuff, we will extract only what we need
        $combinationData = $this->getCombinationSpecificData();

        // Keys we want to extract from the combination data
        $referenceTypes = ['isbn', 'upc', 'ean13', 'mpn'];

        foreach ($referenceTypes as $type) {
            // First, we try to get the references of combination.
            if (! empty($combinationData[$type])) {
                $specificReference = $combinationData[$type];
            // Otherwise, we check if something is set on the product itself
            } elseif (! empty($this->product[$type])) {
                $specificReference = $this->product[$type];
            } else {
                continue;
            }

            // Get a nice readable label for this reference and save it
            $specificReferences[
                $this->getTranslatedKey($type)
            ] = $specificReference;
        }

        return $specificReferences;
    }

    /**
     * Prices should be shown for products with active "Show price" option
     * and customer groups with active "Show price" option.
     */
    private function shouldShowPrice(
        ProductPresentationSettings $settings,
        array $product,
    ): bool {
        return $settings->shouldShowPrice() && (bool) $product['show_price'];
    }

    private function shouldShowOutOfStockLabel(
        ProductPresentationSettings $settings,
        array $product,
    ): bool {
        if (! $settings->showLabelOOSListingPages) {
            return false;
        }

        if (! $this->configuration->getBoolean('PS_STOCK_MANAGEMENT')) {
            return false;
        }

        // Displayed only if the order of out of stock product is denied.
        if (
            $product['out_of_stock'] ===
                OutOfStockType::OUT_OF_STOCK_AVAILABLE
            || ($product['out_of_stock'] === OutOfStockType::OUT_OF_STOCK_DEFAULT
                && $this->configuration->getBoolean('PS_ORDER_OUT_OF_STOCK'))
        ) {
            return false;
        }

        if ($product['id_product_attribute']) {
            // Displayed only if all combinations are out of stock (stock is <= 0)
            $product = new Product((int) $product['id_product']);
            if (empty($product->id)) {
                return false;
            }

            foreach (
                $product->getAttributesResume($this->language->getId()) as $combination
            ) {
                if ($combination['quantity'] > 0) {
                    return false;
                }
            }
        } elseif ($product['quantity'] > 0) {
            // Displayed only if the product stock is <= 0
            return false;
        }

        return true;
    }

    private function fillImages(array $product, Language $language): void
    {
        // Get all product images assigned to this product.
        $productImages = $this->imageRetriever->getAllProductImages(
            $product,
            $language
        );

        // Get filtered product images matching the specified id_product_attribute
        $this->product['images'] = $this->filterImagesForCombination(
            $productImages,
            $product['id_product_attribute']
        );

        /*
         * Get default image for the current product/combination.
         * This image is usually used on places where we 100% need to show the image of the combination (cart, order confirmation).
         * It's always the first image associated to that product/combination.
         */
        $this->product['default_image'] = reset($this->product['images']);

        /*
         * Now let's define product's cover - the image used in listings.
         *
         * For products without combinations, it's simple. It's always the cover.
         *
         * For products with combinations, we can configure it. Two options:
         * 1) Always use the cover, even if it's not assigned to the combination (for example some general image with color palette).
         * 2) Use first image assigned to the combination passed to the presenter.
         * This setting is controlled by PS_USE_COMBINATION_IMAGE_IN_LISTING property.
         */
        if (
            empty($product['id_product_attribute'])
            || ! $this->configuration->get('PS_USE_COMBINATION_IMAGE_IN_LISTING')
        ) {
            foreach ($productImages as $image) {
                if (isset($image['cover']) && $image['cover'] !== null) {
                    $this->product['cover'] = $image;

                    break;
                }
            }
        }

        // In other cases or if cover was not found, we use the first image
        if (! isset($this->product['cover'])) {
            $this->product['cover'] = $this->product['default_image'];
        }
    }

    private function filterImagesForCombination(
        array $images,
        int $productAttributeId,
    ): array {
        $filteredImages = [];

        foreach ($images as $image) {
            if (\in_array($productAttributeId, $image['associatedVariants'], true)) {
                $filteredImages[] = $image;
            }
        }

        return $filteredImages === [] ? $images : $filteredImages;
    }

    private function addPriceInformation(
        ProductPresentationSettings $settings,
        array $product,
    ): void {
        $this->product['has_discount'] = false;
        $this->product['discount_type'] = null;
        $this->product['discount_percentage'] = null;
        $this->product['discount_percentage_absolute'] = null;
        $this->product['discount_amount'] = null;
        $this->product['discount_amount_to_display'] = null;

        if ($settings->include_taxes) {
            $price = $product['price'];
            $regular_price = $product['price'];
        } else {
            $price = $product['price_tax_exc'];
            $regular_price = $product['price_tax_exc'];
        }

        if ($product['specific_prices']) {
            $this->product['has_discount'] = $product['reduction'] !== 0;
            $this->product['discount_type'] =
                $product['specific_prices']['reduction_type'];

            $absoluteReduction = new DecimalNumber(
                $product['specific_prices']['reduction']
            );
            $absoluteReduction = $absoluteReduction->times(
                new DecimalNumber('100')
            );
            $negativeReduction = $absoluteReduction->toNegative();
            $presAbsoluteReduction = $absoluteReduction->round(
                2,
                Rounding::ROUND_HALF_UP
            );
            $presNegativeReduction = $negativeReduction->round(
                2,
                Rounding::ROUND_HALF_UP
            );

            // TODO: add percent sign according to locale preferences
            $this->product['discount_percentage'] =
                Context::getContext()
                    ->getCurrentLocale()
                    ->formatNumber($presNegativeReduction) . '%';
            $this->product['discount_percentage_absolute'] =
                Context::getContext()
                    ->getCurrentLocale()
                    ->formatNumber($presAbsoluteReduction) . '%';
            if ($settings->include_taxes) {
                $regular_price = $product['price_without_reduction'];
            } else {
                $regular_price =
                    $product['price_without_reduction_without_tax'];
            }

            // We must calculate the real amount of discount.
            // see @https://github.com/PrestaShop/PrestaShop/issues/32924
            $product['reduction'] = $regular_price - $price;
            $this->product['discount_amount'] = $this->priceFormatter->format(
                $product['reduction']
            );
            $this->product['discount_amount_to_display'] =
                '-' . $this->priceFormatter->format($product['reduction']);
        }

        $this->product['price_amount'] = $price;
        $this->product['price'] = $this->priceFormatter->format($price);
        $this->product['regular_price_amount'] = $regular_price;
        $this->product['regular_price'] = $this->priceFormatter->format(
            $regular_price
        );

        if ($product['reduction'] < $product['price_without_reduction']) {
            $this->product['discount_to_display'] =
                $this->product['discount_amount'];
        } else {
            $this->product['discount_to_display'] =
                $this->product['regular_price'];
        }

        /*
         * Now, let's format unit price display.
         *
         * If we have a unit ("per 100 g") to display after the unit price AND we have the value, we can proceed with formatting.
         * We are intentionally not using empty here, because unit price can be also zero.
         *
         * If not, we will pass empty strings.
         */
        if (
            ! empty($this->product['unity'])
            && isset(
                $this->product['unit_price_tax_excluded'],
                $this->product['unit_price_tax_included']
            )
        ) {
            /*
             * We use the tax included or tax excluded price, depending on presentation settings.
             * We have the prices calculated from the Product::computeUnitPriceRatio, that is called before it gets passed here.
             *
             * The prices are already adapted to account for specific prices and combinations.
             */
            $this->product['unit_price'] = $this->priceFormatter->format(
                $settings->include_taxes
                    ? $this->product['unit_price_tax_included']
                    : $this->product['unit_price_tax_excluded']
            );

            // And add the full version with the unit after the price
            $this->product['unit_price_full'] =
                $this->product['unit_price'] . ' ' . $product['unity'];
        } else {
            $this->product['unit_price'] = '';
            $this->product['unit_price_full'] = '';
        }

        // Assign no-pack prices in case of products that are packs
        if ($this->product['pack']) {
            $rawNoPackPrice = Pack::noPackPrice(
                (int) $this->product['id_product']
            );
            $this->product['nopackprice'] = $rawNoPackPrice;
            $this->product[
                'nopackprice_to_display'
            ] = $this->priceFormatter->format($rawNoPackPrice);
        } else {
            $this->product['nopackprice'] = null;
            $this->product['nopackprice_to_display'] = null;
        }
    }

    /**
     * @return float
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getRoundedDisplayPrice()
    {
        return Tools::ps_round(
            $this->product['price_amount'],
            Context::getContext()->currency->precision
        );
    }

    /**
     * @return bool
     */
    protected function shouldEnableAddToCartButton(
        array $product,
        ProductPresentationSettings $settings,
    ) {
        // If the product is disabled, we disable add to cart button
        if ($product['active'] !== 1) {
            return false;
        }

        // Disable because of catalog mode enabled in Prestashop settings
        if ($this->settings->catalog_mode) {
            return false;
        }

        // Disable because of "Available for order" checkbox unchecked in product settings
        if ((bool) $product['available_for_order'] === false) {
            return false;
        }

        if (! $this->shouldShowPrice($settings, $product)) {
            return false;
        }

        if (
            $product['customizable'] ===
                ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION
            || $this->getCustomizationRequired()
        ) {
            $shouldEnable = false;

            if (isset($product['customizations'])) {
                $shouldEnable = true;
                foreach ($product['customizations']['fields'] as $field) {
                    if ($field['required'] && ! $field['is_customized']) {
                        $shouldEnable = false;
                    }
                }
            }
        } else {
            $shouldEnable = true;
        }

        // Disable because of stock management
        if (
            $settings->stock_management_enabled
            && ! $product['allow_oosp']
            && ($product['quantity'] <= 0
                || $product['quantity'] - $this->getQuantityWanted() < 0
                || $product['quantity'] - $this->getMinimalQuantity() < 0)
        ) {
            $shouldEnable = false;
        }

        return $shouldEnable;
    }

    /**
     * @return int Quantity of product requested by the customer
     */
    private function getQuantityWanted(): int
    {
        return (int) Tools::getValue(
            'quantity_wanted',
            $this->product['quantity_wanted'] ?? 1
        );
    }

    /**
     * @return int Minimal quantity of product requested by the customer
     */
    private function getMinimalQuantity(): int
    {
        return (int) $this->product['minimal_quantity'];
    }

    /**
     * @return string
     */
    private function getProductURL(
        array $product,
        Language $language,
        bool $canonical = false,
    ) {
        $linkRewrite = $product['link_rewrite'] ?? null;
        $category = $this->getCategory();
        $ean13 = $product['ean13'] ?? null;

        return $this->link->getProductLink(
            $product['id_product'],
            $linkRewrite,
            $category,
            $ean13,
            $language->id,
            null,
            ! $canonical && $product['id_product_attribute'] > 0
                ? $product['id_product_attribute']
                : null,
            false,
            false,
            true
        );
    }

    public function addQuantityInformation(
        ProductPresentationSettings $settings,
        array $product,
        Language $language,
    ): void {
        $show_price = $this->shouldShowPrice($settings, $product);
        $show_availability = $show_price && $settings->stock_management_enabled;
        $this->product['show_availability'] = $show_availability;

        if (! isset($product['quantity_wanted'])) {
            $product['quantity_wanted'] = $this->getQuantityWanted();
        }

        // Validate and format availability date
        $product['available_date'] = $this->prepareAvailabilityDate($product);

        // Default data
        $this->product['availability_message'] = null;
        $this->product['availability_submessage'] = null;
        $this->product['availability_date'] = null;
        $this->product['availability'] = null;

        // If we don't want to show availability, we return immediately
        if (! $show_availability) {
            return;
        }

        // If the product is disabled, but still displayed, we display a proper message
        if ($this->product['active'] !== 1) {
            $this->product['availability_message'] = $this->translator->trans(
                'This product is no longer available for sale.',
                [],
                'Shop.Notifications.Error'
            );
            $this->product['availability'] = 'discontinued';

            return;
        }

        // Quantity available we will display is reduced by amount we want to add to cart
        $availableQuantity = $product['quantity'] - $product['quantity_wanted'];
        if (isset($product['stock_quantity'])) {
            $availableQuantity =
                $product['stock_quantity'] - $product['quantity_wanted'];
        }

        // Combination labels
        $combinationData = $this->getCombinationSpecificData();

        // Now, let's generate a nice availability information. We will have 4 cases to go through.
        // Case 1 - Product in stock
        if ($availableQuantity >= 0) {
            // If the products are the last items remaining, we show different message and exclamation mark
            if ($availableQuantity < $settings->lastRemainingItems) {
                $this->product['availability'] = 'last_remaining_items';
                $this->product[
                    'availability_message'
                ] = $this->translator->trans(
                    'Last items in stock',
                    [],
                    'Shop.Theme.Catalog'
                );
            } else {
                $this->product['availability'] = 'in_stock';

                // We will primarily use label from combination if set, then label on product, then the default label from PS settings
                if (! empty($combinationData['available_now'])) {
                    $this->product['availability_message'] =
                        $combinationData['available_now'];
                } elseif (! empty($product['available_now'])) {
                    $this->product['availability_message'] =
                        $product['available_now'];
                } else {
                    $config = $this->configuration->get(
                        'PS_LABEL_IN_STOCK_PRODUCTS'
                    );
                    $this->product['availability_message'] =
                        $config[$language->id] ?? null;
                }
            }

        // Case 2 - Product not in stock, available for order
        } elseif ($product['allow_oosp']) {
            $this->product['availability_date'] = $product['available_date'];
            $this->product['availability'] = 'available';

            // We will primarily use label from combination if set, then label on product, then the default label from PS settings
            if (! empty($combinationData['available_later'])) {
                $this->product['availability_message'] =
                    $combinationData['available_later'];
            } elseif (! empty($product['available_later'])) {
                $this->product['availability_message'] =
                    $product['available_later'];
            } else {
                $config = $this->configuration->get(
                    'PS_LABEL_OOS_PRODUCTS_BOA'
                );
                $this->product['availability_message'] =
                    $config[$language->id] ?? null;
            }

        // Case 3 - OOSP disabled and customer wants to add more items to cart than are in stock
        } elseif ($product['quantity'] > 0) {
            $this->product['availability_date'] = $product['available_date'];
            $this->product['availability'] = 'unavailable';

            $this->product['availability_message'] = $this->translator->trans(
                'There are not enough products in stock',
                [],
                'Shop.Notifications.Error'
            );

        // Case 4 - Product not in stock, not available for order
        } else {
            $this->product['availability_date'] = $product['available_date'];
            $this->product['availability'] = 'unavailable';

            // We will primarily use label from combination if set, then label on product, then the default label from PS settings
            if (! empty($combinationData['available_later'])) {
                $this->product['availability_message'] =
                    $combinationData['available_later'];
            } elseif (! empty($product['available_later'])) {
                $this->product['availability_message'] =
                    $product['available_later'];
            } else {
                $config = $this->configuration->get(
                    'PS_LABEL_OOS_PRODUCTS_BOD'
                );
                $this->product['availability_message'] =
                    $config[$language->id] ?? null;
            }

            // If the product has combinations and other combination is in stock, we show a small hint about it
            if (
                $product['cache_default_attribute']
                && $product['quantity_all_versions'] > 0
            ) {
                $this->product[
                    'availability_submessage'
                ] = $this->translator->trans(
                    'Product available with different options',
                    [],
                    'Shop.Theme.Catalog'
                );
            }
        }
    }

    /**
     * Returns extra price associated with current combination, if provided
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getAttributePrice(): float
    {
        if (! isset($this->product['attribute_price'])) {
            if (! empty($this->product['id_product_attribute'])) {
                $this->product[
                    'attribute_price'
                ] = (float) Combination::getPrice(
                    $this->product['id_product_attribute']
                );
            } else {
                $this->product['attribute_price'] = 0;
            }
        }

        return (float) $this->product['attribute_price'];
    }

    /**
     * Validates and formats available_date property passed into the lazy array.
     * It will return the date back only if it's a valid date in the future.
     * Also handles the case when the date was not passed at all.
     *
     * @param array $product
     *
     * @return string|null
     */
    private function prepareAvailabilityDate($product)
    {
        // Check if the date is valid
        if (
            empty($product['available_date'])
            || $product['available_date'] === '0000-00-00'
            || ! Validate::isDate($product['available_date'])
        ) {
            return null;
        }

        // Check if it didn't already pass
        $date = new DateTime($product['available_date']);
        if ($date < new DateTime()) {
            return null;
        }

        return $product['available_date'];
    }

    private function getTranslatedKey(string $key): string
    {
        return match ($key) {
            'ean13' => $this->translator->trans(
                'ean13',
                [],
                'Shop.Theme.Catalog'
            ),
            'isbn' => $this->translator->trans(
                'isbn',
                [],
                'Shop.Theme.Catalog'
            ),
            'upc' => $this->translator->trans(
                'upc',
                [],
                'Shop.Theme.Catalog'
            ),
            'mpn' => $this->translator->trans(
                'MPN',
                [],
                'Shop.Theme.Catalog'
            ),
            default => $key,
        };
    }

    protected function getProductAttributeWhitelist(): array
    {
        return [
            'active',
            'add_to_cart_url',
            'additional_shipping_cost',
            'allow_oosp',
            'attachments',
            'attribute_price',
            'attributes',
            'availability',
            'availability_date',
            'availability_message',
            'available_date',
            'available_for_order',
            'available_later',
            'available_now',
            'cache_default_attribute',
            'canonical_url',
            'category',
            'category_name',
            'condition',
            'cover',
            'customer_group_discount',
            'customizable',
            'customization_required',
            'customizations',
            'date_add',
            'date_upd',
            'delivery_in_stock',
            'delivery_out_stock',
            'description',
            'description_short',
            'discount_amount',
            'discount_amount_to_display',
            'discount_percentage',
            'discount_percentage_absolute',
            'discount_type',
            'ecotax',
            'ecotax_rate',
            'extraContent',
            'features',
            'flags',
            'has_discount',
            'id',
            'id_category_default',
            'id_customization',
            'id_image',
            'id_manufacturer',
            'id_product',
            'id_product_attribute',
            'id_shop_default',
            'id_supplier',
            'id_type_redirected',
            'images',
            'indexed',
            'is_customizable',
            'is_virtual',
            'labels',
            'link',
            'link_rewrite',
            'low_stock_alert',
            'low_stock_threshold',
            'main_variants',
            'manufacturer_name',
            'meta_description',
            'meta_title',
            'minimal_quantity',
            'name',
            'new',
            'nopackprice',
            'on_sale',
            'online_only',
            'out_of_stock',
            'pack',
            'pack_stock_type',
            'packItems',
            'price',
            'price_amount',
            'price_tax_exc',
            'price_without_reduction',
            'quantity',
            'quantity_all_versions',
            'quantity_discounts',
            'quantity_label',
            'quantity_wanted',
            'rate',
            'redirect_type',
            'reduction',
            'reference',
            'reference_to_display',
            'show_availability',
            'show_condition',
            'show_price',
            'show_quantities',
            'specific_prices',
            'tax_name',
            'text_fields',
            'unit_price',
            'unit_price_full',
            'unit_price_ratio',
            'unity',
            'uploadable_files',
            'url',
            'virtual',
            'visibility',
            'weight_unit',
        ];
    }

    /**
     * Assemble the same features in one array.
     */
    protected function buildGroupedFeatures(array $productFeatures): array
    {
        $valuesByFeatureName = [];
        $groupedFeatures = [];

        // features can either be "raw" (id_feature, id_product_id_feature_value)
        // or "full" (id_feature, name, value)
        // grouping can only be performed if they are "full"
        if (
            $productFeatures === []
            || ! \array_key_exists('name', reset($productFeatures))
        ) {
            return [];
        }

        foreach ($productFeatures as $feature) {
            $featureName = $feature['name'];
            // build an array of unique features
            $groupedFeatures[$featureName] = $feature;
            // aggregate feature values separately
            $valuesByFeatureName[$featureName][] = $feature['value'];
        }

        // replace value from features that have multiple values with the ones we aggregated earlier
        foreach ($valuesByFeatureName as $featureName => $values) {
            if (\count($values) > 1) {
                $groupedFeatures[$featureName]['value'] = implode(
                    "\n",
                    $values
                );
            }
        }

        return $groupedFeatures;
    }
}
