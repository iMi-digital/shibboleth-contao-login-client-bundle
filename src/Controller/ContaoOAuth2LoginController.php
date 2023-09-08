<?php

declare(strict_types=1);

/*
 * This file is part of Swiss Alpine Club Contao Login Client Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/swiss-alpine-club-contao-login-client-bundle
 */

namespace Markocupic\SwissAlpineClubContaoLoginClientBundle\Controller;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\Exception\InvalidRequestTokenException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\SwissAlpineClubContaoLoginClientBundle\Client\OAuth2ClientFactory;
use Markocupic\SwissAlpineClubContaoLoginClientBundle\Event\OAuth2SuccessEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ssoauth/frontend', name: 'swiss_alpine_club_sso_login_frontend', defaults: ['_scope' => 'frontend', '_token_check' => false])]
#[Route('/ssoauth/backend', name: 'swiss_alpine_club_sso_login_backend', defaults: ['_scope' => 'backend', '_token_check' => false])]
class ContaoOAuth2LoginController extends AbstractController
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OAuth2ClientFactory $oAuth2ClientFactory,
        private readonly string $contaoCsrfTokenName,
        private readonly bool $enableCsrfTokenCheck,
    ) {
    }

    public function __invoke(Request $request, string $_scope): Response
    {
        $this->framework->initialize(ContaoCoreBundle::SCOPE_FRONTEND === $_scope);

////        var_dump($_SERVER);
//        var_dump($_SERVER['REDIRECT_unscoped-affiliation']);
//        var_dump($_SERVER['REDIRECT_uid']);
//        var_dump($_SERVER['REDIRECT_sn']);
//        var_dump($_SERVER['REDIRECT_mail']);
//        var_dump($_SERVER['REDIRECT_cn']);
        if (!$request->server->has('REDIRECT_unscoped-affiliation')) {
            throw new \Markocupic\SwissAlpineClubContaoLoginClientBundle\Client\Exception\InvalidStateException('Required field missing');
        }

        return $this->getAccessTokenAction($request, $_scope);
    }

    private function getAccessTokenAction(Request $request, string $_scope): Response
    {
        // We have an access token!
        // But the user is still not logged in against the Contao backend/frontend firewall.
        $userData = [
            'group' => $request->server->get('REDIRECT_unscoped-affiliation'),
            'uid' => $request->server->get('REDIRECT_uid'),
            'sn' => $request->server->get('REDIRECT_sn'),
            'mail' => $request->server->get('REDIRECT_mail'),
            'cn' => $request->server->get('REDIRECT_cn'),
        ];

        $oauth2SuccessEvent = new OAuth2SuccessEvent($userData, $_scope);

        if (!$this->eventDispatcher->hasListeners($oauth2SuccessEvent::NAME)) {
            return new Response('Successful OAuth2 login but no success handler defined.');
        }

        // Dispatch the OAuth2 success event.
        // Use an event subscriber to ...
        // - identify the Contao user from OAuth2 user
        // - check if user is in an allowed section, etc.
        // - and login to the Contao firewall or redirect to login-failure page
        $this->eventDispatcher->dispatch($oauth2SuccessEvent, $oauth2SuccessEvent::NAME);

        // This point should normally not be reached at all,
        // since a successful login will take you to the Contao frontend or backend.
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
