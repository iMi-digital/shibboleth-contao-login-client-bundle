<?php

declare(strict_types=1);

/*
 * This file is part of Shibboleth Contao Login Client Bundle.
 *
 * (c) iMi digital GmbH <digital@imi.de>, based on work by Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/swiss-alpine-club-contao-login-client-bundle
 */

namespace iMi\ContaoShibbolethLoginClientBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use JustSteveKing\UriBuilder\Uri;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsFrontendModule(ContaoShibbolethFrontendLogin::TYPE, category: 'user', template: 'mod_shibboleth_frontend_login')]
class ContaoShibbolethFrontendLogin extends AbstractFrontendModuleController
{
    public const TYPE = 'shibboleth_frontend_login';

    public function __construct(
        private readonly ContaoFramework     $framework,
        private readonly Security            $security,
        private readonly RequestStack        $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
    ) {
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response|null
    {
        /** @var Environment $environmentAdapter */
        $environmentAdapter = $this->framework->getAdapter(Environment::class);

        /** @var PageModel $pageModelAdapter */
        $pageModelAdapter = $this->framework->getAdapter(PageModel::class);

        /** @var System $systemAdapter */
        $systemAdapter = $this->framework->getAdapter(System::class);

        /** @var StringUtil $stringUtilAdapter */
        $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);

        /** @var Uri $urlAdapter */
        $uriAdapter = $this->framework->getAdapter(Uri::class);

        // Get logged in member object
        if (($user = $this->security->getUser()) instanceof FrontendUser) {
            $template->loggedInAs = $this->translator->trans('MSC.loggedInAs', [$user->username], 'contao_default');
            $template->username = $user->username;
            $template->logout = true;
        } else {
            $strRedirect = $environmentAdapter->get('base').$environmentAdapter->get('request');

            if (!$model->redirectBack && $model->jumpTo > 0) {
                $redirectPage = $pageModelAdapter->findByPk($model->jumpTo);
                $strRedirect = $redirectPage instanceof PageModel ? $redirectPage->getAbsoluteUrl() : $strRedirect;
            }

            // Since Contao 4.9 urls are base64 encoded
            $template->targetPath = $strRedirect;

            $redirectRoute = 'shibboleth_sso_login_frontend';
            $template->shibbolethLoginUrl = $this->router->generate(
                $redirectRoute,
                ['redirectAfterSuccess' => $strRedirect],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            $uri = $uriAdapter->fromString($request->getUri());
            $uri->addQueryParam('sso_error', 'true');
            $template->failurePath = $stringUtilAdapter->specialchars(base64_encode($uri->toString()));

            $template->login = true;

            $template->btnLbl = empty($model->shibboleth_frontend_login_btn_lbl) ? $this->translator->trans('MSC.loginWithShibbolethSso', [], 'contao_default') : $model->shibboleth_frontend_login_btn_lbl;

            $request = $this->requestStack->getCurrentRequest();

            // Check for error messages & start session only if there was an error
            if ($request->query->has('sso_error')) {
                $session = $request->getSession();
                $flashBagKey = $systemAdapter->getContainer()->getParameter('shibboleth_auth_client.session.flash_bag_key');
                $flashBag = $session->getFlashBag()->get($flashBagKey);

                if (\count($flashBag) > 0) {
                    $arrError = [];

                    foreach ($flashBag[0] as $k => $v) {
                        if ('level' === $k) {
                            $arrError['bs-alert-class'] = 'error' === $v ? 'danger' : $v;
                        }
                        $arrError[$k] = $v;
                    }
                    $template->error = $arrError;
                }
            }
        }

        return $template->getResponse();
    }
}
