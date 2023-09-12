<?php

declare(strict_types=1);

/*
 * This file is part of Shibboleth Contao Login Client Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/swiss-alpine-club-contao-login-client-bundle
 */

namespace iMi\ContaoShibbolethLoginClientBundle\Security\Authentication;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use League\Shibboleth\Client\Provider\Exception\IdentityProviderException;
use iMi\ContaoShibbolethLoginClientBundle\Client\ShibbolethClient;
use iMi\ContaoShibbolethLoginClientBundle\Config\ContaoLogConfig;
use iMi\ContaoShibbolethLoginClientBundle\ErrorMessage\ErrorMessageManager;
use iMi\ContaoShibbolethLoginClientBundle\Event\InvalidLoginAttemptEvent;
use iMi\ContaoShibbolethLoginClientBundle\Security\InteractiveLogin\InteractiveLogin;
use iMi\ContaoShibbolethLoginClientBundle\Security\Auth\AuthUser;
use iMi\ContaoShibbolethLoginClientBundle\Security\Auth\AuthUserChecker;
use iMi\ContaoShibbolethLoginClientBundle\Security\User\ContaoUser;
use iMi\ContaoShibbolethLoginClientBundle\Security\User\ContaoUserFactory;
use Psr\Log\LoggerInterface;
use Safe\Exceptions\JsonException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function Safe\json_encode;

class Authenticator
{
    private Adapter $system;

    public function __construct(
        private readonly ContaoFramework          $framework,
        private readonly ContaoUserFactory        $contaoUserFactory,
        private readonly ErrorMessageManager      $errorMessageManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InteractiveLogin         $interactiveLogin,
        private readonly AuthUserChecker          $authUserChecker,
        private readonly LoggerInterface|null     $logger = null,
    ) {
        // Adapters
        $this->system = $this->framework->getAdapter(System::class);
    }

