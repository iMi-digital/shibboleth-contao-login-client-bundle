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

namespace iMi\ContaoShibbolethLoginClientBundle\Security\Auth;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Contao\Validator;
use iMi\ContaoShibbolethLoginClientBundle\ErrorMessage\ErrorMessage;
use iMi\ContaoShibbolethLoginClientBundle\ErrorMessage\ErrorMessageManager;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthUserChecker
{
    /**
     * NAVISION section id regex.
     */
    public const NAV_SECTION_ID_REGEX = '/NAV_MITGLIED_S(\d+)/';

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly TranslatorInterface $translator,
        private readonly ErrorMessageManager $errorMessageManager,
    ) {
    }

    /**
     * Check if OAuth user has a valid uuid/sub.
     */
    public function checkHasUuid(AuthUser $authUser): bool
    {
        /** @var System $systemAdapter */
        if (empty($authUser->getId())) {
            $this->errorMessageManager->add2Flash(
                new ErrorMessage(
                    ErrorMessage::LEVEL_WARNING,
                    $this->translator->trans('ERR.shibbolethLoginError_invalidUuid_matter', [], 'contao_default'),
                    $this->translator->trans('ERR.shibbolethLoginError_invalidUuid_howToFix', [], 'contao_default'),
                )
            );

            return false;
        }

        return true;
    }

    /**
     * Check for allowed SAC section membership.
     */
    public function checkIsMemberOfAllowedAffiliation(AuthUser $authUser, string $contaoScope): bool
    {
        $arrMembership = $this->getAllowedAffiliations($authUser, $contaoScope);

        if (\count($arrMembership) > 0) {
            return true;
        }

        $this->errorMessageManager->add2Flash(
            new ErrorMessage(
                ErrorMessage::LEVEL_WARNING,
                $this->translator->trans('ERR.shibbolethLoginError_userIsNotMemberOfAllowedSection_matter', [$authUser->getFirstName()], 'contao_default'),
                $this->translator->trans('ERR.shibbolethLoginError_userIsNotMemberOfAllowedSection_howToFix', [], 'contao_default'),
            )
        );

        return false;
    }

    /**
     * Check if OAuth user has a valid email address.
     */
    public function checkHasValidEmailAddress(AuthUser $authUser): bool
    {
        /** @var Validator $validatorAdapter */
        $validatorAdapter = $this->framework->getAdapter(Validator::class);

        if (empty($authUser->getEmail()) || !$validatorAdapter->isEmail($authUser->getEmail())) {
            $this->errorMessageManager->add2Flash(
                new ErrorMessage(
                    ErrorMessage::LEVEL_WARNING,
                    $this->translator->trans('ERR.shibbolethLoginError_invalidEmail_matter', [$authUser->getFirstName()], 'contao_default'),
                    $this->translator->trans('ERR.shibbolethLoginError_invalidEmail_howToFix', [], 'contao_default'),
                    $this->translator->trans('ERR.shibbolethLoginError_invalidEmail_explain', [], 'contao_default'),
                )
            );

            return false;
        }

        return true;
    }

    /**
     * Return all allowed SAC section ids a OAuth user belongs to.
     */
    public function getAllowedAffiliations(AuthUser $authUser, string $contaoScope): array
    {
        /** @var System $systemAdapter */
        $systemAdapter = $this->framework->getAdapter(System::class);

        if (ContaoCoreBundle::SCOPE_FRONTEND === $contaoScope) {
            $arrAllowedGroups = $systemAdapter
                ->getContainer()
                ->getParameter('shibboleth_auth_client.shibboleth.allowed_frontend_groups')
            ;
        } else {
            $arrAllowedGroups = $systemAdapter
                ->getContainer()
                ->getParameter('shibboleth_auth_client.shibboleth.allowed_backend_groups')
            ;
        }

        $arrGroupMembership = $this->getRoles($authUser);

        return array_unique(array_intersect($arrAllowedGroups, $arrGroupMembership));
    }

    /**
     * Return all SAC section ids a OAuth user belongs to.
     */
    private function getRoles(AuthUser $authUser): array
    {
        $strRoles = $authUser->getRolesAsArray();

        if (empty($strRoles)) {
            return [];
        }

        return $strRoles;
    }
}
