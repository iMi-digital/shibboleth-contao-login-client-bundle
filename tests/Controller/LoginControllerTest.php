<?php

declare(strict_types=1);

namespace iMi\ContaoShibbolethLoginClientBundle\Tests\Controller;

use Contao\CoreBundle\Exception\RedirectResponseException;
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

    public function testFrontendInvoke(): void
    {
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
            return;
        }
        $this->fail('Redirect exception expected');
    }

    public function testAdminPanelInvoke(): void
    {
        $this->markTestIncomplete('TODO: Need to pre-create the user first');

        $session = new Session(new MockFileSessionStorage());

        $request = new Request([], [], [], [], [], [
            'REDIRECT_unscoped-affiliation' => 'admin',
            'REDIRECT_uid' => 'testadmin',
            'REDIRECT_sn' => 'User',
            'REDIRECT_mail' => 'testadmin@example.com',
            'REDIRECT_cn' => 'Test Tester',
        ]);
        $request->setSession($session);
        $this->getContainer()->get('request_stack')->push($request);

        $this->expectException(RedirectResponseException::class);

        $this->controller->__invoke($request, 'backend');
    }
}
