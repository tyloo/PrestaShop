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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\Command;

use InvalidArgumentException;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidProductQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Updates product in given order.
 */
class UpdateProductInOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var DecimalNumber
     */
    private $priceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $priceTaxExcluded;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(
        int $orderId,
        private readonly int $orderDetailId,
        string $priceTaxIncluded,
        string $priceTaxExcluded,
        int $quantity,
        private readonly ?int $orderInvoiceId = null,
    ) {
        $this->orderId = new OrderId($orderId);
        try {
            $this->priceTaxIncluded = new DecimalNumber($priceTaxIncluded);
            $this->priceTaxExcluded = new DecimalNumber($priceTaxExcluded);
        } catch (InvalidArgumentException) {
            throw new InvalidAmountException();
        }

        $this->setQuantity($quantity);
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    /**
     * @return DecimalNumber
     */
    public function getPriceTaxIncluded()
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getPriceTaxExcluded()
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getOrderInvoiceId(): ?int
    {
        return $this->orderInvoiceId;
    }

    /**
     * @throws InvalidProductQuantityException
     */
    private function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidProductQuantityException('When adding a product quantity must be strictly positive');
        }

        $this->quantity = $quantity;
    }
}
