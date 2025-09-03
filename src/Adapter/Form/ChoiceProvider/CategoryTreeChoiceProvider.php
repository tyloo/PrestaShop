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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CategoryTreeChoiceProvider provides categories as tree choices.
 */
final class CategoryTreeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @param int  $contextShopRootCategoryId
     * @param bool $enabledCategoriesOnly
     */
    public function __construct(
        private readonly CategoryDataProvider $categoryDataProvider,
        private $contextShopRootCategoryId,
        private $enabledCategoriesOnly = false,
    ) {
    }

    /**
     * @return list
     */
    public function getChoices(): array
    {
        $categories = $this->categoryDataProvider->getNestedCategories($this->contextShopRootCategoryId, false, $this->enabledCategoriesOnly);
        $choices = [];

        foreach ($categories as $category) {
            $choices[] = $this->buildChoiceTree($category);
        }

        return $choices;
    }

    private function buildChoiceTree(array $category): array
    {
        $tree = [
            'id_category' => $category['id_category'],
            'name' => $category['name'],
        ];

        if (isset($category['children'])) {
            foreach ($category['children'] as $childCategory) {
                $tree['children'][] = $this->buildChoiceTree($childCategory);
            }
        }

        return $tree;
    }
}
