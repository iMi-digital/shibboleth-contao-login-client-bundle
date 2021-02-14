<?php

declare(strict_types=1);

/*
 * This file is part of Swiss Alpine Club Contao Login Client Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/swiss-alpine-club-contao-login-client-bundle
 */

namespace Markocupic\SwissAlpineClubContaoLoginClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class MarkocupicSwissAlpineClubContaoLoginClientExtension.
 */
class MarkocupicSwissAlpineClubContaoLoginClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        // Default is markocupic_swiss_alpine_club_contao_login_client_bundle
        return 'markocupic_sac_sso_login';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('listener.yml');
        $loader->load('services.yml');
        $loader->load('controller-contao-frontend-module.yml');

        $namespace = $this->getAlias();
        // Oidc stuff
        $container->setParameter($namespace.'.oidc.client_id', $config['oidc']['client_id']);
        $container->setParameter($namespace.'.oidc.client_secret', $config['oidc']['client_secret']);
        $container->setParameter($namespace.'.oidc.url_authorize', $config['oidc']['url_authorize']);
        $container->setParameter($namespace.'.oidc.url_access_token', $config['oidc']['url_access_token']);
        $container->setParameter($namespace.'.oidc.resource_owner_details', $config['oidc']['resource_owner_details']);
        $container->setParameter($namespace.'.oidc.add_to_member_groups', $config['oidc']['add_to_member_groups']);
        $container->setParameter($namespace.'.oidc.url_logout', $config['oidc']['url_logout']);
        $container->setParameter($namespace.'.oidc.redirect_uri_frontend', $config['oidc']['redirect_uri_frontend']);
        $container->setParameter($namespace.'.oidc.redirect_uri_backend', $config['oidc']['redirect_uri_backend']);
        $container->setParameter($namespace.'.oidc.enable_backend_sso', $config['oidc']['enable_backend_sso']);
        $container->setParameter($namespace.'.oidc.enable_csrf_token_check', $config['oidc']['enable_csrf_token_check']);
        // Session stuff
        $container->setParameter($namespace.'.session.attribute_bag_key', $config['session']['attribute_bag_key']);
        $container->setParameter($namespace.'.session.attribute_bag_name', $config['session']['attribute_bag_name']);
        $container->setParameter($namespace.'.session.flash_bag_key', $config['session']['flash_bag_key']);
    }
}
