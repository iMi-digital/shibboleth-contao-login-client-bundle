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

/*
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['loginWithShibbolethSso'] = 'Login with Shibboleth';

// Error management
$GLOBALS['TL_LANG']['MSC']['infoMatter'] = 'Information';
$GLOBALS['TL_LANG']['MSC']['warningMatter'] = 'Warnung';
$GLOBALS['TL_LANG']['MSC']['errorMatter'] = 'Fehlermeldung';
$GLOBALS['TL_LANG']['MSC']['errorHowToFix'] = 'Was kann ich tun?';
$GLOBALS['TL_LANG']['MSC']['errorExplain'] = 'Erklärung';
$GLOBALS['TL_LANG']['MSC']['or'] = 'or';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_accountDisabled_matter'] = 'Hallo %s{{br}}. Leider ist die Überprüfung deiner vom Identity Provider an uns übermittelten Daten fehlgeschlagen.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_accountDisabled_howToFix'] = 'Falls du der Meinung bist, dass es sich hier um einen Irrtum handelt, dann melde dich beim Identity Provider';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_accountDisabled_explain'] = 'Dein Konto wurde leider deaktiviert und kann im Moment nicht verwendet werden.';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userDoesNotExist_matter'] = 'Hallo %s{{br}}. Leider ist die Überprüfung deiner vom Identity Provider an uns übermittelten Daten fehlgeschlagen.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userDoesNotExist_howToFix'] = 'Benutzer existiert nicht.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userDoesNotExist_explain'] = 'Dieser muss zuerst angelegt werden.';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_backendUserNotFound_matter'] = 'Hallo %s{{br}}. Leider konnte dein Konto nicht gefunden werden. Wenn du denkst, dass es sich um einen Irrtum handelt, dann melde dich mit deinem Anliegen beim Administrator.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_backendUserNotFound_howToFix'] = '';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_backendUserNotFound_explain'] = '';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userIsNotMemberOfAllowedSection_matter'] = 'Hallo %s{{br}}. Leider hat dein Loginversuch nicht geklappt, weil du kein Mitglied der entsprechenden Gruppen zu sein scheints.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userIsNotMemberOfAllowedSection_howToFix'] = 'Wende dich an den Administrator / den Identify Provider.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_userIsNotMemberOfAllowedSection_explain'] = '';

$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_invalidEmail_matter'] = 'Hallo %s{{br}} Leider hat die Überprüfung deiner vom Identity Provider an uns übermittelten Daten fehlgeschlagen.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_invalidEmail_howToFix'] = 'Du hast noch keine gültige E-Mail-Adresse hinterlegt.';
$GLOBALS['TL_LANG']['ERR']['shibbolethLoginError_invalidEmail_explain'] = 'Einige Anwendungen auf diesem Portal setzen eine gültige E-Mail-Adresse voraus.';
