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

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

/**
 * Class ImportRuntimeConfig defines import runtime configuration.
 */
final class ImportRuntimeConfig implements ImportRuntimeConfigInterface
{
    /**
     * @var int
     */
    private $processedRows = 0;

    /**
     * @var int request size in bytes
     */
    private $requestSize;

    /**
     * @var int post size limit in bytes
     */
    private $postSizeLimit;

    /**
     * @var int total number of rows to be imported
     */
    private $totalNumberOfRows;

    /**
     * @var array
     */
    private $notices;

    /**
     * @var array
     */
    private $warnings;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param bool $shouldValidateData
     * @param int  $offset
     * @param int  $limit
     */
    public function __construct(
        private $shouldValidateData,
        private $offset,
        private $limit,
        private array $sharedData,
        /**
         * @var array import entity fields mapping
         */
        private readonly array $entityFields,
    ) {
    }

    public function shouldValidateData()
    {
        return $this->shouldValidateData;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getEntityFields()
    {
        return $this->entityFields;
    }

    public function getSharedData()
    {
        return $this->sharedData;
    }

    public function addSharedDataItem($key, $value): void
    {
        $this->sharedData[$key] = $value;
    }

    public function isFinished(): bool
    {
        return $this->processedRows < $this->limit;
    }

    public function setNumberOfProcessedRows($number): void
    {
        $this->processedRows = $number;
    }

    public function getNumberOfProcessedRows()
    {
        return $this->processedRows;
    }

    public function setRequestSizeInBytes($size): void
    {
        $this->requestSize = $size;
    }

    public function setPostSizeLimitInBytes($size): void
    {
        $this->postSizeLimit = $size;
    }

    public function setTotalNumberOfRows($number): void
    {
        $this->totalNumberOfRows = $number;
    }

    public function setNotices(array $notices): void
    {
        $this->notices = $notices;
    }

    public function setWarnings(array $warnings): void
    {
        $this->warnings = $warnings;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function toArray(): array
    {
        return [
            'crossStepsVariables' => $this->sharedData,
            'doneCount' => $this->processedRows + $this->offset,
            'isFinished' => $this->isFinished(),
            'nextPostSize' => $this->requestSize,
            'postSizeLimit' => $this->postSizeLimit,
            'totalCount' => $this->totalNumberOfRows,
            'notices' => $this->notices,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
        ];
    }
}
