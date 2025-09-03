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

namespace PrestaShopBundle\Routing\Converter;

/**
 * Class LegacyRoute contains the info about a route, its legacyLinks, legacyParameters
 * and controller actions hash map. This class can be built simply based on the routeDefaults
 * parameters and its name.
 */
class LegacyRoute
{
    private readonly array $legacyLinks;

    private readonly array $controllersActions;

    /**
     * @param string $routeName
     */
    public function __construct(
        private $routeName,
        array $legacyLinks,
        private readonly array $routeParameters,
    ) {
        $this->legacyLinks = $this->buildLegacyLinks($legacyLinks);
        $this->controllersActions = $this->buildControllerActions($this->legacyLinks, $this->routeName);
    }

    /**
     * @param string|null $action
     */
    public static function isIndexAction($action): bool
    {
        $indexAliases = ['list', 'index'];

        return empty($action) || \in_array(mb_strtolower($action), $indexAliases, true);
    }

    /**
     * @param string $routeName
     */
    public static function buildLegacyRoute($routeName, array $routeDefaults): static
    {
        $legacyLinks = $routeDefaults['_legacy_link'];
        if (! \is_array($legacyLinks)) {
            $legacyLinks = [$legacyLinks];
        }

        $legacyParameters = [];
        if (! empty($routeDefaults['_legacy_parameters']) && \is_array($routeDefaults['_legacy_parameters'])) {
            $legacyParameters = $routeDefaults['_legacy_parameters'];
        }

        return new static($routeName, $legacyLinks, $legacyParameters);
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getLegacyLinks(): array
    {
        return $this->legacyLinks;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function getControllersActions(): array
    {
        return $this->controllersActions;
    }

    private function buildLegacyLinks(array $legacyLinks): array
    {
        $brokenLegacyLinks = [];
        foreach ($legacyLinks as $legacyLink) {
            $linkParts = explode(':', (string) $legacyLink);
            $legacyController = $linkParts[0];
            $legacyAction = $linkParts[1] ?? null;
            $brokenLegacyLinks[] = [
                'controller' => $legacyController,
                'action' => $legacyAction,
            ];
        }

        return $brokenLegacyLinks;
    }

    /**
     * @param string $routeName
     */
    private function buildControllerActions(array $legacyLinks, $routeName): array
    {
        $controllersActions = [];
        foreach ($legacyLinks as $legacyLink) {
            $controller = $legacyLink['controller'];
            if (! isset($controllersActions[$controller])) {
                $controllersActions[$controller] = [];
            }

            $action = self::isIndexAction($legacyLink['action']) ? 'index' : $legacyLink['action'];
            $controllersActions[$controller][$action] = $routeName;
        }

        return $controllersActions;
    }
}
