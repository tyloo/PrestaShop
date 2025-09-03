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
 * Class LanguageQueryBuilder provides query builders for languages grid.
 */
final class LanguageQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->getLanguageQueryBuilder($searchCriteria)
            ->select('l.*');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $builder)
            ->applyPagination($searchCriteria, $builder);

        return $builder;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getLanguageQueryBuilder($searchCriteria)->select('COUNT(id_lang)');
    }

    /**
     * @return QueryBuilder
     */
    private function getLanguageQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'lang', 'l');

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria): void
    {
        $allowedFilters = [
            'id_lang',
            'name',
            'iso_code',
            'language_code',
            'locale',
            'date_format_lite',
            'date_format_full',
            'active',
        ];

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (! \in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if (\in_array($filterName, ['id_lang', 'active'], true)) {
                $builder->andWhere($filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);

                continue;
            }

            $builder->andWhere($filterName . ' LIKE :' . $filterName);
            $builder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
