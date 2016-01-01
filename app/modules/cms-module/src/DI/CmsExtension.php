<?php
/**
 * This file is part of the cms
 * Copyright (c) 2015
 *
 * @file    CmsExtension.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\DI;

use Flame\Modules\Providers\IPresenterMappingProvider;
use Flame\Modules\Providers\IRouterProvider;
use Kdyby\Doctrine\DI\IEntityProvider;
use Nette;
use Flame;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\DI\CompilerExtension;

class CmsExtension extends CompilerExtension implements
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


    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config  = $this->getConfig($this->defaults);

        // system
        $builder->addDefinition($this->prefix('authorizator'))
            ->setClass('CmsModule\Security\Authorizator');

        $builder->addDefinition($this->prefix('authenticator'))
            ->setClass('CmsModule\Security\Authenticator', array($this->prefix('@repositories.user')));


        // controls
        $builder->addDefinition($this->prefix('controls.jsEnvironment'))
            ->setClass('CmsModule\Controls\JSEnvironmentControl');


        // facades
        $this->registerFacades();

        // forms
        $this->registerForms();

        // forms
        $this->registerRepositories();


    }

    private function registerFacades()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('facades.user'))
            ->setClass('CmsModule\Facades\UserFacade');

    }


    private function registerForms()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('forms.loginFormFactory'))
            ->setImplement('CmsModule\Forms\ILoginFormFactory')
            ->addSetup('create');

    }


    private function registerRepositories()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('repositories.user'))
            ->setClass('CmsModule\Repository\UserRepository');



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
            'CmsModule\Entities' => dirname(__DIR__) . '*Entity.php',
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
        $router = new RouteList();

        $router[] = $adminRouter = new RouteList('Cms');

        $adminRouter[] = new Route('admin/[<locale=cs cs|en>/]<presenter>/<action>[/<id>]', array(
            'presenter' => 'Dashboard',
            'action'    => 'default'
        ));

        return $router;
    }


    /**
     * Returns array of ClassNameMask => PresenterNameMask
     *
     * @example return array('*' => 'Booking\*Module\Presenters\*Presenter');
     * @return array
     */
    public function getPresenterMapping()
    {
        return [
            'Cms' => 'CmsModule\*Module\Presenters\*Presenter',
        ];
    }

}