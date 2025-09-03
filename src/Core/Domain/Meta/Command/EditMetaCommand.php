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

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Command;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\Name;

class EditMetaCommand extends AbstractMetaCommand
{
    private readonly MetaId $metaId;

    private ?Name $pageName = null;

    /**
     * @var string[]
     */
    private ?array $localisedPageTitles = null;

    /**
     * @var string[]
     */
    private ?array $localisedMetaDescriptions = null;

    /**
     * @var string[]
     */
    private ?array $localisedRewriteUrls = null;

    /**
     * @param int $metaId
     *
     * @throws MetaException
     */
    public function __construct($metaId)
    {
        $this->metaId = new MetaId($metaId);
    }

    public function getMetaId(): MetaId
    {
        return $this->metaId;
    }

    public function getPageName(): ?Name
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setPageName($pageName)
    {
        $this->pageName = new Name($pageName);

        return $this;
    }

    /**
     * @param string[] $localisedPageTitles
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setLocalisedPageTitles(array $localisedPageTitles)
    {
        foreach ($localisedPageTitles as $idLang => $title) {
            $this->assertNameMatchesRegexPattern($idLang, $title, MetaConstraintException::INVALID_PAGE_TITLE);
        }

        $this->localisedPageTitles = $localisedPageTitles;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedPageTitles(): ?array
    {
        return $this->localisedPageTitles;
    }

    /**
     * @param string[] $localisedMetaDescriptions
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setLocalisedMetaDescriptions(array $localisedMetaDescriptions)
    {
        foreach ($localisedMetaDescriptions as $idLang => $description) {
            $this->assertNameMatchesRegexPattern($idLang, $description, MetaConstraintException::INVALID_META_DESCRIPTION);
        }

        $this->localisedMetaDescriptions = $localisedMetaDescriptions;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaDescriptions(): ?array
    {
        return $this->localisedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalisedRewriteUrls(): ?array
    {
        return $this->localisedRewriteUrls;
    }

    /**
     * @param string[] $localisedRewriteUrls
     *
     * @return self
     */
    public function setLocalisedRewriteUrls(array $localisedRewriteUrls)
    {
        $this->localisedRewriteUrls = $localisedRewriteUrls;

        return $this;
    }
}
