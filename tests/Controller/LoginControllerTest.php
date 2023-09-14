<?php

declare(strict_types=1);

namespace iMi\ContaoShibbolethLoginClientBundle\Tests\Controller;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\MemberModel;
use Contao\TestCase\ContaoDatabaseTrait;
use iMi\ContaoShibbolethLoginClientBundle\Controller\ContaoShibbolethLoginController;
use iMi\ContaoShibbolethLoginClientBundle\Tests\ContaoTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class LoginControllerTest extends ContaoTestCase
{
    use ContaoDatabaseTrait;

    private ContaoShibbolethLoginController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->getContainer()->get(\iMi\ContaoShibbolethLoginClientBundle\Controller\ContaoShibbolethLoginController::class);
    }

    public function testCanLoginWithGroupStaffOnFrontend(): void
    {
        $memberGroup = $this->createMemberGroup();

        $session = new Session(new MockFileSessionStorage());

        $request = new Request(['redirectAfterSuccess' => 'https://www.example.com/success'], [], [], [], [], [
            'REDIRECT_unscoped-affiliation' => 'staff',
            'REDIRECT_uid' => 'testuser',
            'REDIRECT_sn' => 'User',
            'REDIRECT_mail' => 'testuser@example.com',
            'REDIRECT_cn' => 'Test Tester',
        ]);
        $request->setSession($session);
        $this->getContainer()->get('request_stack')->push($request);

        try {
            $this->controller->__invoke($request, 'frontend');
        } catch (RedirectResponseException $exception) {
            $this->assertTrue($exception->getResponse()->isRedirect());
            $this->assertEquals('https://www.example.com/success', $exception->getResponse()->headers->get('Location'));

            $user = MemberModel::findByUsername('testuser');
            $this->assertEquals([$memberGroup->id], unserialize($user->groups));

            return;
        }

        $this->fail('Redirect exception expected');
    }

    public function testCannotLoginWithInvalidGroupOnFrontend(): void
    {
        $session = new Session(new MockFileSessionStorage());

        $request = new Request(['redirectAfterSuccess' => 'https://www.example.com/success'], [], [], [], [], [
            'REDIRECT_unscoped-affiliation' => 'notstaff',
            'REDIRECT_uid' => 'testuser',
            'REDIRECT_sn' => 'User',
            'REDIRECT_mail' => 'testuser@example.com',
            'REDIRECT_cn' => 'Test Tester',
        ]);
        $request->setSession($session);
        $this->getContainer()->get('request_stack')->push($request);

        try {
            $this->controller->__invoke($request, 'frontend');
        } catch (RedirectResponseException $exception) {
            $this->assertTrue($exception->getResponse()->isRedirect());
            $this->assertEquals('https://www.example.com/success?failure=1', $exception->getResponse()->headers->get('Location'));
            return;
        }
        $this->fail('Redirect exception expected');
    }

    public function testCannotLoginInAdminPanelBecauseUserDoesNotExist(): void
    {
        $session = new Session(new MockFileSessionStorage());

        $request = new Request(['redirectAfterSuccess' => 'https://www.example.com/contao'], [], [], [], [], [
            'REDIRECT_unscoped-affiliation' => 'admin',
            'REDIRECT_uid' => 'testadmin',
            'REDIRECT_sn' => 'User',
            'REDIRECT_mail' => 'testadmin@example.com',
            'REDIRECT_cn' => 'Test Tester',
        ]);
        $request->setSession($session);
        $this->getContainer()->get('request_stack')->push($request);

        try {
            $this->controller->__invoke($request, 'backend');
        } catch (RedirectResponseException $exception) {
            $this->assertTrue($exception->getResponse()->isRedirect());
            $this->assertEquals('https://www.example.com/contao?failure=1', $exception->getResponse()->headers->get('Location'));
            return;
        }
        $this->fail('Redirect exception expected');
    }
}
