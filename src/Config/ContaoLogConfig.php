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

namespace iMi\ContaoShibbolethLoginClientBundle\Config;

final class ContaoLogConfig
{
    public const SAC_OAUTH2_DEBUG_LOG = 'SAC_SSO_DEBUG_LOG';
    public const SAC_OAUTH2_FRONTEND_LOGIN_FAIL = 'SAC_SSO_FRONTEND_LOGIN_FAIL';
    public const SAC_OAUTH2_BACKEND_LOGIN_FAIL = 'SAC_SSO_BACKEND_LOGIN_FAIL';
}
