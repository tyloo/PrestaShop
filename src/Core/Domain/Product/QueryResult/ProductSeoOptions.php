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

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

/**
 * Transfers data about Product SEO options
 */
class ProductSeoOptions
{
    /**
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param string[] $localizedLinkRewrites
     */
    public function __construct(
        private readonly array $localizedMetaTitles,
        private readonly array $localizedMetaDescriptions,
        private readonly array $localizedLinkRewrites,
        private readonly string $redirectType,
        private readonly ?RedirectTargetInformation $redirectTarget,
    ) {
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles(): array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions(): array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLinkRewrites(): array
    {
        return $this->localizedLinkRewrites;
    }

    public function getRedirectType(): string
    {
        return $this->redirectType;
    }

    public function getRedirectTargetId(): int
    {
        return $this->redirectTarget !== null ? $this->redirectTarget->getId() : RedirectTarget::NO_TARGET;
    }

    public function getRedirectTarget(): ?RedirectTargetInformation
    {
        return $this->redirectTarget;
    }
}
