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
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Class RequestSqlRepository is responsible for retrieving RequestSql data from database.
 */
class RequestSqlRepository implements RepositoryInterface
{
    private readonly string $requestSqlTable;

    /**
     * @param string $dbPrefix
     */
    public function __construct(
        private readonly Connection $connection,
        $dbPrefix,
    ) {
        $this->requestSqlTable = $dbPrefix . 'request_sql';
    }

    public function findAll(): array
    {
        $statement = $this->connection->query("SELECT rs.* FROM $this->requestSqlTable rs");

        return $statement->fetchAll();
    }

    /**
     * Get count of all request sql's.
     *
     * @return int Number of request sql rows
     */
    public function getCount(): int
    {
        $statement = $this->connection->query("SELECT COUNT(rs.id_request_sql) AS c FROM $this->requestSqlTable rs");
        $row = $statement->fetch();

        return (int) $row['c'];
    }
}
