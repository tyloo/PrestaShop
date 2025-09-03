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

namespace PrestaShopBundle\Security\Attribute;

use Attribute;

/**
 * Forbid access to the page if Demonstration mode is enabled.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class DemoRestricted
{
    public function __construct(
        /**
         * The route for the redirection.
         */
        private ?string $redirectRoute = null,
        /**
         * The message of the exception.
         */
        private string $message = 'This functionality has been disabled.',
        /**
         * The translation domain for the message.
         */
        private string $domain = 'Admin.Notifications.Error',
        /**
         * The route params which are used together to generate the redirect route.
         */
        private array $redirectQueryParamsToKeep = [],
    ) {
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain the translation domain name
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message the message displayed after redirection
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getRedirectRoute(): ?string
    {
        return $this->redirectRoute;
    }

    /**
     * @param string $redirectRoute the route used for redirection
     */
    public function setRedirectRoute(?string $redirectRoute): void
    {
        $this->redirectRoute = $redirectRoute;
    }

    /**
     * Returns the alias name for an annotated configuration.
     */
    public function getAliasName(): string
    {
        return 'demo_restricted';
    }

    /**
     * Returns whether multiple annotations of this type are allowed.
     */
    public function allowArray(): bool
    {
        return true;
    }

    public function getRedirectQueryParamsToKeep(): array
    {
        return $this->redirectQueryParamsToKeep;
    }

    public function setRedirectQueryParamsToKeep(array $redirectQueryParamsToKeep): void
    {
        $this->redirectQueryParamsToKeep = $redirectQueryParamsToKeep;
    }
}
