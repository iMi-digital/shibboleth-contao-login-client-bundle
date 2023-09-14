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

namespace iMi\ContaoShibbolethLoginClientBundle\EventSubscriber;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\MemberModel;
use Contao\UserModel;
use iMi\ContaoShibbolethLoginClientBundle\Config\ContaoLogConfig;
use iMi\ContaoShibbolethLoginClientBundle\Event\InvalidLoginAttemptEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Safe\json_encode;

class InvalidLoginAttemptSubscriber implements EventSubscriberInterface
{
    private const PRIORITY = 1000;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly LoggerInterface|null $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [InvalidLoginAttemptEvent::NAME => ['handleFailedLoginAttempts', self::PRIORITY]];
    }

    /**
     * @throws \Exception
     */
    public function handleFailedLoginAttempts(InvalidLoginAttemptEvent $loginEvent): void
    {
        // Increment tl_user.loginAttempts or tl_member.loginAttempts, if login fails
        // Write cause of error to the Contao system log
        $resourceOwner = $loginEvent->getResourceOwner();

        // Prepare args for the log text
        $logArgs = [
            $resourceOwner->getFirstName(),
            $resourceOwner->getLastName(),
            $resourceOwner->getId(),
            $loginEvent->getCauseOfError(),
            json_encode($resourceOwner->toArray()),
        ];

        if (ContaoCoreBundle::SCOPE_FRONTEND === $loginEvent->getContaoScope()) {
            $memberModelAdapter = $this->framework->getAdapter(MemberModel::class);
            $userModel = $memberModelAdapter->findByUsername($resourceOwner->getId());
            $logLevel = ContaoLogConfig::SAC_OAUTH2_FRONTEND_LOGIN_FAIL;
            $logText = sprintf(
                'Shibboleth (SSO-Frontend-Login) failed for user "%s %s" with member id [%s]. Cause: %s. JSON Payload: %s',
                ...$logArgs,
            );
        } else {
            $userModelAdapter = $this->framework->getAdapter(UserModel::class);
            $userModel = $userModelAdapter->findByUsername($resourceOwner->getId());
            $logLevel = ContaoLogConfig::SAC_OAUTH2_BACKEND_LOGIN_FAIL;
            $logText = sprintf(
                'Shibboleth (SSO-Backend-Login) failed for user "%s %s" with member id [%s]. Cause: %s. JSON Payload: %s',
                ...$logArgs,
            );
        }

        if (null !== $userModel) {
            ++$userModel->loginAttempts;
            $userModel->save();
        }

        $this->logger->info($logText, ['contao' => new ContaoContext(__METHOD__, $logLevel, null)]);
    }
}
