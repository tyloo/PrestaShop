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

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command responsible for multiple tax rules groups deletion
 */
class EditTaxRulesGroupCommand
{
    /**
     * @var TaxRulesGroupId
     */
    protected $taxRulesGroupId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var bool|null
     */
    protected $enabled;

    /**
     * @var int[]|null
     */
    protected $shopAssociation;

    /**
     * @throws TaxRulesGroupConstraintException
     */
    public function __construct(int $taxRulesGroupId)
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
    }

    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function setShopAssociation(?array $shopAssociation): self
    {
        if ($shopAssociation !== null && ! $this->assertArrayContainsOnlyIntegerValues($shopAssociation)) {
            throw new TaxRulesGroupConstraintException(\sprintf('Given shop association %s must contain only integer values', var_export($shopAssociation, true)), TaxRulesGroupConstraintException::INVALID_SHOP_ASSOCIATION);
        }

        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    protected function assertArrayContainsOnlyIntegerValues(array $values): bool
    {
        $filterAllIntegers = function ($value) {
            return \is_int($value);
        };

        return ! empty($values) && \count($values) === \count(array_filter($values, $filterAllIntegers));
    }
}
