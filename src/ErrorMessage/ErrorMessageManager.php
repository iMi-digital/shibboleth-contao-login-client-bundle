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

namespace iMi\ContaoShibbolethLoginClientBundle\ErrorMessage;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ErrorMessageManager
{
    public function __construct(
        private RequestStack $requestStack,
        private string $flashBagKey,
    ) {
    }

    /**
     * Add an error message to the session flash bag.
     */
    public function add2Flash(ErrorMessage $objErrorMsg): void
    {
        $this->getFlashBag()->add($this->flashBagKey, $objErrorMsg->get());
    }

    /**
     * Clear flash messages.
     */
    public function clearFlash(): void
    {
        $this->getFlashBag()->set($this->flashBagKey, []);
    }

    private function getFlashBag(): FlashBagInterface
    {
        return $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
    }
}
