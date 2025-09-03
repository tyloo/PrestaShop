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

namespace PrestaShop\PrestaShop\Core\Form;

use Exception;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * Complete implementation of FormHandlerInterface.
 */
class Handler implements FormHandlerInterface
{
    /**
     * @var string
     */
    public $form;

    /**
     * @param string $hookName
     * @param string $formName
     */
    public function __construct(
        /**
         * @var FormFactoryInterface the form factory
         */
        protected FormFactoryInterface $formFactory,
        /**
         * @var HookDispatcherInterface the event dispatcher
         */
        protected HookDispatcherInterface $hookDispatcher,
        /**
         * @var FormDataProviderInterface the form data provider
         */
        protected FormDataProviderInterface $formDataProvider,
        string $form,
        protected $hookName,
        protected $formName = 'form',
    ) {
        $this->form = $form;
    }

    /**
     * @throws Exception
     */
    public function getForm()
    {
        $formBuilder = $this->formFactory->createNamedBuilder($this->formName, $this->form);

        $formBuilder->setData($this->formDataProvider->getData());

        $this->hookDispatcher->dispatchWithParameters(
            \sprintf('action%sForm', $this->hookName),
            [
                'form_builder' => $formBuilder,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @throws Exception
     * @throws UndefinedOptionsException
     */
    public function save(array $data)
    {
        $errors = $this->formDataProvider->setData($data);

        $this->hookDispatcher->dispatchWithParameters(
            \sprintf('action%sSave', $this->hookName),
            [
                'errors' => &$errors,
                'form_data' => &$data,
            ]
        );

        return $errors;
    }
}
