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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\LowStockThreshold;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Dimension;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Gtin;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Contains all the data needed to handle the product update.
 *
 * @see UpdateProductHandlerInterface
 *
 * This command is only designed for the general data of product which can be persisted in one call.
 * It was not designed to handle the product relations.
 */
class UpdateProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var string[]|null
     */
    private $localizedNames;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedDescriptions;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedShortDescriptions;

    /**
     * @var ProductVisibility|null
     */
    private $visibility;

    /**
     * @var bool|null
     */
    private $availableForOrder;

    /**
     * @var bool|null
     */
    private $onlineOnly;

    /**
     * @var bool|null
     */
    private $showPrice;

    /**
     * @var ProductCondition|null
     */
    private $condition;

    /**
     * @var bool|null
     */
    private $showCondition;

    /**
     * @var ManufacturerIdInterface|null
     */
    private $manufacturerId;

    /**
     * @var DecimalNumber|null
     */
    private $price;

    /**
     * @var DecimalNumber|null
     */
    private $ecotax;

    /**
     * @var int|null
     */
    private $taxRulesGroupId;

    /**
     * @var bool|null
     */
    private $onSale;

    /**
     * @var DecimalNumber|null
     */
    private $wholesalePrice;

    /**
     * @var DecimalNumber|null
     */
    private $unitPrice;

    /**
     * @var string|null
     */
    private $unity;

    /**
     * @var string[]|null
     */
    private $localizedMetaTitles;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]|null
     */
    private $localizedLinkRewrites;

    /**
     * @var RedirectOption|null
     */
    private $redirectOption;

    /**
     * @var Isbn|null
     */
    private $isbn;

    /**
     * @var Upc|null
     */
    private $upc;

    private ?Gtin $gtin = null;

    /**
     * @var string|null
     */
    private $mpn;

    /**
     * @var Reference|null
     */
    private $reference;

    /**
     * @var Dimension|null
     */
    private $width;

    /**
     * @var Dimension|null
     */
    private $height;

    /**
     * @var Dimension|null
     */
    private $depth;

    /**
     * @var Dimension|null
     */
    private $weight;

    /**
     * @var DecimalNumber|null
     */
    private $additionalShippingCost;

    /**
     * @var DeliveryTimeNoteType
     */
    private $deliveryTimeNoteType;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeInStockNotes;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeOutOfStockNotes;

    /**
     * @var PackStockType|null
     */
    private $packStockType;

    /**
     * @var int|null
     */
    private $minimalQuantity;

    /**
     * @var LowStockThreshold|null
     */
    private $lowStockThreshold;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @var bool|null
     */
    private $active;

    public function __construct(int $productId, ShopConstraint $shopConstraint)
    {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles key => value pairs where each key represents language id
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): self
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions key => value pairs where each key represents language id
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): self
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedLinkRewrites(): ?array
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites key => value pairs where each key represents language id
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites): self
    {
        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    public function getRedirectOption(): ?RedirectOption
    {
        return $this->redirectOption;
    }

    public function setRedirectOption(string $redirectType, int $redirectTarget): self
    {
        $this->redirectOption = new RedirectOption($redirectType, $redirectTarget);

        return $this;
    }

    public function getPrice(): ?DecimalNumber
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = new DecimalNumber($price);

        return $this;
    }

    public function getEcotax(): ?DecimalNumber
    {
        return $this->ecotax;
    }

    public function setEcotax(string $ecotax): self
    {
        $this->ecotax = new DecimalNumber($ecotax);

        return $this;
    }

    public function getTaxRulesGroupId(): ?int
    {
        return $this->taxRulesGroupId;
    }

    public function setTaxRulesGroupId(int $taxRulesGroupId): self
    {
        $this->taxRulesGroupId = $taxRulesGroupId;

        return $this;
    }

    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    public function setOnSale(bool $onSale): self
    {
        $this->onSale = $onSale;

        return $this;
    }

    public function getWholesalePrice(): ?DecimalNumber
    {
        return $this->wholesalePrice;
    }

    public function setWholesalePrice(string $wholesalePrice): self
    {
        $this->wholesalePrice = new DecimalNumber($wholesalePrice);

        return $this;
    }

    public function getUnitPrice(): ?DecimalNumber
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = new DecimalNumber($unitPrice);

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): self
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedShortDescriptions(): ?array
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @param string[] $localizedShortDescriptions
     */
    public function setLocalizedShortDescriptions(array $localizedShortDescriptions): self
    {
        $this->localizedShortDescriptions = $localizedShortDescriptions;

        return $this;
    }

    public function getVisibility(): ?ProductVisibility
    {
        return $this->visibility;
    }

    public function isAvailableForOrder(): ?bool
    {
        return $this->availableForOrder;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = new ProductVisibility($visibility);

        return $this;
    }

    public function setAvailableForOrder(bool $availableForOrder): self
    {
        $this->availableForOrder = $availableForOrder;

        return $this;
    }

    public function isOnlineOnly(): ?bool
    {
        return $this->onlineOnly;
    }

    public function setOnlineOnly(bool $onlineOnly): self
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    public function showPrice(): ?bool
    {
        return $this->showPrice;
    }

    public function setShowPrice(bool $showPrice): self
    {
        $this->showPrice = $showPrice;

        return $this;
    }

    public function getCondition(): ?ProductCondition
    {
        return $this->condition;
    }

    public function setCondition(string $condition): self
    {
        $this->condition = new ProductCondition($condition);

        return $this;
    }

    public function setShowCondition(bool $showCondition): self
    {
        $this->showCondition = $showCondition;

        return $this;
    }

    public function showCondition(): ?bool
    {
        return $this->showCondition;
    }

    public function getManufacturerId(): ?ManufacturerIdInterface
    {
        return $this->manufacturerId;
    }

    /**
     * @throws ManufacturerConstraintException
     */
    public function setManufacturerId(int $manufacturerId): self
    {
        $this->manufacturerId = $manufacturerId === NoManufacturerId::NO_MANUFACTURER_ID ?
            new NoManufacturerId() :
            new ManufacturerId($manufacturerId)
        ;

        return $this;
    }

    public function getIsbn(): ?Isbn
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = new Isbn($isbn);

        return $this;
    }

    public function getUpc(): ?Upc
    {
        return $this->upc;
    }

    public function setUpc(string $upc): self
    {
        $this->upc = new Upc($upc);

        return $this;
    }

    /**
     * @deprecated since 9.0 will be removed in 10.0
     */
    public function getEan13(): ?Gtin
    {
        return $this->getGtin();
    }

    /**
     * @deprecated since 9.0 will be removed in 10.0
     */
    public function setEan13(string $gtin): self
    {
        return $this->setGtin($gtin);
    }

    public function getGtin(): ?Gtin
    {
        return $this->gtin;
    }

    public function setGtin(string $gtin): self
    {
        $this->gtin = new Gtin($gtin);

        return $this;
    }

    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    public function setMpn(string $mpn): self
    {
        $this->mpn = $mpn;

        return $this;
    }

    public function getReference(): ?Reference
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = new Reference($reference);

        return $this;
    }

    public function getWidth(): ?Dimension
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->setDimension($width, 'width');

        return $this;
    }

    public function getHeight(): ?Dimension
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->setDimension($height, 'height');

        return $this;
    }

    public function getDepth(): ?Dimension
    {
        return $this->depth;
    }

    public function setDepth(string $depth): self
    {
        $this->setDimension($depth, 'depth');

        return $this;
    }

    public function getWeight(): ?Dimension
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->setDimension($weight, 'weight');

        return $this;
    }

    public function getAdditionalShippingCost(): ?DecimalNumber
    {
        return $this->additionalShippingCost;
    }

    public function setAdditionalShippingCost(string $additionalShippingCost): self
    {
        $this->additionalShippingCost = new DecimalNumber($additionalShippingCost);

        return $this;
    }

    public function getDeliveryTimeNoteType(): ?DeliveryTimeNoteType
    {
        return $this->deliveryTimeNoteType;
    }

    public function setDeliveryTimeNoteType(int $type): self
    {
        $this->deliveryTimeNoteType = new DeliveryTimeNoteType($type);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeInStockNotes(): ?array
    {
        return $this->localizedDeliveryTimeInStockNotes;
    }

    /**
     * @param string[] $localizedDeliveryTimeInStockNotes
     */
    public function setLocalizedDeliveryTimeInStockNotes(array $localizedDeliveryTimeInStockNotes): self
    {
        $this->localizedDeliveryTimeInStockNotes = $localizedDeliveryTimeInStockNotes;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDeliveryTimeOutOfStockNotes(): ?array
    {
        return $this->localizedDeliveryTimeOutOfStockNotes;
    }

    /**
     * @param string[] $localizedDeliveryTimeOutOfStockNotes
     */
    public function setLocalizedDeliveryTimeOutOfStockNotes(array $localizedDeliveryTimeOutOfStockNotes): self
    {
        $this->localizedDeliveryTimeOutOfStockNotes = $localizedDeliveryTimeOutOfStockNotes;

        return $this;
    }

    public function getPackStockType(): ?PackStockType
    {
        return $this->packStockType;
    }

    /**
     * @throws ProductPackConstraintException
     */
    public function setPackStockType(int $packStockType): self
    {
        $this->packStockType = new PackStockType($packStockType);

        return $this;
    }

    public function getMinimalQuantity(): ?int
    {
        return $this->minimalQuantity;
    }

    public function setMinimalQuantity(int $minimalQuantity): self
    {
        $this->minimalQuantity = $minimalQuantity;

        return $this;
    }

    public function getLowStockThreshold(): ?LowStockThreshold
    {
        return $this->lowStockThreshold;
    }

    public function setLowStockThreshold(int $lowStockThreshold): self
    {
        $this->lowStockThreshold = new LowStockThreshold($lowStockThreshold);

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableNowLabels(): ?array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @param string[] $localizedAvailableNowLabels
     */
    public function setLocalizedAvailableNowLabels(array $localizedAvailableNowLabels): self
    {
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableLaterLabels(): ?array
    {
        return $this->localizedAvailableLaterLabels;
    }

    /**
     * @param string[] $localizedAvailableLaterLabels
     */
    public function setLocalizedAvailableLaterLabels(array $localizedAvailableLaterLabels): self
    {
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;

        return $this;
    }

    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }

    public function setAvailableDate(DateTimeInterface $availableDate): self
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    private function setDimension(string $value, string $propertyName): void
    {
        $codeByDimension = [
            'width' => ProductConstraintException::INVALID_WIDTH,
            'height' => ProductConstraintException::INVALID_HEIGHT,
            'depth' => ProductConstraintException::INVALID_DEPTH,
            'weight' => ProductConstraintException::INVALID_WEIGHT,
        ];

        try {
            $this->{$propertyName} = new Dimension($value);
        } catch (DomainConstraintException $e) {
            throw new ProductConstraintException(\sprintf('Invalid product %s.', $propertyName), $codeByDimension[$propertyName], $e);
        }
    }
}
