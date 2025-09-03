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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotDeleteStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotUpdateStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Store;

/**
 * Methods to access data source of Store
 */
class StoreRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @throws CoreException
     * @throws StoreNotFoundException
     */
    public function get(StoreId $storeId): Store
    {
        /** @var Store $store */
        $store = $this->getObjectModel(
            $storeId->getValue(),
            Store::class,
            StoreNotFoundException::class
        );

        return $store;
    }

    public function partialUpdate(Store $store, array $propertiesToUpdate, int $errorCode): void
    {
        $this->partiallyUpdateObjectModel(
            $store,
            $propertiesToUpdate,
            CannotUpdateStoreException::class,
            $errorCode
        );
    }

    public function delete(StoreId $storeId): void
    {
        $this->deleteObjectModel(
            $this->get($storeId),
            CannotDeleteStoreException::class,
            CannotDeleteStoreException::FAILED_DELETE
        );
    }

    /**
     * @return ShopId[]
     */
    public function getShopIdsByConstraint(ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId() !== null) {
            return $this->getAssociatedShopIdsFromGroup($shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return array_map(static fn (array $result): ShopId => new ShopId((int) $result['id_shop']), $this->connection->createQueryBuilder()
                ->select('id_shop')
                ->from($this->dbPrefix . 'store_shop', 'ss')
                ->executeQuery()
                ->fetchAllAssociative()
            );
        }

        return [$shopConstraint->getShopId()];
    }

    /**
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ss.id_shop')
            ->from($this->dbPrefix . 'store_shop', 'ss')
            ->innerJoin(
                'ss',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = ss.id_shop'
            )
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->groupBy('id_shop')
        ;

        return array_map(static fn (array $result): ShopId => new ShopId((int) $result['id_shop']), $qb->executeQuery()->fetchAllAssociative());
    }
}
