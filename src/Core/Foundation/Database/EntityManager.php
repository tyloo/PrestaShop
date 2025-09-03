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

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

use PrestaShop\PrestaShop\Adapter\EntityMetaDataRetriever;

class EntityManager
{
    private array $entityMetaData = [];

    public function __construct(
        private readonly DatabaseInterface $db,
        private readonly \PrestaShop\PrestaShop\Core\ConfigurationInterface $configuration,
    ) {
    }

    /**
     * Return current database object used.
     */
    public function getDatabase(): DatabaseInterface
    {
        return $this->db;
    }

    /**
     * Return current repository used.
     *
     * @param string $className
     */
    public function getRepository($className)
    {
        $repositoryClass = null;
        if (\is_callable([$className, 'getRepositoryClassName'])) {
            $repositoryClass = \call_user_func([$className, 'getRepositoryClassName']);
        }

        if (! $repositoryClass) {
            $repositoryClass = EntityRepository::class;
        }

        return new $repositoryClass(
            $this,
            $this->configuration->get('_DB_PREFIX_'),
            $this->getEntityMetaData($className)
        );
    }

    /**
     * Return entity's meta data.
     *
     * @param string $className
     *
     * @throws \PrestaShop\PrestaShop\Adapter\CoreException
     */
    public function getEntityMetaData($className)
    {
        if (! \array_key_exists($className, $this->entityMetaData)) {
            $metaDataRetriever = new EntityMetaDataRetriever();
            $this->entityMetaData[$className] = $metaDataRetriever->getEntityMetaData($className);
        }

        return $this->entityMetaData[$className];
    }

    /**
     * Flush entity to DB.
     *
     * @return $this
     */
    public function save(EntityInterface $entity): static
    {
        $entity->save();

        return $this;
    }

    /**
     * DElete entity from DB.
     *
     * @return $this
     */
    public function delete(EntityInterface $entity): static
    {
        $entity->delete();

        return $this;
    }
}
