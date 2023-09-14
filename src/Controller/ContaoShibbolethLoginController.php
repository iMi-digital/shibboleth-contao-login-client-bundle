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

namespace iMi\ContaoShibbolethLoginClientBundle\Controller;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\Exception\InvalidRequestTokenException;
use Contao\CoreBundle\Framework\ContaoFramework;
use iMi\ContaoShibbolethLoginClientBundle\Client\ShibbolethClientFactory;
use iMi\ContaoShibbolethLoginClientBundle\Event\ShibbolethSuccessEvent;
use iMi\ContaoShibbolethLoginClientBundle\Security\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ssoauth/frontend', name: 'shibboleth_sso_login_frontend', defaults: ['_scope' => 'frontend', '_token_check' => false])]
#[Route('/ssoauth/backend', name: 'shibboleth_sso_login_backend', defaults: ['_scope' => 'backend', '_token_check' => false])]
class ContaoShibbolethLoginController extends AbstractController
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(Request $request, string $_scope): Response
    {
        $this->framework->initialize(ContaoCoreBundle::SCOPE_FRONTEND === $_scope);

        if (!$request->server->has('REDIRECT_unscoped-affiliation')) {
            throw new \iMi\ContaoShibbolethLoginClientBundle\Client\Exception\InvalidStateException('Required field missing, reason can be a configuration problem in the webserver.');
        }

        return $this->getAccessTokenAction($request, $_scope);
    }

    private function getAccessTokenAction(Request $request, string $_scope): Response
    {
        // We have an access token!
        // But the user is still not logged in against the Contao backend/frontend firewall.
        $userData = [
            'groups' => $request->server->get('REDIRECT_unscoped-affiliation'),
            'uid' => $request->server->get('REDIRECT_uid'),
            'sn' => $request->server->get('REDIRECT_sn'),
            'mail' => $request->server->get('REDIRECT_mail'),
            'cn' => $request->server->get('REDIRECT_cn'),
        ];

        $redirectAfterSuccess = $request->get('redirectAfterSuccess', null);
        $user = new AuthUser($userData);
        $shibbolethSuccessEvent = new ShibbolethSuccessEvent($user, $redirectAfterSuccess, $_scope);

        if (!$this->eventDispatcher->hasListeners($shibbolethSuccessEvent::NAME)) {
            return new Response('Successful Shibboleth login but no success handler defined.');
        }

        // Dispatch the Shibboleth success event.
        // Use an event subscriber to ...
        // - identify the Contao user from Shibboleth user
        // - check if user is in an allowed section, etc.
        // - and login to the Contao firewall or redirect to login-failure page
        $this->eventDispatcher->dispatch($shibbolethSuccessEvent, $shibbolethSuccessEvent::NAME);

        // This point should normally not be reached at all,
        // since a successful login will take you to the Contao frontend or backend.
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
