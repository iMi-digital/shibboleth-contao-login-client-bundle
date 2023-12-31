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

namespace iMi\ContaoShibbolethLoginClientBundle\Event;

use iMi\ContaoShibbolethLoginClientBundle\Security\Auth\AuthUser;
use Symfony\Contracts\EventDispatcher\Event;

class ShibbolethSuccessEvent extends Event
{
    public const NAME = 'imi_shibboleth_client.auth_success';

    public function __construct(
        private readonly AuthUser $userData,
        private readonly ?string $redirectAfterSuccess,
        private readonly string $contaoScope
    ) {
    }

    public function getAuthUser(): AuthUser
    {
        return $this->userData;
    }

    public function getContaoScope(): string
    {
        return $this->contaoScope;
    }

    public function getRedirectAfterSuccess(): ?string
    {
        return $this->redirectAfterSuccess;
    }
}
