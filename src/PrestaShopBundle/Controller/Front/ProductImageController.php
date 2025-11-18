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

namespace PrestaShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class ProductImageController extends AbstractController
{
    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $projectDir,
    ) {
    }

    #[Route('/{idImage}-{imageType}/{friendlyName}.{extension}', name: 'product_image', requirements: ['idImage' => '\d+', 'extension' => 'jpe?g|webp|png|avif'])]
    #[Route('/{idImage}/{friendlyName}.{extension}', name: 'product_image_no_type', requirements: ['idImage' => '\d+', 'extension' => 'jpe?g|webp|png|avif'])]
    public function __invoke(int $idImage, string $extension, ?string $imageType = null): Response
    {
        $idPath = implode('/', str_split((string) $idImage));
        $filename = $imageType ? "$idImage-$imageType.$extension" : "$idImage.$extension";
        $productImage = new File("$this->projectDir/img/p/$idPath/$filename");

        return $this->file($productImage, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
