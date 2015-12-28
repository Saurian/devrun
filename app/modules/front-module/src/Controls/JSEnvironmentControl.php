<?php

namespace FrontModule\Controls;

use Nette\Application\UI\Control;
use Nette\Utils\Html;

class JSEnvironmentControl extends Control
{

    /** @var \Nette\Http\Request $request */
    private $request;

    function __construct(\Nette\Http\Request $request)
    {
        $this->request = $request;
    }

    public function render()
    {
        $basePath = $this->request->getUrl()->getBasePath();
        $data     = "var base_path = \"" . $basePath . '";';

        /** @var Html $el */
        $el = Html::el("script")->type("text/javascript");
        $el->setText($data);

        echo $el;
    }

}