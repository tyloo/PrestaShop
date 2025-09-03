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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Retrieve Logs data from database.
 * This class should not be used as a Grid query builder. @see LogQueryBuilder
 */
class LogRepository implements RepositoryInterface
{
    private readonly string $logTable;

    /**
     * @param string $databasePrefix
     */
    public function __construct(
        private readonly Connection $connection,
        private $databasePrefix,
    ) {
        $this->logTable = $this->databasePrefix . 'log';
    }

    public function findAll(): array
    {
        $result = $this->connection->executeQuery("SELECT l.* FROM $this->logTable l");

        return $result->fetchAllAssociative();
    }

    /**
     * Get all logs with employee name and avatar information SQL query.
     *
     * @return string the SQL query
     */
    public function findAllWithEmployeeInformationQuery(array $filters)
    {
        $queryBuilder = $this->getAllWithEmployeeInformationQuery($filters);

        $query = $queryBuilder->getSQL();
        $parameters = $queryBuilder->getParameters();

        foreach ($parameters as $pattern => $value) {
            $query = str_replace(":$pattern", $value, $query);
        }

        return $query;
    }

    /**
     * Get all logs with employee name and avatar information.
     *
     * @return array the list of logs
     */
    public function findAllWithEmployeeInformation(array $filters): array
    {
        $queryBuilder = $this->getAllWithEmployeeInformationQuery($filters);
        $statement = $queryBuilder->executeQuery();

        return $statement->fetchAllAssociative();
    }

    /**
     * Get a reusable Query Builder to dump and execute SQL.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getAllWithEmployeeInformationQuery(array $filters)
    {
        $employeeTable = $this->databasePrefix . 'employee';
        $queryBuilder = $this->connection->createQueryBuilder();
        $wheres = array_filter($filters['filters'], fn ($value): bool => ! empty($value));
        $scalarFilters = array_filter($wheres, fn ($key): bool => ! \in_array($key, ['date_from', 'date_to', 'employee'], true), \ARRAY_FILTER_USE_KEY);

        $qb = $queryBuilder
            ->select('l.*', 'e.email', "CONCAT(e.firstname, ' ', e.lastname) as employee")
            ->from($this->logTable, 'l')
            ->innerJoin('l', $employeeTable, 'e', 'l.id_employee = e.id_employee')
            ->orderBy($filters['orderBy'], $filters['sortOrder'])
            ->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        foreach ($scalarFilters as $column => $value) {
            $qb->andWhere("$column LIKE :$column");
            $qb->setParameter($column, '%' . $value . '%');
        }

        /* Manage Dates interval */
        if (! empty($wheres['date_from']) && ! empty($wheres['date_to'])) {
            $qb->andWhere('l.date_add BETWEEN :date_from AND :date_to');
            $qb->setParameters([
                'date_from' => $wheres['date_from'],
                'date_to' => $wheres['date_to'],
            ]);
        }

        /* Manage Employee filter */
        if (! empty($wheres['employee'])) {
            $qb->andWhere('e.lastname LIKE :employee OR e.firstname LIKE :employee');
            $qb->setParameter('employee', '%' . $wheres['employee'] . '%');
        }

        return $qb;
    }

    /**
     * Delete all logs.
     *
     * @return int the number of affected rows
     *
     * @throws DBALException
     */
    public function deleteAll()
    {
        $platform = $this->connection->getDatabasePlatform();

        return $this->connection->executeStatement($platform->getTruncateTableSQL($this->logTable, true));
    }
}
