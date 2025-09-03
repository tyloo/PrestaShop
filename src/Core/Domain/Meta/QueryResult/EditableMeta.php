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

namespace PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\Name;

/**
 * Class EditableMeta is responsible for providing data for meta form.
 */
class EditableMeta
{
    private readonly MetaId $metaId;

    private readonly Name $pageName;

    /**
     * @param int      $metaId
     * @param string   $pageName
     * @param string[] $localisedPageTitles
     * @param string[] $localisedMetaDescriptions
     * @param string[] $localisedUrlRewrites
     *
     * @throws Exception\MetaConstraintException
     * @throws MetaException
     */
    public function __construct(
        $metaId,
        $pageName,
        private readonly array $localisedPageTitles,
        private readonly array $localisedMetaDescriptions,
        private readonly array $localisedUrlRewrites,
    ) {
        $this->metaId = new MetaId($metaId);
        $this->pageName = new Name($pageName);
    }

    public function getMetaId(): MetaId
    {
        return $this->metaId;
    }

    public function getPageName(): Name
    {
        return $this->pageName;
    }

    /**
     * @return string[]
     */
    public function getLocalisedPageTitles(): array
    {
        return $this->localisedPageTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaDescriptions(): array
    {
        return $this->localisedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalisedUrlRewrites(): array
    {
        return $this->localisedUrlRewrites;
    }
}
