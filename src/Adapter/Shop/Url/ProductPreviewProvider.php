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

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;
use Tools;

class ProductPreviewProvider implements UrlProviderInterface
{
    public function __construct(
        protected Link $link,
        private readonly bool $urlRewritingIsEnabled,
        protected int $employeeId,
    ) {
    }

    /**
     * Create a link to a product.
     */
    public function getUrl(?int $productId = null, ?bool $active = true, ?int $shopId = null): string
    {
        $preview_url = $this->link->getProductLink(
            $productId,
            null,
            null,
            null,
            null,
            $shopId,
            null,
            $this->urlRewritingIsEnabled
        );

        if (! $active) {
            $token = Tools::getAdminTokenLite('AdminProducts');
            $preview_url = \sprintf(
                '%s%sadtoken=%s&id_employee=%d&preview=1',
                $preview_url,
                (str_contains((string) $preview_url, '?')) ? '&' : '?',
                $token,
                $this->employeeId
            );
        }

        return $preview_url;
    }
}
