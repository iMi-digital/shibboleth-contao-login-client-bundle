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

namespace iMi\ContaoShibbolethLoginClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class iMiContaoShibbolethLoginClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        // Default root key would be markocupic_shibboleth_auth_client
        return Configuration::ROOT_KEY;
    }

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $loader->load('subscriber.yaml');
        $loader->load('services.yaml');

        $rootKey = $this->getAlias();

        $container->setParameter($rootKey.'.shibboleth.debug_mode', $config['shibboleth']['debug_mode']);
        $container->setParameter($rootKey.'.shibboleth.client_id', $config['shibboleth']['client_id']);
        $container->setParameter($rootKey.'.shibboleth.client_secret', $config['shibboleth']['client_secret']);
        $container->setParameter($rootKey.'.shibboleth.auth_provider_endpoint_authorize', $config['shibboleth']['auth_provider_endpoint_authorize']);
        $container->setParameter($rootKey.'.shibboleth.auth_provider_endpoint_token', $config['shibboleth']['auth_provider_endpoint_token']);
        $container->setParameter($rootKey.'.shibboleth.auth_provider_endpoint_userinfo', $config['shibboleth']['auth_provider_endpoint_userinfo']);
        $container->setParameter($rootKey.'.shibboleth.add_to_frontend_user_groups', $config['shibboleth']['add_to_frontend_user_groups']);
        $container->setParameter($rootKey.'.shibboleth.auth_provider_endpoint_logout', $config['shibboleth']['auth_provider_endpoint_logout']);
        $container->setParameter($rootKey.'.shibboleth.auto_create_frontend_user', $config['shibboleth']['auto_create_frontend_user']);
        $container->setParameter($rootKey.'.shibboleth.allow_frontend_login_to_sac_members_only', $config['shibboleth']['allow_frontend_login_to_sac_members_only']);
        $container->setParameter($rootKey.'.shibboleth.allow_frontend_login_to_predefined_section_members_only', $config['shibboleth']['allow_frontend_login_to_predefined_section_members_only']);
        $container->setParameter($rootKey.'.shibboleth.allow_frontend_login_if_contao_account_is_disabled', $config['shibboleth']['allow_frontend_login_if_contao_account_is_disabled']);
        $container->setParameter($rootKey.'.shibboleth.auto_create_backend_user', $config['shibboleth']['auto_create_backend_user']);
        $container->setParameter($rootKey.'.shibboleth.allow_backend_login_to_sac_members_only', $config['shibboleth']['allow_backend_login_to_sac_members_only']);
        $container->setParameter($rootKey.'.shibboleth.allow_backend_login_to_predefined_section_members_only', $config['shibboleth']['allow_backend_login_to_predefined_section_members_only']);
        $container->setParameter($rootKey.'.shibboleth.allow_backend_login_if_contao_account_is_disabled', $config['shibboleth']['allow_backend_login_if_contao_account_is_disabled']);
        $container->setParameter($rootKey.'.shibboleth.allowed_frontend_groups', $config['shibboleth']['allowed_frontend_groups']);
        $container->setParameter($rootKey.'.shibboleth.allowed_backend_groups', $config['shibboleth']['allowed_backend_groups']);
        $container->setParameter($rootKey.'.shibboleth.client_auth_endpoint_frontend_route', $config['shibboleth']['client_auth_endpoint_frontend_route']);
        $container->setParameter($rootKey.'.shibboleth.client_auth_endpoint_backend_route', $config['shibboleth']['client_auth_endpoint_backend_route']);
        $container->setParameter($rootKey.'.shibboleth.enable_backend_sso', $config['shibboleth']['enable_backend_sso']);
        $container->setParameter($rootKey.'.shibboleth.enable_csrf_token_check', $config['shibboleth']['enable_csrf_token_check']);
        // Session stuff
        $container->setParameter($rootKey.'.session.flash_bag_key', $config['session']['flash_bag_key']);
        // Backend settings
        $container->setParameter($rootKey.'.backend.disable_contao_login', $config['backend']['disable_contao_login']);
    }
}