    /**
     * @throws IdentityProviderException
     * @throws JsonException
     */
    public function authenticateContaoUser(AuthUser $authUser, ?string $redirectAfterSuccess, string $contaoScope): void
    {
        $allowedScopes = [
            ContaoCoreBundle::SCOPE_BACKEND,
            ContaoCoreBundle::SCOPE_FRONTEND,
        ];

        if (!\in_array($contaoScope, $allowedScopes, true)) {
            throw new \InvalidArgumentException(sprintf('The Contao Scope must be either "%s" "%s" given.', implode('" or "', $allowedScopes), $contaoScope));
        }

        $container = $this->system->getContainer();

        $isDebugMode = $container->getParameter('shibboleth_auth_client.shibboleth.debug_mode');

        /** @var bool $blnAutoCreateContaoUser */
        $blnAutoCreateContaoUser = $container->getParameter('shibboleth_auth_client.shibboleth.auto_create_'.$contaoScope.'_user');

        /** @var bool $blnAllowContaoLoginIfAccountIsDisabled */
        $blnAllowContaoLoginIfAccountIsDisabled = $container->getParameter('shibboleth_auth_client.shibboleth.allow_'.$contaoScope.'_login_if_contao_account_is_disabled');

        // For testing & debugging purposes only
        //$authUser->overrideData($authUser->getDummyResourceOwnerData(true));

        if ($isDebugMode) {
            // Log OAuth user details
            $logText = sprintf(
                'SAC oauth2 debug %s login. NAME: %s - SAC MEMBER ID: %s - ROLES: %s - DATA ALL: %s',
                $contaoScope,
                $authUser->getFullName(),
                $authUser->getSacMemberId(),
                $authUser->getRolesAsString(),
                json_encode($authUser->toArray()),
            );

            $this->log($logText, __METHOD__, ContaoLogConfig::SAC_OAUTH2_DEBUG_LOG);
        }

        // Check if uuid/sub is set
        if (!$this->authUserChecker->checkHasUuid($authUser)) {
            $this->dispatchInvalidLoginAttemptEvent(InvalidLoginAttemptEvent::FAILED_CHECK_HAS_UUID, $contaoScope, $authUser);

            throw new RedirectResponseException($oAuth2Client->getFailurePath());
        }

//        // Check if user is a SAC member
//        if ($blnAllowLoginToSacMembersOnly) {
//            if (!$this->authUserChecker->checkIsSacMember($authUser)) {
//                $this->dispatchInvalidLoginAttemptEvent(InvalidLoginAttemptEvent::FAILED_CHECK_IS_SAC_MEMBER, $contaoScope, $authUser);
//
//                throw new RedirectResponseException($oAuth2Client->getFailurePath());
//            }
//        }

//        // Check if user is member of an allowed section
//        if ($blnAllowLoginToPredefinedSectionsOnly) {
//            if (!$this->authUserChecker->checkIsMemberOfAllowedSection($authUser, $contaoScope)) {
//                $this->dispatchInvalidLoginAttemptEvent(InvalidLoginAttemptEvent::FAILED_CHECK_IS_MEMBER_OF_ALLOWED_SECTION, $contaoScope, $authUser);
//
//                throw new RedirectResponseException($oAuth2Client->getFailurePath());
//            }
//        }

        // Check has valid email address
        // This test should always be positive,
        // because creating an account at https://www.sac-cas.ch
        // requires already a valid email address
        if (!$this->authUserChecker->checkHasValidEmailAddress($authUser)) {
            $this->dispatchInvalidLoginAttemptEvent(InvalidLoginAttemptEvent::FAILED_CHECK_HAS_VALID_EMAIL_ADDRESS, $contaoScope, $authUser);

            throw new RedirectResponseException($oAuth2Client->getFailurePath());
        }

        // Create the user wrapper object
        $contaoUser = $this->contaoUserFactory->loadContaoUser($authUser, $contaoScope);

        // Create Contao frontend or backend user, if it doesn't exist.
        if (ContaoCoreBundle::SCOPE_FRONTEND === $contaoScope) {
            if ($blnAutoCreateContaoUser) {
                $contaoUser->createIfNotExists();
            }
        }

        // if $contaoScope === 'backend': Check if Contao backend user exists
        // if $contaoScope === 'frontend': Check if Contao frontend user exists
        if (!$contaoUser->checkUserExists()) {
            $this->dispatchInvalidLoginAttemptEvent(InvalidLoginAttemptEvent::FAILED_CHECK_USER_EXISTS, $contaoScope, $authUser, $contaoUser);

            throw new RedirectResponseException($oAuth2Client->getFailurePath());
        }

        // Allow login to frontend users only if account is disabled
        if (ContaoCoreBundle::SCOPE_FRONTEND === $contaoScope) {
            // Set tl_member.disable = ''
            $contaoUser->enableLogin();
        }

        // if $contaoScope === 'backend': Set tl_user.locked = 0
        // if $contaoScope === 'frontend': Set tl_member.locked = 0
        $contaoUser->unlock();

        // Set tl_user.loginAttempts = 0
        $contaoUser->resetLoginAttempts();

        // Update tl_member and tl_user
        $contaoUser->updateFrontendUser();
        $contaoUser->updateBackendUser();

        // if $contaoScope === 'backend': Check if tl_user.disable == '' or tl_user.login == '1' or tl_user.start and tl_user.stop are not in an allowed time range
        // if $contaoScope === 'frontend': Check if tl_member.disable == '' or tl_member.login == '1' or tl_member.start and tl_member.stop are not in an allowed time range
        if (!$contaoUser->checkIsAccountEnabled() && !$blnAllowContaoLoginIfAccountIsDisabled) {
            $this->dispatchInvalidLoginAttemptEvent(InvalidLoginAttemptEvent::FAILED_CHECK_IS_ACCOUNT_ENABLED, $contaoScope, $authUser, $contaoUser);

            throw new RedirectResponseException($oAuth2Client->getFailurePath());
        }

        // The flash bag should actually be empty. Let's clear it to be on the safe side.
        $this->errorMessageManager->clearFlash();

        // Log in as a Contao backend or frontend user.
        $this->interactiveLogin->login($contaoUser);

        // Clear the session
        // <NOP> not done for Shibboleth, could be controlled in Apache

        // Contao system log
        $logText = sprintf(
            '%s User "%s" [%s] has logged in with SAC OPENID CONNECT APP.',
            ContaoCoreBundle::SCOPE_FRONTEND === $contaoScope ? 'Frontend' : 'Backend',
            $authUser->getFullName(),
            $authUser->getId()
        );
        $this->log($logText, __METHOD__, ContaoContext::ACCESS);

        // All ok. The Contao user has successfully logged in.
        // Let's redirect to the target page now.
        throw new RedirectResponseException($redirectAfterSuccess);
    }

    private function log(string $logText, string $method, string $context): void
    {
        $this->logger?->info(
            $logText,
            ['contao' => new ContaoContext($method, $context, null)]
        );
    }

    private function dispatchInvalidLoginAttemptEvent(string $causeOfError, string $contaoScope, OAuthUser $authUser, ContaoUser $contaoUser = null): void
    {
        $event = new InvalidLoginAttemptEvent($causeOfError, $contaoScope, $authUser, $contaoUser);
        $this->eventDispatcher->dispatch($event, InvalidLoginAttemptEvent::NAME);
    }
}
