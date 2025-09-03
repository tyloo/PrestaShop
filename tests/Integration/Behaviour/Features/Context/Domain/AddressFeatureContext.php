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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Cart;
use Db;
use DbQuery;
use Order;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CountryStateByIdChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ManufacturerNameByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AbstractEditAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCartAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditOrderAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Cart\CartAddressType;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderAddressType;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CountryByIdChoiceProvider;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class AddressFeatureContext extends AbstractDomainFeatureContext
{
    private const DEFAULT_ADDRESS_ID = 1;

    private const DEFAULT_COUNTRY_STATE_ID = 0;

    /**
     * @When I add new brand address :manufacturerAddressReference with following details:
     */
    public function addNewBrandAddressWithFollowingDetails(string $manufacturerAddressReference, TableNode $table): void
    {
        $testCaseData = $table->getRowsHash();
        $editableManufacturerAddress = $this->mapEditableManufacturerAddress(
            $testCaseData,
            self::DEFAULT_ADDRESS_ID
        );
        /** @var AddressId $addressIdObject */
        $addressIdObject = $this->getCommandBus()->handle(new AddManufacturerAddressCommand(
            $editableManufacturerAddress->getLastName(),
            $editableManufacturerAddress->getFirstName(),
            $editableManufacturerAddress->getAddress(),
            $editableManufacturerAddress->getCountryId(),
            $editableManufacturerAddress->getCity(),
            $editableManufacturerAddress->getManufacturerId(),
            null,
            null,
            $editableManufacturerAddress->getStateId()
        ));
        SharedStorage::getStorage()->set($manufacturerAddressReference, $addressIdObject->getValue());
    }

    /**
     * @Then brand address :manufacturerAddressReference should have following details:
     */
    public function manufacturerAddressShouldHaveFollowingDetails(string $manufacturerAddressReference, TableNode $table): void
    {
        $manufacturerAddressId = SharedStorage::getStorage()->get($manufacturerAddressReference);
        /** @var EditableManufacturerAddress $editableManufacturerAddress */
        $editableManufacturerAddress = $this->getQueryBus()->handle(
            new GetManufacturerAddressForEditing($manufacturerAddressId)
        );
        $testCaseData = $table->getRowsHash();
        $expectedEditableManufacturerAddress = $this->mapEditableManufacturerAddress(
            $testCaseData,
            $manufacturerAddressId
        );
        Assert::assertEquals($expectedEditableManufacturerAddress, $editableManufacturerAddress);
    }

    /**
     * @When I add new address to customer :customerReference with following details:
     */
    public function addNewAddressToCustomerWithFollowingDetails(string $customerReference, TableNode $table): void
    {
        $testCaseData = $table->getRowsHash();
        $customerId = SharedStorage::getStorage()->get($customerReference);
        /** @var CountryByIdChoiceProvider $countryChoiceProvider */
        $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $countryId = (int) $countryChoiceProvider->getChoices()[$testCaseData['Country']];
        $countryStateId = self::DEFAULT_COUNTRY_STATE_ID;
        if (isset($testCaseData['State'])) {
            $countryStateId = $this->getStateId($countryId, $testCaseData);
        }

        /** @var AddressId $addressIdObject */
        $addressIdObject = $this->getCommandBus()->handle(new AddCustomerAddressCommand(
            $customerId,
            $testCaseData['Address alias'],
            $testCaseData['First name'],
            $testCaseData['Last name'],
            $testCaseData['Address'],
            $testCaseData['City'],
            $countryId,
            $testCaseData['Postal code'],
            $testCaseData['DNI'] ?? null,
            null,
            null,
            null,
            $countryStateId
        ));
        SharedStorage::getStorage()->set($testCaseData['Address alias'], $addressIdObject->getValue());
    }

    /**
     * @When I edit address :addressReference with following details:
     */
    public function editAddressToCustomerWithFollowingDetails(string $addressReference, TableNode $table): void
    {
        $testCaseData = $table->getRowsHash();
        $customerAddressId = (int) SharedStorage::getStorage()->get($addressReference);

        $editAddressCommand = new EditCustomerAddressCommand($customerAddressId);
        $this->updateEditCommandFields($editAddressCommand, $testCaseData);

        try {
            /** @var AddressId $addressIdObject */
            $addressIdObject = $this->getCommandBus()->handle($editAddressCommand);
            SharedStorage::getStorage()->set($testCaseData['Address alias'], $addressIdObject->getValue());
        } catch (AddressException $addressException) {
            $this->setLastException($addressException);
        }
    }

    /**
     * @When I edit :addressType address for order :orderReference with following details:
     */
    public function editOrderAddressWithFollowingDetails(string $addressType, string $orderReference, TableNode $table): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $testCaseData = $table->getRowsHash();
        if ($addressType === 'delivery') {
            $addressType = OrderAddressType::DELIVERY_ADDRESS_TYPE;
        } elseif ($addressType === 'invoice') {
            $addressType = OrderAddressType::INVOICE_ADDRESS_TYPE;
        }

        $editOrderAddressCommand = new EditOrderAddressCommand($orderId, $addressType);
        $this->updateEditCommandFields($editOrderAddressCommand, $testCaseData);

        try {
            /** @var AddressId $addressIdObject */
            $addressIdObject = $this->getCommandBus()->handle($editOrderAddressCommand);
            SharedStorage::getStorage()->set($testCaseData['Address alias'], $addressIdObject->getValue());
        } catch (AddressException $addressException) {
            $this->setLastException($addressException);
        }
    }

    /**
     * @When I edit :addressType address for cart :cartReference with following details:
     */
    public function editCartAddressWithFollowingDetails(string $addressType, string $cartReference, TableNode $table): void
    {
        $cartId = SharedStorage::getStorage()->get($cartReference);
        $testCaseData = $table->getRowsHash();
        if ($addressType === 'delivery') {
            $addressType = CartAddressType::DELIVERY_ADDRESS_TYPE;
        } elseif ($addressType === 'invoice') {
            $addressType = CartAddressType::INVOICE_ADDRESS_TYPE;
        }

        $editCartAddressCommand = new EditCartAddressCommand($cartId, $addressType);
        $this->updateEditCommandFields($editCartAddressCommand, $testCaseData);

        try {
            /** @var AddressId $addressIdObject */
            $addressIdObject = $this->getCommandBus()->handle($editCartAddressCommand);
            SharedStorage::getStorage()->set($testCaseData['Address alias'], $addressIdObject->getValue());
        } catch (AddressException $addressException) {
            $this->setLastException($addressException);
        }
    }

    private function updateEditCommandFields(AbstractEditAddressCommand $editAddressCommand, array $testCaseData): void
    {
        if (! empty($testCaseData['Address alias'])) {
            $editAddressCommand->setAddressAlias($testCaseData['Address alias']);
        }

        if (! empty($testCaseData['First name'])) {
            $editAddressCommand->setFirstName($testCaseData['First name']);
        }

        if (! empty($testCaseData['Last name'])) {
            $editAddressCommand->setLastName($testCaseData['Last name']);
        }

        if (! empty($testCaseData['Address'])) {
            $editAddressCommand->setAddress($testCaseData['Address']);
        }

        if (! empty($testCaseData['City'])) {
            $editAddressCommand->setCity($testCaseData['City']);
        }

        if (! empty($testCaseData['Country'])) {
            /** @var CountryByIdChoiceProvider $countryChoiceProvider */
            $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
            $countryList = $countryChoiceProvider->getChoices();
            if (! isset($countryList[$testCaseData['Country']])) {
                throw new RuntimeException(\sprintf('Cannot find country %s', $testCaseData['Country']));
            }

            $countryId = (int) $countryList[$testCaseData['Country']];
            $editAddressCommand->setCountryId($countryId);
            $stateId = $this->getStateId($countryId, $testCaseData);
            if ($stateId !== null) {
                $editAddressCommand->setStateId($stateId);
            }
        }
    }

    /**
     * @Then address :addressReference is assigned to an order :orderReference for :customerReference
     */
    public function assignAddressToOrder(string $addressReference, string $orderReference, string $customerReference): void
    {
        $customerAddressId = (int) SharedStorage::getStorage()->get($addressReference);
        $customerId = (int) SharedStorage::getStorage()->get($customerReference);

        $order = new Order();
        $order->id_address_invoice = $customerAddressId;
        $order->id_address_delivery = $customerAddressId;
        $order->id_cart = 1;
        $order->id_currency = 1;
        $order->id_customer = $customerId;
        $order->id_carrier = 1;
        $order->id_shop = 1;
        $order->id_shop_group = 1;
        $order->payment = 'Payment by check';
        $order->module = 'ps_checkpayment';
        $order->total_paid = 42;
        $order->total_paid_real = 42;
        $order->total_paid_tax_incl = 42;
        $order->total_paid_tax_excl = 42;
        $order->total_products = 42;
        $order->total_products_wt = 42;
        $order->conversion_rate = 1.0;
        if ($order->save() === false) {
            throw new RuntimeException('Cannot save order');
        }

        // Update cart addresses so that they match
        $cart = new Cart($order->id_cart);
        $cart->id_address_delivery = $customerAddressId;
        $cart->id_address_invoice = $customerAddressId;
        if ($cart->save() === false) {
            throw new RuntimeException('Cannot save cart');
        }

        SharedStorage::getStorage()->set($orderReference, $order->id);
    }

    /**
     * @Then order :orderReference should have :addressReference as a :addressType address
     */
    public function checkOrderAddress(string $orderReference, string $addressReference, string $addressType): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $cart = new Cart($order->id_cart);
        $orderAddressId = null;
        $cartAddressId = null;
        switch ($addressType) {
            case 'invoice':
                $orderAddressId = (int) $order->id_address_invoice;
                $cartAddressId = (int) $cart->id_address_invoice;
                break;
            case 'delivery':
                $orderAddressId = (int) $order->id_address_delivery;
                $cartAddressId = (int) $cart->id_address_delivery;
                break;
        }

        $expectedAddressId = (int) SharedStorage::getStorage()->get($addressReference);

        Assert::assertEquals(
            $expectedAddressId,
            $orderAddressId,
            \sprintf('Invalid order %s address, expected %s but found %s', $addressType, $expectedAddressId, $orderAddressId)
        );
        Assert::assertEquals(
            $expectedAddressId,
            $cartAddressId,
            \sprintf('Invalid cart %s address, expected %s but found %s', $addressType, $expectedAddressId, $cartAddressId)
        );
    }

    /**
     * @Then address :addressReference is assigned to a cart :cartReference for :customerReference
     */
    public function assignAddressToCart(string $addressReference, string $cartReference, string $customerReference): void
    {
        $customerAddressId = (int) SharedStorage::getStorage()->get($addressReference);
        $customerId = (int) SharedStorage::getStorage()->get($customerReference);

        // Update cart addresses so that they match
        $cart = new Cart();
        $cart->id_customer = $customerId;
        $cart->id_currency = 1;
        $cart->id_address_delivery = $customerAddressId;
        $cart->id_address_invoice = $customerAddressId;
        if ($cart->save() === false) {
            throw new RuntimeException('Cannot save cart');
        }

        SharedStorage::getStorage()->set($cartReference, $cart->id);
    }

    /**
     * @Then cart :cartReference should have :addressReference as a :addressType address
     */
    public function checkCartAddress(string $cartReference, string $addressReference, string $addressType): void
    {
        $cartId = SharedStorage::getStorage()->get($cartReference);
        $cart = new Cart($cartId);
        $cartAddressId = null;
        switch ($addressType) {
            case 'invoice':
                $cartAddressId = (int) $cart->id_address_invoice;
                break;
            case 'delivery':
                $cartAddressId = (int) $cart->id_address_delivery;
                break;
        }

        $expectedAddressId = (int) SharedStorage::getStorage()->get($addressReference);

        Assert::assertEquals(
            $expectedAddressId,
            $cartAddressId,
            \sprintf('Invalid cart %s address, expected %d but found %d', $addressType, $expectedAddressId, $cartAddressId)
        );
    }

    /**
     * @Then customer :customerReference should have address :addressReference with following details:
     */
    public function customerShouldHaveAddressWithFollowingDetails(
        string $customerReference,
        string $addressReference,
        TableNode $table): void
    {
        $testCaseData = $table->getRowsHash();
        $customerId = SharedStorage::getStorage()->get($customerReference);
        $customerAddressId = SharedStorage::getStorage()->get($addressReference);

        /** @var EditableCustomerAddress $customerAddress */
        $customerAddress = $this->getQueryBus()->handle(new GetCustomerAddressForEditing($customerAddressId));

        Assert::assertSame($customerId, $customerAddress->getCustomerId()->getValue());
        Assert::assertEquals($testCaseData['Address alias'], $customerAddress->getAddressAlias());
        Assert::assertEquals($testCaseData['First name'], $customerAddress->getFirstName());
        Assert::assertEquals($testCaseData['Last name'], $customerAddress->getLastName());
        Assert::assertEquals($testCaseData['Address'], $customerAddress->getAddress());
        Assert::assertEquals($testCaseData['City'], $customerAddress->getCity());

        /** @var CountryByIdChoiceProvider $countryChoiceProvider */
        $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $countryId = (int) $countryChoiceProvider->getChoices()[$testCaseData['Country']];
        Assert::assertSame($countryId, $customerAddress->getCountryId()->getValue());

        if (! empty($testCaseData['State'])) {
            $countryStateChoiceProvider = $this->getContainer()->get('prestashop.adapter.form.choice_provider.country_state_by_id');
            $countryStateList = $countryStateChoiceProvider->getChoices(['id_country' => $countryId]);
            if (! isset($countryStateList[$testCaseData['State']])) {
                throw new RuntimeException(\sprintf('Cannot find state %s for country %s', $testCaseData['State'], $testCaseData['Country']));
            }

            $countryStateId = $countryStateList[$testCaseData['State']];
            Assert::assertSame((int) $countryStateId, $customerAddress->getStateId()->getValue());
        } else {
            Assert::assertSame(0, $customerAddress->getStateId()->getValue());
        }

        Assert::assertEquals($testCaseData['Postal code'], $customerAddress->getPostCode());
    }

    /**
     * @Then customer :customerReference should have :addressCount addresses
     */
    public function checkCustomerAddressCount(string $customerReference, int $expectedCount): void
    {
        $customerId = SharedStorage::getStorage()->get($customerReference);

        $query = new DbQuery();
        $query->select('COUNT(a.id_address)');
        $query->from('address', 'a');
        $query->where('id_customer = ' . $customerId);
        $query->where('deleted = 0');

        $databaseCount = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        Assert::assertEquals(
            $expectedCount,
            $databaseCount,
            \sprintf('Found %s addresses for customer %s, expected %s', $databaseCount, $customerReference, $expectedCount)
        );
    }

    /**
     * @Then customer :customerReference should have :addressCount deleted addresses
     */
    public function checkCustomerDeletedAddressCount(string $customerReference, int $expectedCount): void
    {
        $customerId = SharedStorage::getStorage()->get($customerReference);

        $query = new DbQuery();
        $query->select('COUNT(a.id_address)');
        $query->from('address', 'a');
        $query->where('id_customer = ' . $customerId);
        $query->where('deleted = 1');

        $databaseCount = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        Assert::assertEquals(
            $expectedCount,
            $databaseCount,
            \sprintf('Found %s deleted addresses for customer %s, expected %s', $databaseCount, $customerReference, $expectedCount)
        );
    }

    private function mapEditableManufacturerAddress(array $testCaseData, int $addressId): EditableManufacturerAddress
    {
        /** @var ManufacturerNameByIdChoiceProvider $manufacturerProvider */
        $manufacturerProvider = $this->getContainer()->get(
            'prestashop.adapter.form.choice_provider.manufacturer_name_by_id'
        );
        $manufacturerId = $manufacturerProvider->getChoices()[$testCaseData['Brand']];

        /** @var CountryByIdChoiceProvider $countryChoiceProvider */
        $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $countryId = (int) $countryChoiceProvider->getChoices()[$testCaseData['Country']];

        $stateId = $this->getStateId($countryId, $testCaseData);

        return new EditableManufacturerAddress(
            new AddressId($addressId),
            $testCaseData['Last name'],
            $testCaseData['First name'],
            $testCaseData['Address'],
            $testCaseData['City'],
            $manufacturerId,
            $countryId,
            null,
            null,
            $stateId
        );
    }

    /**
     * @When I delete address :addressReference
     */
    public function deleteAddress(string $addressReference): void
    {
        $addressId = SharedStorage::getStorage()->get($addressReference);
        $this->getCommandBus()->handle(new DeleteAddressCommand($addressId));
    }

    /**
     * @Then brand address :addressReference does not exist
     */
    public function brandAddressDoesNotExist(string $addressReference): void
    {
        $addressId = SharedStorage::getStorage()->get($addressReference);
        try {
            $this->getQueryBus()->handle(new GetManufacturerAddressForEditing($addressId));
            throw new RuntimeException(\sprintf('Manufacturer address "%s" should not be found', $addressReference));
        } catch (AddressNotFoundException) {
        }
    }

    /**
     * @When I bulk delete addresses :addressesReferences
     */
    public function BulkDeleteAddresses(string $addressesReferences): void
    {
        $addressesReferencesArray = explode(',', $addressesReferences);
        $addressesIds = [];
        $storage = SharedStorage::getStorage();
        foreach ($addressesReferencesArray as $addressReference) {
            $addressesIds[] = $storage->get($addressReference);
        }

        $this->getCommandBus()->handle(new BulkDeleteAddressCommand($addressesIds));
    }

    /**
     * @When I edit brand address :manufacturerAddressReference with following details:
     */
    public function editBrandAddressWithFollowingDetails(string $manufacturerAddressReference, TableNode $table): void
    {
        $manufacturerAddressId = SharedStorage::getStorage()->get($manufacturerAddressReference);
        $testCaseData = $table->getRowsHash();
        /** @var EditableManufacturerAddress $manufacturerAddress */
        $manufacturerAddress = $this->mapEditableManufacturerAddress($testCaseData, $manufacturerAddressId);
        $editManufacturerAddressCommand = new EditManufacturerAddressCommand($manufacturerAddressId);
        $editManufacturerAddressCommand->setLastName($manufacturerAddress->getLastName());
        $editManufacturerAddressCommand->setFirstName($manufacturerAddress->getFirstName());
        $editManufacturerAddressCommand->setAddress($manufacturerAddress->getAddress());
        $editManufacturerAddressCommand->setCity($manufacturerAddress->getCity());
        $editManufacturerAddressCommand->setManufacturerId($manufacturerAddress->getManufacturerId());
        $editManufacturerAddressCommand->setCountryId($manufacturerAddress->getCountryId());
        $editManufacturerAddressCommand->setStateId($manufacturerAddress->getStateId());
        $this->getCommandBus()->handle($editManufacturerAddressCommand);
    }

    /**
     * @throws RuntimeException
     */
    private function getStateId(int $countryId, array $testCaseData): ?int
    {
        if (empty($testCaseData['State'])) {
            return null;
        }

        /** @var CountryStateByIdChoiceProvider $countryStateChoiceProvider */
        $countryStateChoiceProvider = $this->getContainer()->get('prestashop.adapter.form.choice_provider.country_state_by_id');
        $countryStateList = $countryStateChoiceProvider->getChoices(['id_country' => $countryId]);
        if (! isset($countryStateList[$testCaseData['State']])) {
            throw new RuntimeException(\sprintf('Cannot find state %s for country %s', $testCaseData['State'], $testCaseData['Country']));
        }

        return (int) $countryStateList[$testCaseData['State']];
    }
}
