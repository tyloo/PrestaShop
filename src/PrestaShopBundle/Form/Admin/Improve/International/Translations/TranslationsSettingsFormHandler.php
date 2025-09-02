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

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class TranslationsSettingsFormHandler implements FormHandlerInterface
{
    /**
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $form
     * @param string $hookName
     */
    public function __construct(protected FormFactoryInterface $formFactory, protected HookDispatcherInterface $hookDispatcher, protected string $form, protected string $hookName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $formBuilder = $this->formFactory->createNamedBuilder('form', $this->form);

        $this->hookDispatcher->dispatchWithParameters(
            "action{$this->hookName}Form",
            [
                'form_builder' => $formBuilder,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data): array
    {
        // Translations forms do not save data
        return [];
    }
}
