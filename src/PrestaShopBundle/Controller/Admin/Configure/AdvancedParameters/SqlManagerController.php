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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\BulkDeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use PrestaShop\PrestaShop\Core\Export\Exception\FileWritingException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\RequestSqlFilters;
use PrestaShop\PrestaShop\Core\SqlManager\Exporter\SqlRequestExporter;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Responsible of "Configure > Advanced Parameters > Database -> SQL Manager" page.
 */
class SqlManagerController extends PrestaShopAdminController
{
    #[Route(
        path: '/configure/advanced-parameters/sql-requests',
        name: 'admin_sql_requests_index',
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql',
        ],
        methods: 'GET',
    )]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        RequestSqlFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.request_sql')]
        GridFactoryInterface $gridLogFactory,
        #[Autowire(service: 'prestashop.admin.request_sql_settings.form_handler')]
        ConfigurationFormHandlerInterface $settingsFormHandler,
    ): Response {
        // handle "Export to SQL manager" action from legacy pages
        if ($request->query->has('addrequest_sql')) {
            return $this->forward('PrestaShopBundle:Admin\Configure\AdvancedParameters\RequestSql:create');
        }

        $grid = $gridLogFactory->getGrid($filters);
        $settingsForm = $settingsFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/index.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_sql_requests_create'),
                    'desc' => $this->trans('Add new SQL query', [], 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'layoutTitle' => $this->trans('SQL manager', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlSettingsForm' => $settingsForm->createView(),
            'requestSqlGrid' => $this->presentGrid($grid),
        ]);
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/process-settings',
        name: 'admin_sql_requests_process_settings',
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:update',
        ],
        methods: 'POST',
    )]
    #[DemoRestricted(redirectRoute: 'admin_sql_requests_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_sql_requests_index')]
    public function processFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.request_sql_settings.form_handler')]
        ConfigurationFormHandlerInterface $settingsFormHandler,
    ): RedirectResponse {
        $settingForm = $settingsFormHandler->getForm();
        $settingForm->handleRequest($request);

        if ($settingForm->isSubmitted()) {
            if (!$errors = $settingsFormHandler->save($settingForm->getData())) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_sql_requests_index');
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/new',
        name: 'admin_sql_requests_create',
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:addrequest_sql',
        ],
        methods: ['GET', 'POST'],
    )]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'You do not have permission to create this.', redirectRoute: 'admin_sql_requests_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.sql_request_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.sql_request_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response|RedirectResponse {
        $data = $this->getSqlRequestDataFromRequest($request);

        $sqlRequestForm = $formBuilder->getForm($data);
        $sqlRequestForm->handleRequest($request);

        try {
            $result = $formHandler->handle($sqlRequestForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_requests_index');
            }
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/create.html.twig', [
            'layoutTitle' => $this->trans('New SQL query', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlForm' => $sqlRequestForm->createView(),
            'dbTableNames' => $this->getDatabaseTables(),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
        ]);
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/{sqlRequestId}/edit',
        name: 'admin_sql_requests_edit',
        requirements: ['sqlRequestId' => '\d+'],
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:updaterequest_sql',
            '_legacy_parameters' => ['id_request_sql' => 'sqlRequestId'],
        ],
        methods: ['GET', 'POST'],
    )]
    #[DemoRestricted(redirectRoute: 'admin_sql_requests_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_sql_requests_index')]
    public function editAction(
        int $sqlRequestId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.sql_request_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.sql_request_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response|RedirectResponse {
        $sqlRequestForm = $formBuilder->getFormFor($sqlRequestId);
        $sqlRequestForm->handleRequest($request);

        try {
            $result = $formHandler->handleFor($sqlRequestId, $sqlRequestForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_requests_index');
            }
        } catch (SqlRequestNotFoundException) {
            $this->addFlash(
                'error',
                $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_sql_requests_index');
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/edit.html.twig', [
            'layoutTitle' => $this->trans('Editing SQL query %query%', ['%query%' => $sqlRequestForm->getData()['name']], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlForm' => $sqlRequestForm->createView(),
            'dbTableNames' => $this->getDatabaseTables(),
        ]);
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/{sqlRequestId}/delete',
        name: 'admin_sql_requests_delete',
        requirements: ['sqlRequestId' => '\d+'],
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:deleterequest_sql',
            '_legacy_parameters' => ['id_request_sql' => 'sqlRequestId'],
        ],
        methods: ['GET', 'DELETE'],
    )]
    #[DemoRestricted(redirectRoute: 'admin_sql_requests_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.', redirectRoute: 'admin_sql_requests_index')]
    public function deleteAction(int $sqlRequestId): RedirectResponse
    {
        try {
            $deleteSqlRequestCommand = new DeleteSqlRequestCommand(
                new SqlRequestId($sqlRequestId)
            );

            $this->dispatchCommand($deleteSqlRequestCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->redirectToRoute('admin_sql_requests_index');
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/delete-bulk',
        name: 'admin_sql_requests_delete_bulk',
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:submitBulkdeleterequest_sql',
        ],
        methods: 'POST',
    )]
    #[DemoRestricted(redirectRoute: 'admin_sql_requests_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.', redirectRoute: 'admin_sql_requests_index')]
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        try {
            $requestSqlIds = $this->getBulkSqlRequestFromRequest($request);
            $bulkDeleteSqlRequestCommand = new BulkDeleteSqlRequestCommand($requestSqlIds);

            $this->dispatchCommand($bulkDeleteSqlRequestCommand);

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->redirectToRoute('admin_sql_requests_index');
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/{sqlRequestId}/view',
        name: 'admin_sql_requests_view',
        requirements: ['sqlRequestId' => '\d+'],
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:viewsql_request',
            '_legacy_parameters' => ['id_request_sql' => 'sqlRequestId'],
        ],
        methods: 'GET',
    )]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'You do not have permission to view this.', redirectRoute: 'admin_sql_requests_index')]
    public function viewAction(Request $request, int $sqlRequestId): Response
    {
        try {
            $query = new GetSqlRequestExecutionResult($sqlRequestId);

            $sqlRequestExecutionResult = $this->dispatchQuery($query);
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleViewException($e));

            return $this->redirectToRoute('admin_sql_requests_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/view.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Result of SQL query', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'sqlRequestResult' => $sqlRequestExecutionResult,
        ]);
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/{sqlRequestId}/export',
        name: 'admin_sql_requests_export',
        requirements: ['sqlRequestId' => '\d+'],
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:exportsql_request',
            '_legacy_parameters' => ['id_request_sql' => 'sqlRequestId'],
        ],
        methods: 'GET',
    )]
    #[DemoRestricted(redirectRoute: 'admin_sql_requests_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_sql_requests_index')]
    public function exportAction(
        int $sqlRequestId,
        SqlRequestExporter $sqlRequestExporter,
    ): RedirectResponse|BinaryFileResponse {
        try {
            $query = new GetSqlRequestExecutionResult($sqlRequestId);
            /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
            $sqlRequestExecutionResult = $this->dispatchQuery($query);

            $exportedFile = $sqlRequestExporter->exportToFile(
                $query->getSqlRequestId(),
                $sqlRequestExecutionResult
            );

            /** @var SqlRequestSettings $sqlRequestSettings */
            $sqlRequestSettings = $this->dispatchQuery(new GetSqlRequestSettings());
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleExportException($e));

            return $this->redirectToRoute('admin_sql_requests_index');
        }

        $stream = new Stream($exportedFile->getPathname());
        $response = new BinaryFileResponse($stream);
        $response->setCharset($sqlRequestSettings->getFileEncoding());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $exportedFile->getFilename());

        return $response;
    }

    #[Route(
        path: '/configure/advanced-parameters/sql-requests/tables/{mySqlTableName}/columns',
        name: 'admin_sql_requests_table_columns',
        defaults: [
            '_legacy_controller' => 'AdminRequestSql',
            '_legacy_link' => 'AdminRequestSql:ajax',
            '_legacy_parameters' => ['table' => 'mySqlTableName'],
        ],
        methods: 'GET',
    )]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_sql_requests_index')]
    public function ajaxTableColumnsAction(string $mySqlTableName): JsonResponse
    {
        $query = new GetDatabaseTableFieldsList($mySqlTableName);
        /** @var DatabaseTableFields $databaseFields */
        $databaseFields = $this->dispatchQuery($query);

        return $this->json(['columns' => $databaseFields->getFields()]);
    }

    /**
     * When "Export to SQL Manager" feature is used,
     * it adds "name" and "sql" to request's POST data
     * which is used as default form data
     * when creating SqlRequest.
     */
    protected function getSqlRequestDataFromRequest(Request $request): array
    {
        if ($request->request->has('sql') || $request->request->has('name')) {
            return [
                'sql' => $request->request->get('sql'),
                'name' => $request->request->get('name'),
            ];
        }

        return [];
    }

    protected function handleException(SqlRequestException $e): string
    {
        $code = $e->getCode();
        $type = $e::class;

        $exceptionMessages = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            SqlRequestConstraintException::class => $e->getMessage(),
            SqlRequestException::class => $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error'),
        ];

        $deleteExceptionMessages = [
            CannotDeleteSqlRequestException::CANNOT_SINGLE_DELETE => $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error'),
            CannotDeleteSqlRequestException::CANNOT_BULK_DELETE => $this->trans('An error occurred while deleting this selection.', [], 'Admin.Notifications.Error'),
        ];

        if (CannotDeleteSqlRequestException::class === $type
            && isset($deleteExceptionMessages[$code])
        ) {
            return $deleteExceptionMessages[$code];
        }

        if (isset($exceptionMessages[$type])) {
            return $exceptionMessages[$type];
        }

        return $this->getErrorMessageForException($e);
    }

    protected function handleViewException(SqlRequestException $e): string
    {
        $type = $e::class;

        $exceptionMessages = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
        ];

        if (isset($exceptionMessages[$type])) {
            return $exceptionMessages[$type];
        }

        return $this->getErrorMessageForException($e);
    }

    protected function handleExportException(Exception $e): string
    {
        $type = $e::class;

        if ($e instanceof FileWritingException) {
            return $this->handleApplicationExportException($e);
        }

        if ($e instanceof SqlRequestException) {
            return $this->handleDomainExportException($e);
        }

        return $this->getErrorMessageForException($e);
    }

    protected function handleApplicationExportException(FileWritingException $e): string
    {
        $code = $e->getCode();

        $applicationErrors = [
            FileWritingException::CANNOT_OPEN_FILE_FOR_WRITING => $this->trans('Cannot open export file for writing', [], 'Admin.Notifications.Error'),
        ];

        if (isset($applicationErrors[$code])) {
            return $applicationErrors[$code];
        }

        return $this->getErrorMessageForException($e);
    }

    protected function handleDomainExportException(SqlRequestException $e): string
    {
        $type = $e::class;

        $domainErrors = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
        ];

        if (isset($domainErrors[$type])) {
            return $domainErrors[$type];
        }

        return $this->getErrorMessageForException($e);
    }

    /**
     * @return string[] Array of database tables
     */
    protected function getDatabaseTables(): array
    {
        /** @var DatabaseTablesList $databaseTablesList */
        $databaseTablesList = $this->dispatchQuery(new GetDatabaseTablesList());

        return $databaseTablesList->getTables();
    }

    /**
     * Get SQL Request IDs from request for bulk actions.
     *
     * @return int[]
     */
    protected function getBulkSqlRequestFromRequest(Request $request): array
    {
        $sqlRequestIds = $request->request->all('sql_request_bulk');

        return array_map('intval', $sqlRequestIds);
    }
}
