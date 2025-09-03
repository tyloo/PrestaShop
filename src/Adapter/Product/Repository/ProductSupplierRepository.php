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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductSupplierValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotAddProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotBulkDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotUpdateProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\InvalidProductSupplierAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotAssociatedException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\SupplierAssociationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use ProductSupplier;

/**
 * Methods for accessing ProductSupplier data source
 */
class ProductSupplierRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly ProductSupplierValidator $productSupplierValidator,
    ) {
    }

    /**
     * @throws ProductSupplierNotFoundException
     */
    public function get(ProductSupplierId $productSupplierId): ProductSupplier
    {
        /** @var ProductSupplier $productSupplier */
        $productSupplier = $this->getObjectModel(
            $productSupplierId->getValue(),
            ProductSupplier::class,
            ProductSupplierNotFoundException::class
        );

        return $productSupplier;
    }

    /**
     * Returns productSupplierId matching the association if present (null instead)
     * If the association had a productSupplierId defined which doesn't match the found result it means the provided
     * data is not consistent so an exception is raised.
     *
     * @throws InvalidProductSupplierAssociationException
     */
    public function findIdByAssociation(SupplierAssociationInterface $association): ?ProductSupplierId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ps.id_product_supplier')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->andWhere('ps.id_product_attribute = :combinationId')
            ->andWhere('ps.id_supplier = :supplierId')
            ->setParameter('combinationId', $association->getCombinationId()->getValue())
            ->setParameter('supplierId', $association->getSupplierId()->getValue())
        ;

        if ($association->getProductId() instanceof ProductId) {
            $qb
                ->andWhere('ps.id_product = :productId')
                ->setParameter('productId', $association->getProductId()->getValue())
            ;
        }

        $result = $qb->executeQuery()->fetchAssociative();
        if (empty($result)) {
            return null;
        }

        $productSupplierId = (int) $result['id_product_supplier'];

        if ($association->getProductSupplierId() instanceof ProductSupplierId
            && $productSupplierId !== $association->getProductSupplierId()->getValue()) {
            throw new InvalidProductSupplierAssociationException(\sprintf('Invalid ProductSupplier ID in association: %s Provided is %d but the persisted one is %d.', (string) $association, $association->getProductSupplierId()->getValue(), $productSupplierId));
        }

        return new ProductSupplierId($productSupplierId);
    }

    /**
     * Returns the ProductSupplier matching the association, if it's not found an exception is thrown. If you are unsure
     * of the presence of an association use getIdByAssociation instead to check the presence, it returns null when not found.
     *
     * @throws InvalidProductSupplierAssociationException
     * @throws ProductSupplierNotAssociatedException
     * @throws ProductSupplierNotFoundException
     */
    public function getByAssociation(SupplierAssociationInterface $association): ProductSupplier
    {
        $productSupplierId = $this->findIdByAssociation($association);
        if (! $productSupplierId instanceof ProductSupplierId) {
            throw new ProductSupplierNotAssociatedException(\sprintf('Could not find a ProductSupplier matching this association: %s', (string) $association));
        }

        return $this->get($productSupplierId);
    }

    /**
     * Returns the ID of the Supplier set as default for this product, data comes from product table
     * but is only returned if the association is present in product_supplier relation table.
     */
    public function getDefaultSupplierId(ProductId $productId): ?SupplierId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('p.id_supplier AS default_supplier_id')
            ->from($this->dbPrefix . 'product', 'p')
            // Right join association matching the default supplier, it must be present since it is a right join
            ->rightJoin(
                'p',
                $this->dbPrefix . 'product_supplier',
                'ps',
                'ps.id_product = p.id_product AND ps.id_supplier = p.id_supplier'
            )
            ->setParameter('productId', $productId->getValue())
            ->where('p.id_product = :productId')
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if (! $result) {
            return null;
        }

        return new SupplierId((int) $result['default_supplier_id']);
    }

    /**
     * Returns the ProductSupplier associated to a product as its default one.
     */
    public function getDefaultProductSupplierId(ProductId $productId): ?ProductSupplierId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('ps.id_product_supplier AS default_supplier_id')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->innerJoin(
                'ps',
                $this->dbPrefix . 'product',
                'p',
                'ps.id_supplier = p.id_supplier'
            )
            ->where('ps.id_product = :productId')
            ->andWhere('ps.id_supplier = p.id_supplier')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if (empty($result['default_supplier_id'])) {
            return null;
        }

        return new ProductSupplierId((int) $result['default_supplier_id']);
    }

    /**
     * @return ProductSupplierAssociation[]
     */
    public function getAssociationsForSupplier(ProductId $productId, SupplierId $supplierId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('ps.id_product_attribute, ps.id_product_supplier')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->andWhere('ps.id_product = :productId')
            ->andWhere('ps.id_supplier = :supplierId')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('supplierId', $supplierId->getValue())
            ->addOrderBy('ps.id_product_supplier', 'ASC')
        ;

        $results = $qb->executeQuery()->fetchAllAssociative();

        if (empty($results)) {
            return [];
        }

        return array_map(fn (array $row) => new ProductSupplierAssociation(
            $productId->getValue(),
            (int) $row['id_product_attribute'],
            $supplierId->getValue(),
            empty($row['id_product_supplier']) ? null : (int) $row['id_product_supplier']
        ), $results);
    }

    /**
     * @return SupplierId[]
     */
    public function getAssociatedSupplierIds(ProductId $productId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('ps.id_supplier')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->andWhere('ps.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->groupBy('ps.id_supplier')
        ;

        $results = $qb->executeQuery()->fetchAllAssociative();

        if (empty($results)) {
            return [];
        }

        return array_map(static fn (array $row): SupplierId => new SupplierId((int) $row['id_supplier']), $results);
    }

    /**
     * @throws CannotAddProductSupplierException
     */
    public function add(ProductSupplier $productSupplier, int $errorCode = 0): ProductSupplierId
    {
        $this->productSupplierValidator->validate($productSupplier);
        $id = $this->addObjectModel($productSupplier, CannotAddProductSupplierException::class, $errorCode);

        return new ProductSupplierId($id);
    }

    /**
     * @throws CannotUpdateProductSupplierException
     */
    public function update(ProductSupplier $productSupplier): void
    {
        $this->productSupplierValidator->validate($productSupplier);
        $this->updateObjectModel($productSupplier, CannotUpdateProductSupplierException::class);
    }

    /**
     * @throws CannotDeleteProductSupplierException
     */
    public function delete(ProductSupplierId $productSupplierId): void
    {
        $this->deleteObjectModel($this->get($productSupplierId), CannotDeleteProductSupplierException::class);
    }

    /**
     * @throws CannotBulkDeleteProductSupplierException
     */
    public function bulkDelete(array $productSupplierIds): void
    {
        $failedIds = [];
        foreach ($productSupplierIds as $productSupplierId) {
            try {
                $this->delete($productSupplierId);
            } catch (CannotDeleteProductSupplierException) {
                $failedIds[] = $productSupplierId->getValue();
            }
        }

        if ($failedIds === []) {
            return;
        }

        throw new CannotBulkDeleteProductSupplierException($failedIds, \sprintf('Failed to delete following product suppliers: %s', implode(', ', $failedIds)));
    }

    public function getProductSuppliersInfo(ProductId $productId, ?CombinationIdInterface $combinationId = null): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->leftJoin(
                'ps',
                $this->dbPrefix . 'supplier',
                's',
                'ps.id_supplier = s.id_supplier'
            )
            ->where('ps.id_product = :productId')
            ->addOrderBy('s.name', 'ASC')
            ->addOrderBy('s.id_supplier', 'ASC')
            ->setParameter('productId', $productId->getValue())
        ;

        if ($combinationId instanceof CombinationIdInterface) {
            $qb->andWhere('ps.id_product_attribute = :combinationId')
                ->setParameter('combinationId', $combinationId->getValue())
            ;
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Returns true if some suppliers have identical names, in which case we integrate the ID into the name to avoid confusion.
     */
    public function hasDuplicateSuppliersName(): bool
    {
        // We need to fetch all names and perform the check programmatically because MySQL is case-insensitive
        $qb = $this->connection->createQueryBuilder();
        $qb->select('name')
            ->from($this->dbPrefix . 'supplier', 's')
        ;

        $suppliers = $qb->executeQuery()->fetchAllAssociative();
        $names = [];
        foreach ($suppliers as $supplier) {
            if (\in_array($supplier['name'], $names, true)) {
                return true;
            }

            $names[] = $supplier['name'];
        }

        return false;
    }

    /**
     * Returns the list of ProductSupplierId which don't match the expected suppliers.
     *
     * @return ProductSupplierId[]
     */
    public function getUselessProductSupplierIds(ProductId $productId, array $expectedSuppliersId): array
    {
        $supplierIds = array_map(fn (SupplierId $supplierId) => (string) $supplierId->getValue(), $expectedSuppliersId);

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ps.id_product_supplier')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->where($qb->expr()->and(
                $qb->expr()->eq('id_product', $productId->getValue()),
                $qb->expr()->notIn('id_supplier', $supplierIds)
            ))
        ;

        $uselessProductSupplierIds = $qb->executeQuery()->fetchAllAssociative();
        if (empty($uselessProductSupplierIds)) {
            return [];
        }

        return array_map(static fn (array $row): ProductSupplierId => new ProductSupplierId((int) $row['id_product_supplier']), $uselessProductSupplierIds);
    }
}
