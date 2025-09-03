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

namespace PrestaShop\PrestaShop\Core\Hook;

use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher as HookDispatcherAdapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HookDispatcher is responsible for dispatching hooks.
 */
final class HookDispatcher implements HookDispatcherInterface
{
    public function __construct(
        private readonly HookDispatcherAdapter $hookDispatcherAdapter,
    ) {
    }

    public function dispatchHook(HookInterface $hook)
    {
        $this->hookDispatcherAdapter->dispatchForParameters(
            $hook->getName(),
            $hook->getParameters()
        );
    }

    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
        $this->dispatchHook(new Hook($hookName, $hookParameters));
    }

    public function dispatchRendering(HookInterface $hook)
    {
        $event = $this->hookDispatcherAdapter->renderForParameters(
            $hook->getName(),
            $hook->getParameters()
        );

        $content = $event->getContent();
        array_walk($content, function (&$partialContent): void {
            $partialContent = empty($partialContent) ? '' : current($partialContent);
        });

        return new RenderedHook($hook, $content);
    }

    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
        return $this->dispatchRendering(new Hook($hookName, $hookParameters));
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        return $this->hookDispatcherAdapter->dispatch($event, $eventName);
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->hookDispatcherAdapter->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->hookDispatcherAdapter->addSubscriber($subscriber);
    }

    public function removeListener($eventName, $listener)
    {
        $this->hookDispatcherAdapter->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->hookDispatcherAdapter->removeSubscriber($subscriber);
    }

    public function getListeners($eventName = null)
    {
        return $this->hookDispatcherAdapter->getListeners($eventName);
    }

    public function getListenerPriority($eventName, $listener)
    {
        return $this->hookDispatcherAdapter->getListenerPriority($eventName, $listener);
    }

    public function hasListeners($eventName = null)
    {
        return $this->hookDispatcherAdapter->hasListeners($eventName);
    }
}
