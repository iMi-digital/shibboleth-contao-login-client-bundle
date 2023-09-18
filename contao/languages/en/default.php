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

/*
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['loginWithShibbolethSso'] = 'Log in with Shibboleth';
$GLOBALS['TL_LANG']['MSC']['logoutFromShibbolethIdp'] = 'Log out from Shibboleth Identity Provider';

// Error management
$GLOBALS['TL_LANG']['MSC']['infoMatter'] = 'Information';
$GLOBALS['TL_LANG']['MSC']['warningMatter'] = 'Warning';
$GLOBALS['TL_LANG']['MSC']['errorMatter'] = 'Error Message';
$GLOBALS['TL_LANG']['MSC']['errorHowToFix'] = 'What can I do?';
$GLOBALS['TL_LANG']['MSC']['errorExplain'] = 'Explanation';
$GLOBALS['TL_LANG']['MSC']['or'] = 'or';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_accountDisabled_matter'] = 'Hello %s.{{br}}Unfortunately, the verification of your data transmitted to us by the Identity Provider has failed.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_accountDisabled_howToFix'] = 'If you believe this is an error, please contact the Identity Provider';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_accountDisabled_explain'] = 'Unfortunately, your account has been disabled and cannot be used at the moment.';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userDoesNotExist_matter'] = 'Hello %s.{{br}}Unfortunately, the verification of your data transmitted to us by the Identity Provider has failed.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userDoesNotExist_howToFix'] = 'User does not exist.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userDoesNotExist_explain'] = 'This must be created first.';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_backendUserNotFound_matter'] = 'Hello %s.{{br}}Unfortunately, your account could not be found. If you believe this is an error, please contact the administrator with your concerns.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_backendUserNotFound_howToFix'] = '';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_backendUserNotFound_explain'] = '';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userIsNotMemberOfAllowedSection_matter'] = 'Hello %s.{{br}}Unfortunately, your login attempt did not work because you do not appear to be a member of the respective groups.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userIsNotMemberOfAllowedSection_howToFix'] = 'Contact the administrator / the Identity Provider or login with another user after you logged out from the identify provider.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userIsNotMemberOfAllowedSection_explain'] = '';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_invalidEmail_matter'] = 'Hello %s{{br}}Unfortunately, the verification of your data transmitted to us by the Identity Provider has failed.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_invalidEmail_howToFix'] = 'You have not yet provided a valid email address.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_invalidEmail_explain'] = 'Some applications on this portal require a valid email address.';
