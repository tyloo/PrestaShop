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

namespace PrestaShop\PrestaShop\Core\Module;

use Db;
use Exception;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use Shop;

class HookRepository
{
    private $db_prefix;

    public function __construct(
        private readonly HookInformationProvider $hookInfo,
        private readonly Shop $shop,
        private readonly Db $db,
    ) {
        $this->db_prefix = $this->db->getPrefix();
    }

    public function getIdByName($hook_name): int
    {
        $escaped_hook_name = $this->db->escape($hook_name);

        $id_hook = $this->db->getValue(
            \sprintf("SELECT id_hook FROM %shook WHERE name = '%s'", $this->db_prefix, $escaped_hook_name)
        );

        return (int) $id_hook;
    }

    /**
     * Creates a new hook if not already existing and returns the hook id.
     *
     * @param string $hook_name   The name of the hook
     * @param string $title       The title for the hook
     * @param string $description The description for the hook
     * @param int    $position    if the modules in the hook can be positioned
     *
     * @return int Hook ID
     */
    public function createHook($hook_name, $title = '', $description = '', $position = 1)
    {
        $id_hook = $this->getIdByName($hook_name);
        if ($id_hook > 0) {
            return $id_hook;
        }

        $this->db->insert('hook', [
            'name' => $this->db->escape($hook_name),
            'title' => $this->db->escape($title),
            'description' => $this->db->escape($description),
            'position' => $this->db->escape($position),
        ], false, true, Db::INSERT);

        return $this->db->Insert_ID();
    }

    private function getIdModule($module_name): int
    {
        $escaped_module_name = $this->db->escape($module_name);

        $id_module = $this->db->getValue(
            \sprintf("SELECT id_module FROM %smodule WHERE name = '%s'", $this->db_prefix, $escaped_module_name)
        );

        return (int) $id_module;
    }

    public function unHookModulesFromHook($hook_name)
    {
        $id_hook = $this->getIdByName($hook_name);
        $id_shop = (int) $this->shop->id;

        $this->db->execute("DELETE FROM {$this->db_prefix}hook_module
             WHERE id_hook = {$id_hook} AND id_shop = {$id_shop}
        ");

        $this->db->execute("DELETE FROM {$this->db_prefix}hook_module_exceptions
            WHERE id_hook = {$id_hook} AND id_shop = {$id_shop}
        ");

        return $this;
    }

    /**
     * Saves hook settings for a list of hooks.
     * The $hooks array should have this format:
     * [
     *     "hookName" => [
     *         "module1",
     *         "module2",
     *         "module3" => [
     *             "except_pages" => [
     *                 "page1",
     *                 "page2",
     *                 "page3"
     *             ]
     *         ]
     *     ]
     * ]
     * Only hooks present as keys in the $hooks array are affected and all changes
     * are only done for the shop this Repository belongs to.
     */
    public function persistHooksConfiguration(array $hooks)
    {
        foreach ($hooks as $hook_name => $module_names) {
            $id_hook = $this->getIdByName($hook_name);
            if ($id_hook === 0) {
                $id_hook = $this->createHook($hook_name);
            }

            if (! $id_hook) {
                throw new Exception(\sprintf('Could not create hook `%1$s`.', $hook_name));
            }

            $this->unHookModulesFromHook($hook_name);

            $position = 0;
            foreach ($module_names as $module) {
                if (\is_array($module)) {
                    $module_name = key($module);
                    $extra_data = current($module);
                } else {
                    $module_name = $module;
                    $extra_data = [];
                }

                ++$position;
                $id_module = $this->getIdModule($module_name);
                if ($id_module === 0) {
                    continue;
                }

                $row = [
                    'id_module' => $id_module,
                    'id_shop' => (int) $this->shop->id,
                    'id_hook' => $id_hook,
                    'position' => $position,
                ];

                $this->db->insert('hook_module', $row);

                if (! empty($extra_data['except_pages'])) {
                    $this->setModuleHookExceptions(
                        $id_module,
                        $id_hook,
                        $extra_data['except_pages']
                    );
                }
            }
        }

        return $this;
    }

    private function setModuleHookExceptions($id_module, $id_hook, array $pages)
    {
        $id_shop = (int) $this->shop->id;
        $id_module = (int) $id_module;
        $id_hook = (int) $id_hook;

        $this->db->execute("DELETE FROM {$this->db_prefix}hook_module_exceptions
            WHERE id_shop = {$id_shop}
            AND id_module = {$id_module}
            AND id_hook = {$id_hook}
        ");

        foreach ($pages as $page) {
            $this->db->insert('hook_module_exceptions', [
                'id_shop' => $id_shop,
                'id_module' => $id_module,
                'id_hook' => $id_hook,
                'file_name' => $page,
            ]);
        }

        return $this;
    }

    private function getModuleHookExceptions($id_module, $id_hook): array
    {
        $id_shop = (int) $this->shop->id;
        $id_module = (int) $id_module;
        $id_hook = (int) $id_hook;

        $rows = $this->db->executeS("SELECT file_name
            FROM {$this->db_prefix}hook_module_exceptions
            WHERE id_shop = {$id_shop}
            AND id_module = {$id_module}
            AND id_hook = {$id_hook}
            ORDER BY file_name ASC
        ");

        return array_map(fn ($row) => $row['file_name'], $rows);
    }

    /**
     * @return non-empty-array[]
     */
    public function getHooksWithModules(): array
    {
        $id_shop = (int) $this->shop->id;

        $sql = "SELECT h.name as hook_name, h.id_hook, m.name as module_name, m.id_module
            FROM {$this->db_prefix}hook_module hm
            INNER JOIN {$this->db_prefix}hook h
                ON h.id_hook = hm.id_hook
            INNER JOIN {$this->db_prefix}module m
                ON m.id_module = hm.id_module
            WHERE hm.id_shop = {$id_shop}
            ORDER BY h.name ASC, hm.position ASC
        ";

        $rows = $this->db->executeS($sql);

        $hooks = [];

        foreach ($rows as $row) {
            $exceptions = $this->getModuleHookExceptions(
                $row['id_module'],
                $row['id_hook']
            );

            if ($exceptions === []) {
                $hooks[$row['hook_name']][] = $row['module_name'];
            } else {
                $hooks[$row['hook_name']][$row['module_name']] = [
                    'except_pages' => $exceptions,
                ];
            }
        }

        return $hooks;
    }

    /**
     * @return mixed[]
     */
    public function getDisplayHooksWithModules(): array
    {
        $hooks = [];
        foreach ($this->getHooksWithModules() as $hook_name => $modules) {
            if ($this->hookInfo->isDisplayHookName($hook_name)) {
                $hooks[$hook_name] = $modules;
            }
        }

        return $hooks;
    }
}
