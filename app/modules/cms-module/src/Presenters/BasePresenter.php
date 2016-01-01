<?php

/**
 *
 * This file is part of the CMS
 *
 * Copyright (c) 2015
 *
 * @file BasePresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Presenters;

use CmsModule\Controls\JSEnvironmentControl;
use CmsModule\Doctrine\EntityFormMapper;
use CmsModule\Entities\UserEntity;
use CmsModule\Facades\UserFacade;
use Grido\Grid;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
    /** @persistent */
    public $locale;

    /** @var EntityManager @inject */
    public $em;

    /** @var UserFacade @inject */
    public $userManager;

    /** @var MailerManager @todo inject */
    public $mailerManager;

    /** @var NotificationManager @todo inject */
    public $notificationManager;

    /** @var JSEnvironmentControl @inject */
    public $jsEnvironmentControl;

    /** @var EntityFormMapper @inject */
    public $formMapper;

    /** @var UserEntity */
    public $userEntity;


    protected function startup()
    {
        parent::startup();

        $user = $this->getUser();

        if (!$user->isAllowed($this->name, $this->action)) {
            $this->getUser()->logout();
            $this->redirect(':Cms:Login:', array('backlink' => $this->storeRequest()));
        }

        if ($this->getUser()->isLoggedIn()) {
            $this->userEntity = $this->em->getRepository(UserEntity::getClassName())->find($this->getUser()->getId());
        }

        $this->template->robots = "index, follow";
    }

    protected function beforeRender()
    {
        parent::beforeRender();

        // $helper = $this->commonHelper;

        /** @var Template $template */
         $template = $this->getTemplate();
        // $template->addFilter('czechDate', array($helper, 'czechDate'));

        $template->version = $this->context->parameters['website']['version'];
        $template->production = $this->context->parameters['website']['production'];


        //$this->ajaxLayout();
    }

    protected function ajaxLayout()
    {
        if ($this->isAjax() && !$this->enableAjaxLayout) $this->setLayout(false);
    }


    public function actionClearCache()
    {
        if ($dir = $this->getContext()->getParameters()['tempDir'] . '/cache') {
            foreach (\Nette\Utils\Finder::findFiles('*.php')->from($dir) as $key => $file) {
                @unlink($key);
            }
            $storage = new \Nette\Caching\Storages\FileStorage($dir);
            $cache = new \Nette\Caching\Cache($storage);
            $cache->clean(array(\Nette\Caching\Cache::ALL=>true));
            $this->redirect('Homepage:');
        }
    }


    protected function createTemplate($class = NULL)
    {
        $template = parent::createTemplate();
        $latte = $template->getLatte();

        $set = new \Latte\Macros\MacroSet($latte->getCompiler());
        $set->addMacro('scache', '?>?<?php echo strtotime(date(\'Y-m-d hh \')); ?>"<?php');

        $latte->addFilter('scache', $set);
        return $template;
    }


    /**
     * ajax redirect
     *
     * @param string $uri
     * @param null   $gridControlToRedraw
     */
    public function ajaxRedirect($uri = 'this', $gridControlToRedraw = null)
    {
        if ($this->isAjax()) {

            if ($gridControlToRedraw) {

                /** @var Grid $grid */
                $grid = $this[$gridControlToRedraw];
                $grid->redrawControl();
            }

            $this->redrawControl();

        } else {
            $this->redirect($uri);
        }
    }

    /**
     * ajax redraw snippets if needed
     * call from js extensions
     */
    public function handleAjaxRedrawAction()
    {
        if ($this->isAjax()) {
            $this->redrawControl();
        }
    }


    public function isSubPresenterActive($class)
    {
        $subClasses = [
            'projects' => [
                'Cms:Projects',
                'Cms:Requests',
                'Cms:EvaluedProjects',
            ],
            'projectsSupport' => [
                'Cms:SupportRecommendation',
                'Cms:Process',
                'Cms:Action',
                'Cms:Blog',
                'Cms:Milestone',
                'Cms:BudgetItem',
                'Cms:Message',
                'Cms:ChangeRequestsList',
                'Cms:Advertising',
                'Cms:FinalReport',
            ],
            'content' => [
                'Cms:Ambassadors',
                'Cms:Article',
                'Cms:ActionsPage',
                'Cms:Rules',
                'Cms:Form',
                'Cms:News',
                'Cms:Homepage',
                'Cms:ProjectsPage',
                'Cms:Education',
                'Cms:Downloads',
            ],
            'info' => [
                'Cms:Registration',
            ],
            'evaluations' => [
                'Cms:Evaluators',
                'Cms:Mentors',
            ],
        ];

        if (isset($subClasses[$class])) {
            return in_array($this->getName(), $subClasses[$class]);
        }

        return false;
    }

    public function createComponentJSEnvironment()
    {
        return $this->jsEnvironmentControl;
    }

}
