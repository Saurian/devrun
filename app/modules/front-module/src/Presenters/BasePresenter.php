<?php

namespace FrontModule\Presenters;

use FrontModule\Controls\JSEnvironmentControl;
use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var JSEnvironmentControl @inject */
    public $jsEnvironmentControl;



    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->version    = $this->context->parameters['website']['version'];
        $this->template->production = $this->context->parameters['website']['production'];

    }


    public function createComponentJSEnvironment()
    {
        return $this->jsEnvironmentControl;
    }

}
