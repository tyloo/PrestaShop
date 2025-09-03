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

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * This command modifies an existing SqlRequest object, replacing its data by the provided one.
 */
class EditSqlRequestCommand
{
    private SqlRequestId $sqlRequestId;

    private ?string $name = null;

    private ?string $sql = null;

    public function __construct(SqlRequestId $sqlRequestId)
    {
        $this->setSqlRequestId($sqlRequestId);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSql(): ?string
    {
        return $this->sql;
    }

    public function getSqlRequestId(): SqlRequestId
    {
        return $this->sqlRequestId;
    }

    private function setSqlRequestId(SqlRequestId $sqlRequestId): static
    {
        $this->sqlRequestId = $sqlRequestId;

        return $this;
    }

    /**
     * Set Request SQL name.
     *
     * @param string $name
     *
     * @throws SqlRequestConstraintException
     */
    public function setName($name): static
    {
        if (! \is_string($name) || ($name === '' || $name === '0')) {
            throw new SqlRequestConstraintException(\sprintf('Invalid SqlRequest name "%s"', var_export($name, true)), SqlRequestConstraintException::INVALID_NAME);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set Request SQL query.
     *
     * @param string $sql
     *
     * @throws SqlRequestConstraintException
     */
    public function setSql($sql): static
    {
        if (! \is_string($sql) || ($sql === '' || $sql === '0')) {
            throw new SqlRequestConstraintException(\sprintf('Invalid SqlRequest SQL query "%s"', var_export($sql, true)), SqlRequestConstraintException::INVALID_SQL_QUERY);
        }

        $this->sql = $sql;

        return $this;
    }
}
