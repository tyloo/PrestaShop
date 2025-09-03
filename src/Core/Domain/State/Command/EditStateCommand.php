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

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Edits state with provided data
 */
class EditStateCommand
{
    private readonly StateId $stateId;

    /**
     * @var CountryId|null
     */
    private $countryId;

    /**
     * @var ZoneId|null
     */
    private $zoneId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $isoCode;

    /**
     * @var bool|null
     */
    private $active;

    /**
     * @throws StateConstraintException
     */
    public function __construct(int $stateId)
    {
        $this->stateId = new StateId($stateId);
    }

    public function getStateId(): StateId
    {
        return $this->stateId;
    }

    public function getCountryId(): ?CountryId
    {
        return $this->countryId;
    }

    /**
     * @throws CountryConstraintException
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = new CountryId($countryId);

        return $this;
    }

    public function getZoneId(): ?ZoneId
    {
        return $this->zoneId;
    }

    /**
     * @throws ZoneException
     */
    public function setZoneId(int $zoneId): self
    {
        $this->zoneId = new ZoneId($zoneId);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
