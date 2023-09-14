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

namespace iMi\ContaoShibbolethLoginClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_KEY = 'shibboleth_auth_client';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::ROOT_KEY);

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('shibboleth')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('debug_mode')
                            ->defaultFalse()
                            ->info('If set to true, the details about the resource owner will be logged (contao log).')
                        ->end()
                        ->scalarNode('client_auth_endpoint_frontend_route')
                            ->cannotBeEmpty()
                            ->defaultValue('shibboleth_sso_login_frontend')
                        ->end()
                        ->scalarNode('client_auth_endpoint_backend_route')
                            ->cannotBeEmpty()
                            ->defaultValue('shibboleth_sso_login_backend')
                        ->end()
                        ->booleanNode('auto_create_frontend_user')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('allow_frontend_login_if_contao_account_is_disabled')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('allowed_frontend_groups')
                            ->scalarPrototype()->end()
                            ->info('Array of allowed SAC section ids. eg. [4250,4251,4252,4253,4254]')
                        ->end()
                        ->arrayNode('add_to_frontend_user_groups')
                            ->scalarPrototype()->end()
                            ->info('Add one or more contao frontend user group ids where user will be assigned, if he logs in. eg [9,10]')
                        ->end()
                        ->booleanNode('auto_create_backend_user')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('allow_backend_login_if_contao_account_is_disabled')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('allowed_backend_groups')
                            ->scalarPrototype()->end()
                            ->info('Array of allowed SAC section ids. eg. [4250,4251,4252,4253,4254]')
                        ->end()
                        ->booleanNode('enable_backend_sso')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end() // end shibboleth
                ->arrayNode('session')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('flash_bag_key')
                            ->cannotBeEmpty()
                            ->defaultValue('_shibboleth_auth_client_flash_bag')
                        ->end()
                    ->end()
                ->end() // session
                ->arrayNode('backend')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('disable_contao_login')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
