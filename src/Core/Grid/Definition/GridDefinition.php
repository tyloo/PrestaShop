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

namespace PrestaShop\PrestaShop\Core\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;
use PrestaShop\PrestaShop\Core\Grid\Exception\InvalidDataException;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;

/**
 * Class Definition is responsible for storing grid definition (columns, row actions & etc.).
 */
final class GridDefinition implements GridDefinitionInterface
{
    /**
     * @param string $id   Unique grid identifier
     * @param string $name
     */
    public function __construct(
        private $id,
        private $name,
        private ColumnCollectionInterface $columns,
        private FilterCollectionInterface $filters,
        private GridActionCollectionInterface $gridActions,
        private BulkActionCollectionInterface $bulkActions,
        private readonly ViewOptionsCollectionInterface $viewOptions,
    ) {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumnById(string $id): ColumnInterface
    {
        /** @var ColumnInterface $column */
        foreach ($this->columns as $column) {
            if ($id === $column->getId()) {
                return $column;
            }
        }

        throw new ColumnNotFoundException(\sprintf('Column with id "%s" not found', $id));
    }

    public function getBulkActions()
    {
        return $this->bulkActions;
    }

    public function getGridActions()
    {
        return $this->gridActions;
    }

    public function getViewOptions()
    {
        return $this->viewOptions;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setName(string $name)
    {
        if (! \is_string($name)) {
            throw new InvalidDataException('Definition name should be a string.');
        }

        $this->name = $name;
    }

    /**
     * @param ColumnCollectionInterface $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    public function setGridActions(GridActionCollectionInterface $gridActions)
    {
        $this->gridActions = $gridActions;
    }

    public function setBulkActions(BulkActionCollectionInterface $bulkActions)
    {
        $this->bulkActions = $bulkActions;
    }

    public function setFilters(FilterCollectionInterface $filters)
    {
        $this->filters = $filters;
    }
}
