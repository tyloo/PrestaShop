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

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * This class is an adapter if can use PrestaShopLoggerInterface and decorate it into a PSR logger.
 */
class PSRLoggerAdapter implements LoggerInterface
{
    public function __construct(
        private readonly PrestaShopLoggerInterface $logger,
    ) {
    }

    public function emergency($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function alert($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function critical($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function error($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function warning($message, array $context = []): void
    {
        $this->logger->logWarning($message);
    }

    public function notice($message, array $context = []): void
    {
        $this->logger->logInfo($message);
    }

    public function info($message, array $context = []): void
    {
        $this->logger->logInfo($message);
    }

    public function debug($message, array $context = []): void
    {
        $this->logger->logDebug($message);
    }

    public function log($level, $message, array $context = []): void
    {
        $legacyLevel = match ($level) {
            LogLevel::EMERGENCY, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::ERROR => PrestaShopLoggerInterface::ERROR,
            LogLevel::WARNING => PrestaShopLoggerInterface::WARNING,
            LogLevel::NOTICE, LogLevel::INFO => PrestaShopLoggerInterface::INFO,
            default => PrestaShopLoggerInterface::DEBUG,
        };
        $this->logger->log($message, $legacyLevel);
    }
}
