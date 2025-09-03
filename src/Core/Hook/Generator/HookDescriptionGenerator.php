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

namespace PrestaShop\PrestaShop\Core\Hook\Generator;

use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShop\PrestaShop\Core\Util\String\StringValidatorInterface;

/**
 * Generates description for hook names.
 */
final class HookDescriptionGenerator implements HookDescriptionGeneratorInterface
{
    public function __construct(
        private readonly array $hookDescriptions,
        private readonly StringValidatorInterface $stringValidator,
        private readonly StringModifierInterface $stringModifier,
    ) {
    }

    public function generate($hookName): HookDescription
    {
        foreach ($this->hookDescriptions as $hookDescription) {
            $prefix = $hookDescription['prefix'] ?? '';
            $suffix = $hookDescription['suffix'] ?? '';

            if ($this->stringValidator->startsWithAndEndsWith($hookName, $prefix, $suffix)
                && ! $this->stringValidator->doesContainsWhiteSpaces($hookName)
            ) {
                $hookId = $this->extractHookId($hookName, $prefix, $suffix);

                return new HookDescription(
                    $hookName,
                    $this->getTextWithHookId($hookDescription['title'], $hookId),
                    $this->getTextWithHookId($hookDescription['description'], $hookId)
                );
            }
        }

        return new HookDescription(
            $hookName,
            '',
            ''
        );
    }

    /**
     * Removes from hook name id prefix and suffix.
     *
     * @param string $hookName
     * @param string $prefix
     * @param string $suffix
     */
    private function extractHookId($hookName, $prefix, $suffix): string
    {
        return str_replace([$prefix, $suffix], '', $hookName);
    }

    /**
     * Gets text with replaced hook id.
     *
     * @param string $description
     *
     * @return string
     */
    private function getTextWithHookId($description, string $hookId)
    {
        if (! $this->doesHookDescriptionContainsPlaceholder($description)) {
            return $description;
        }

        $hookIdSplitByCamelCase = $this->stringModifier->splitByCamelCase($hookId);

        $isPlaceholderAsFirstValueInString = $this->doesPlaceholderIsTheFirstElementOfTheDescription($description);

        if ($isPlaceholderAsFirstValueInString) {
            $hookIdSplitByCamelCase = ucfirst($hookIdSplitByCamelCase);
        } else {
            $hookIdSplitByCamelCase = strtolower($hookIdSplitByCamelCase);
        }

        return \sprintf($description, $hookIdSplitByCamelCase);
    }

    /**
     * Checks if hook description contains placeholder value.
     *
     * @param string $description
     */
    private function doesHookDescriptionContainsPlaceholder($description): bool
    {
        return str_contains($description, '%s');
    }

    /**
     * Checks if placeholder is the first element of the string.
     *
     * @param string $description
     */
    private function doesPlaceholderIsTheFirstElementOfTheDescription($description): bool
    {
        return str_starts_with($description, '%s');
    }
}
