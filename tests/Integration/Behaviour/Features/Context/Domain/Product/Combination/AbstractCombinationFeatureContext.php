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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractCombinationFeatureContext extends AbstractProductFeatureContext
{
    protected function getCombinationsList(string $productReference, int $shopId, ?ProductCombinationFilters $combinationFilters = null): CombinationListForEditing
    {
        return $this->getQueryBus()->handle(new GetEditableCombinationsList(
            $this->getSharedStorage()->get($productReference),
            $this->getDefaultLangId(),
            ShopConstraint::shop($shopId),
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getLimit() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getOffset() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getOrderBy() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getOrderWay() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getFilters() : []
        ));
    }

    /**
     * @return CombinationId[]
     */
    protected function getCombinationIds(string $productReference, int $shopId, ?ProductCombinationFilters $combinationFilters = null): array
    {
        return $this->getQueryBus()->handle(new GetCombinationIds(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($shopId),
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getLimit() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getOffset() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getOrderBy() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getOrderWay() : null,
            $combinationFilters instanceof ProductCombinationFilters ? $combinationFilters->getFilters() : []
        ));
    }

    protected function getCombinationForEditing(string $combinationReference, int $shopId): CombinationForEditing
    {
        return $this->getQueryBus()->handle(new GetCombinationForEditing(
            $this->getSharedStorage()->get($combinationReference),
            ShopConstraint::shop($shopId)
        ));
    }

    protected function buildProductCombinationFiltersForShop(int $productId, TableNode $tableNode, int $shopId): ProductCombinationFilters
    {
        $dataRows = $tableNode->getRowsHash();
        $defaults = ProductCombinationFilters::getDefaults();

        $limit = isset($dataRows['limit']) ? (int) $dataRows['limit'] : $defaults['limit'];
        $offset = isset($dataRows['page']) ? $this->countOffset((int) $dataRows['page'], $limit) : $defaults['offset'];
        $orderBy = isset($dataRows['order by']) ? $this->getDbField($dataRows['order by']) : $defaults['orderBy'];
        $orderWay = isset($dataRows['order way']) ? $this->getDbField($dataRows['order way']) : $defaults['sortOrder'];
        unset($dataRows['limit'], $dataRows['page'], $dataRows['order by'], $dataRows['order way'], $dataRows['criteria']);

        $filters = $defaults['filters'];
        $filters['product_id'] = $productId;
        $filters['shop_id'] = $this->getDefaultShopId();

        foreach ($dataRows as $criteriaField => $criteriaValue) {
            $attributeGroupMatch = preg_match('/attributes\[(.*?)\]/', $criteriaField, $matches) ? $matches[1] : null;
            if ($attributeGroupMatch !== null) {
                $attributeGroupId = $this->getSharedStorage()->get($attributeGroupMatch);
                $attributes = PrimitiveUtils::castStringArrayIntoArray($criteriaValue);
                foreach ($attributes as $attributeRef) {
                    $filters['attributes'][$attributeGroupId][] = $this->getSharedStorage()->get($attributeRef);
                }
            } elseif ($criteriaField === 'is default') {
                $filters[$this->getDbField('is default')] = PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['is default']);
            } else {
                $filters[$this->getDbField($criteriaField)] = $criteriaValue;
            }
        }

        return new ProductCombinationFilters(
            ShopConstraint::shop($shopId),
            [
                'limit' => $limit,
                'offset' => $offset,
                'orderBy' => $orderBy,
                'sortOrder' => $orderWay,
                'filters' => $filters,
            ]
        );
    }

    private function getDbField(string $field): string
    {
        $fieldMap = [
            'impact on price' => 'price',
            'is default' => 'default_on',
        ];

        return $fieldMap[$field] ?? $field;
    }

    private function countOffset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }
}
