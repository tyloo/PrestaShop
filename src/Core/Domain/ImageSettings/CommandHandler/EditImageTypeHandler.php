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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShopBundle\Entity\ImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

#[AsCommandHandler]
final class EditImageTypeHandler extends AbstractObjectModelHandler implements EditImageTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository,
    ) {
    }

    /**
     * @throws ImageTypeException
     */
    public function handle(EditImageTypeCommand $command): void
    {
        /** @var ImageType $imageType */
        $imageType = $this->imageTypeRepository->find($command->getImageTypeId()->getValue());

        if ($imageType->getId() === null) {
            throw new ImageTypeNotFoundException(\sprintf('Image type with id "%d" was not found', $command->getImageTypeId()->getValue()));
        }

        if ($command->getName() !== null) {
            $imageType->setName($command->getName());
        }

        if ($command->getWidth() !== null) {
            $imageType->setWidth($command->getWidth());
        }

        if ($command->getHeight() !== null) {
            $imageType->setHeight($command->getHeight());
        }

        if ($command->isProducts() !== null) {
            $imageType->setProducts($command->isProducts());
        }

        if ($command->isCategories() !== null) {
            $imageType->setCategories($command->isCategories());
        }

        if ($command->isManufacturers() !== null) {
            $imageType->setManufacturers($command->isManufacturers());
        }

        if ($command->isSuppliers() !== null) {
            $imageType->setSuppliers($command->isSuppliers());
        }

        if ($command->isStores() !== null) {
            $imageType->setStores($command->isStores());
        }

        $this->imageTypeRepository->save($imageType);
    }
}
