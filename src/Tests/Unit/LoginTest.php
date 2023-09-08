<?php

use Contao\TestCase\ContaoTestCase;

class LoginTest extends ContaoTestCase
{

    public function testLogin()
    {
        $container = $this->getContainerWithContaoConfiguration();

        echo $container->getParameter('contao.upload_path'); // will output "files"

        $framework = $this->mockContaoFramework();

        $eventDispatcher = $container->get('event_dispatcher');


        $controller = new \Markocupic\SwissAlpineClubContaoLoginClientBundle\Controller\ContaoOAuth2LoginController($framework, $eventDispatcher);
        $response = $controller->renderAction(new Request(), '{{request_token}}');
    }
}
