<?php
/**
 * This file is part of the sandbox
 * Copyright (c) 2015
 *
 * @file    FrontExtension.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace FrontModule\DI;

use Flame\Modules\Configurators\IPresenterMappingConfig;
use Flame\Modules\Providers\IPresenterMappingProvider;
use Flame\Modules\Providers\IRouterProvider;
use Kdyby\Doctrine\DI\IEntityProvider;
use Nette;
use Flame;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\DI\CompilerExtension;

class FrontExtension extends CompilerExtension implements
    IEntityProvider,
    IPresenterMappingProvider,
    IRouterProvider
{

    const TAG_COMMAND = 'kdyby.console.command';
    const TAG_SUBSCRIBER = 'kdyby.subscriber';
    const TAG_FILTER = 'filter.widget';
    const TAG_FORM = 'form.widget';

    public $defaults = array(
        'environment'        => '%debugMode%',
        'expirationResponse' => '1 hour',
        'bridgeStorage'           => [
            'path'       => '%tempDir%/cache/_Bridge.Storage',
            'expiration' => '2 hours',
        ],
        'filterStorage'           => [
            'path'       => '%tempDir%/cache/_Filter.Storage',
            'expiration' => '2 hours',
        ],
    );

    private function testMode()
    {
        return $this->compiler->getConfig()['parameters']['consoleMode'];
    }

    public function loadConfiguration()
    {

        $builder = $this->getContainerBuilder();
        $config  = $this->getConfig($this->defaults);


        $builder->addDefinition($this->prefix('jsEnvironment'))
            ->setClass('FrontModule\Controls\JSEnvironmentControl');

    }

    public function beforeCompile()
    {
        parent::beforeCompile();
        $builder = $this->getContainerBuilder();
        $config  = $this->getConfig($this->defaults);

        if (!is_dir($debugDir = $builder->expand('%tempDir%/debug'))) {
            @mkdir($debugDir, 0777, true);
        }

        foreach (array('filterStorage', 'bridgeStorage') as $pathSystem) {
            if (!is_dir($pathSystem = $builder->expand($config[$pathSystem]['path']))) {
                @mkdir($pathSystem, 0777, true);
            }
        }
    }


    /**
     * Returns associative array of Namespace => mapping definition
     *
     * @return array
     */
    function getEntityMappings()
    {
        return array(
            'FrontModule\Entities' => dirname(__DIR__) . '*Entity.php',
        );
    }


    /**
     * Returns array of ServiceDefinition,
     * that will be appended to setup of router service
     *
     * @return \Nette\Application\IRouter
     */

    public function getRoutesDefinition()
    {
        $routeList     = new RouteList;
        $routeList[]   = $frontRouter = new RouteList('Front');
        $frontRouter[] = new Route('sitemap.xml', array('presenter' => 'Sitemap', 'action' => 'sitemap',));
        $frontRouter[] = new Route('clear', array('presenter' => 'Homepage', 'action' => 'clearCache',));
        $frontRouter[] = new Route('[<locale=cs sk|en|cs>/]<presenter>/<action>[/<id>]', array(
            'presenter' => array(
                Route::VALUE        => 'Homepage',
                Route::FILTER_TABLE => array(),
            ),
            'action'    => array(
                Route::VALUE        => 'default',
                Route::FILTER_TABLE => array(
                    'smazat' => 'clear',
                ),
            ),
            'id' => null
        ));
        return $routeList;
    }


    /**
     * Setup presenter mapping : ClassNameMask => PresenterNameMask
     *
     * @example https://gist.github.com/jsifalda/50bedd439ab23df57058
     *
     * @param IPresenterMappingConfig &$presenterMappingConfig
     *
     * @return void
     */
    public function setupPresenterMapping(IPresenterMappingConfig &$presenterMappingConfig)
    {
        $presenterMappingConfig->setMapping('Front', 'FrontModule\*Module\Presenters\*Presenter');
    }
}