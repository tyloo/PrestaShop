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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Responsible for building queries for profiles grid data.
 */
final class ProfileQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @param string $dbPrefix
     * @param int    $languageId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private $languageId,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('p.id_profile, pl.name')
        ;

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(p.id_profile)');
    }

    /**
     * Get generic query builder.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'profile', 'p')
            ->innerJoin('p', $this->dbPrefix . 'profile_lang', 'pl', 'p.id_profile = pl.id_profile')
            ->andWhere('pl.id_lang = :language')
            ->setParameter('language', $this->languageId)
        ;

        $allowedFilters = [
            'id_profile',
            'name',
        ];

        foreach ($filters as $name => $value) {
            if (! \in_array($name, $allowedFilters, true)) {
                continue;
            }

            if ($name === 'id_profile') {
                $qb->andWhere('p.id_profile = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            $qb->andWhere(\sprintf('%s LIKE :%s', $name, $name));
            $qb->setParameter($name, '%' . $value . '%');
        }

        return $qb;
    }
}
