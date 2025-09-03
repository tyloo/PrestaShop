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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Exception;
use Mail;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPreviewVariablesBuilder;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailTemplateTwigRenderer;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\FolderThemeCatalog;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\MailVariablesTransformation;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\GenerateMailsType;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\TranslateMailsBodyType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Service\TranslationService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MailThemeController manages mail theme generation, you can define the shop
 * mail theme, and regenerate mail in a specific language.
 *
 * Accessible via "Design > Mail Theme"
 */
class MailThemeController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            ThemeCatalogInterface::class => FolderThemeCatalog::class,
            MailPreviewVariablesBuilder::class => MailPreviewVariablesBuilder::class,
            LanguageRepositoryInterface::class => LanguageRepositoryInterface::class,
            MailTemplateRendererInterface::class => MailTemplateTwigRenderer::class,
        ];
    }

    /**
     * Show mail theme settings and generation page.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.mail_theme.form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');
        $generateThemeMailsForm = $this->createForm(GenerateMailsType::class);
        $translateMailsBodyForm = $this->createForm(TranslateMailsBodyType::class);
        $mailThemes = $this->container->get(ThemeCatalogInterface::class)->listThemes();

        return $this->render('@PrestaShop/Admin/Improve/Design/MailTheme/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Email theme', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'mailThemeConfigurationForm' => $formHandler->getForm(),
            'generateMailsForm' => $generateThemeMailsForm,
            'translateMailsBodyForm' => $translateMailsBodyForm,
            'mailThemes' => $mailThemes,
        ]);
    }

    /**
     * Manage generation form post and generate mails.
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function generateMailsAction(Request $request): Response
    {
        $generateThemeMailsForm = $this->createForm(GenerateMailsType::class);
        $generateThemeMailsForm->handleRequest($request);

        if ($generateThemeMailsForm->isSubmitted()) {
            if (! $generateThemeMailsForm->isValid()) {
                $this->addFlashFormErrors($generateThemeMailsForm);

                return $this->redirectToRoute('admin_mail_theme_index');
            }

            $data = $generateThemeMailsForm->getData();
            try {
                $coreMailsFolder = '';
                $modulesMailFolder = '';
                // Overwrite theme folder if selected
                if (! empty($data['theme'])) {
                    if (is_dir($data['theme'] . '/mails')) {
                        $coreMailsFolder = $data['theme'] . '/mails';
                    }

                    if (is_dir($data['theme'] . '/modules')) {
                        $modulesMailFolder = $data['theme'] . '/modules';
                    }
                }

                $generateCommand = new GenerateThemeMailTemplatesCommand(
                    $data['mailTheme'],
                    $data['language'],
                    $data['overwrite'],
                    $coreMailsFolder,
                    $modulesMailFolder
                );

                $this->dispatchCommand($generateCommand);

                if ($data['overwrite']) {
                    $this->addFlash(
                        'success',
                        $this->trans(
                            'Successfully overwrote email templates for theme %s with locale %s',
                            [
                                $data['mailTheme'],
                                $data['language'],
                            ],
                            'Admin.Notifications.Success'
                        )
                    );
                } else {
                    $this->addFlash(
                        'success',
                        $this->trans(
                            'Successfully generated email templates for theme %s with locale %s',
                            [
                                $data['mailTheme'],
                                $data['language'],
                            ],
                            'Admin.Notifications.Success'
                        )
                    );
                }
            } catch (CoreException $e) {
                $this->addFlashErrors([
                    $this->trans(
                        \sprintf(
                            'Cannot generate email templates for theme %s with locale %s',
                            $data['mailTheme'],
                            $data['language']
                        ),
                        [],
                        'Admin.Notifications.Error'
                    ),
                    $e->getMessage(),
                ]);
            }
        }

        return $this->redirectToRoute('admin_mail_theme_index');
    }

    /**
     * Save mail theme configuration
     *
     * @throws Exception
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_mail_theme_index')]
    public function saveConfigurationAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.mail_theme.form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        /** @var Form $form */
        $form = $formHandler->getForm()->handleRequest($request);

        if ($form->isSubmitted()) {
            if (! $form->isValid()) {
                $this->addFlashFormErrors($form);

                return $this->redirectToRoute('admin_mail_theme_index');
            }

            $errors = $formHandler->save($form->getData());
            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans(
                        'Email theme configuration saved successfully',
                        [],
                        'Admin.Notifications.Success'
                    )
                );
            } else {
                $this->addFlashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_mail_theme_index');
    }

    /**
     * Preview the list of layouts for a defined theme
     *
     * @throws InvalidArgumentException
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function previewThemeAction(Request $request, string $theme): Response
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $mailTheme = $this->container->get(ThemeCatalogInterface::class)->getByName($theme);

        return $this->render('@PrestaShop/Admin/Improve/Design/MailTheme/preview.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Previewing theme %s', [$mailTheme->getName()], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'mailTheme' => $mailTheme,
        ]);
    }

    /**
     * This action allows to send a test mail of a specific email template, however the Mail
     * class used to send emails is not modular enough to allow sending templates on the fly.
     * This would require either:
     *  - a little modification of the Mail class to add an easy way to send a template content (rather than its name)
     *  - a full refacto of the Mail class which wouldn't be coupled to static files any more
     *
     * These modifications will be performed in a future release so for now we can only send test emails
     * with the current email theme using generated static files.
     *
     * @throws InvalidArgumentException
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function sendTestMailAction(string $theme, string $layout, string $locale, string $module = ''): Response
    {
        if ($this->getConfiguration()->get('PS_MAIL_THEME') !== $theme) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot send test email for theme %theme% because it is not your current theme',
                    [
                        '%theme%' => $theme,
                    ],
                    'Admin.Notifications.Error'
                )
            );

            return $this->redirectToRoute('admin_mail_theme_preview', ['theme' => $theme]);
        }

        $employeeData = $this->getEmployeeContext()->getEmployee();

        $language = $this->container->get(LanguageRepositoryInterface::class)->getOneByLocaleOrIsoCode($locale);
        if ($language === null) {
            throw new InvalidArgumentException(\sprintf('Cannot find Language with locale or isoCode %s', $locale));
        }

        $templatePath = $module === '' || $module === '0' ? _PS_MAIL_DIR_ : _PS_MODULE_DIR_ . $module . '/mails/';

        $mailLayout = $this->getMailLayout($theme, $layout, $module);
        $mailVariables = $this->container->get(MailPreviewVariablesBuilder::class)->buildTemplateVariables($mailLayout);

        $mailSent = Mail::send(
            $language->getId(),
            $layout,
            $this->trans('Test email %template%', ['%template%' => $layout], 'Admin.Design.Feature'),
            $mailVariables,
            $employeeData->getEmail(),
            $employeeData->getFirstName() . ' ' . $employeeData->getLastName(),
            $employeeData->getEmail(),
            $employeeData->getFirstName() . ' ' . $employeeData->getLastName(),
            null,
            null,
            $templatePath
        );

        if ($mailSent) {
            $this->addFlash(
                'success',
                $this->trans(
                    'Test email for layout %layout% was successfully sent to %email%',
                    [
                        '%layout%' => $layout,
                        '%email%' => $employeeData->getEmail(),
                    ],
                    'Admin.Notifications.Success'
                )
            );
        } else {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot send test email for layout %layout%',
                    [
                        '%layout%' => $layout,
                    ],
                    'Admin.Notifications.Error'
                )
            );
        }

        return $this->redirectToRoute('admin_mail_theme_preview', ['theme' => $theme]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.')]
    public function translateBodyAction(
        Request $request,
        #[Autowire(service: 'prestashop.service.translation')]
        TranslationService $translationService,
    ): RedirectResponse {
        $translateMailsBodyForm = $this->createForm(TranslateMailsBodyType::class);
        $translateMailsBodyForm->handleRequest($request);

        if (! $translateMailsBodyForm->isSubmitted() || ! $translateMailsBodyForm->isValid()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot translate emails body content',
                    [],
                    'Admin.Notifications.Error'
                )
            );

            return $this->redirectToRoute('admin_mail_theme_index');
        }

        $translateData = $translateMailsBodyForm->getData();
        $language = $translateData['language'];
        $locale = $translationService->langToLocale($language);

        return $this->redirectToRoute('admin_international_translation_overview', [
            'lang' => $language,
            'locale' => $locale,
            'type' => 'mails_body',
        ]);
    }

    /**
     * Preview a mail layout from a defined theme
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function previewLayoutAction(string $theme, string $layout, string $type, string $locale, string $module = ''): Response
    {
        $renderedLayout = $this->renderLayout($theme, $layout, $type, $locale, $module);

        return new Response($renderedLayout);
    }

    /**
     * Display the raw source of a theme layout (mainly useful for developers/integrators)
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function rawLayoutAction(string $theme, string $layout, string $type, string $locale, string $module = ''): Response
    {
        $renderedLayout = $this->renderLayout($theme, $layout, $type, $locale, $module);

        return new Response($renderedLayout, Response::HTTP_OK, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Dynamically display an email template, this is usually used by the MailGenerator but this action
     * allows to display preview before generating the file (handy when you are developing an email theme)
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    private function renderLayout(string $themeName, string $layoutName, string $type, string $locale = '', string $module = ''): string
    {
        $layout = $this->getMailLayout($themeName, $layoutName, $module);

        if ($locale === '' || $locale === '0') {
            $locale = $this->getLanguageContext()->getLocale();
        }

        $language = $this->container->get(LanguageRepositoryInterface::class)->getOneByLocaleOrIsoCode($locale);
        if ($language === null) {
            throw new InvalidArgumentException(\sprintf('Cannot find Language with locale or isoCode %s', $locale));
        }

        $mailLayoutVariables = $this->container->get(MailPreviewVariablesBuilder::class)->buildTemplateVariables($layout);

        $renderer = $this->container->get(MailTemplateRendererInterface::class);
        // Special case for preview, we fill the mail variables
        $renderer->addTransformation(new MailVariablesTransformation(MailTemplateInterface::HTML_TYPE, $mailLayoutVariables));
        $renderer->addTransformation(new MailVariablesTransformation(MailTemplateInterface::TXT_TYPE, $mailLayoutVariables));

        return match ($type) {
            MailTemplateInterface::HTML_TYPE => $renderer->renderHtml($layout, $language),
            MailTemplateInterface::TXT_TYPE => $renderer->renderTxt($layout, $language),
            default => throw new NotFoundHttpException(\sprintf('Requested type %s is not managed, please use one of these: %s', $type, implode(',', [MailTemplateInterface::HTML_TYPE, MailTemplateInterface::TXT_TYPE]))),
        };
    }

    /**
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    private function getMailLayout(string $themeName, string $layoutName, string $module): LayoutInterface
    {
        $themeCatalog = $this->container->get(ThemeCatalogInterface::class);
        /** @var ThemeInterface $theme */
        $theme = $themeCatalog->getByName($themeName);

        /** @var LayoutInterface $layout */
        $layout = null;
        /** @var LayoutInterface $layoutInterface */
        foreach ($theme->getLayouts() as $layoutInterface) {
            if ($layoutInterface->getName() === $layoutName
                && $layoutInterface->getModuleName() === $module
            ) {
                $layout = $layoutInterface;
                break;
            }
        }

        if ($layout === null) {
            throw new FileNotFoundException(\sprintf('Cannot find layout %s%s in theme %s', $module === '' || $module === '0' ? '' : $module . ':', $layoutName, $themeName));
        }

        return $layout;
    }
}
