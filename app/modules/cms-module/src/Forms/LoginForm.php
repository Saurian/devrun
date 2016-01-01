<?php
/**
 * This file is part of the smart-up
 * Copyright (c) 2015
 *
 * @file    LoginForm.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Forms;

use Nette\Application\UI\Form;

interface ILoginFormFactory
{
    /** @return LoginForm */
    function create();
}

/**
 * Class LoginForm
 *
 * @package CmsModule\Forms
 * @method onLoggedIn($form, $user)
 */
class LoginForm extends Form implements ILoginFormFactory
{

    public $onLoggedIn = [];

    /** @return LoginForm */
    function create()
    {
        // $this->addProtection('Vypršela platnost zabezpečovacího tokenu. Prosím, odešlete přihlašovací formulář znovu.');

        $this->addText('username', 'Přihlašovací jméno:')
            ->setAttribute('placeholder', "Přihlašovací jméno")
            ->addRule(Form::FILLED, 'Zadejte přihlašovací jméno.')
            ->addRule(Form::MIN_LENGTH, 'Přihlašovací jméno musí mít minimálně 4 znaky.', 4)
            ->addRule(Form::MAX_LENGTH, 'Přihlašovací jméno může mít maximálně 32 znaků.', 32)
            ->getControlPrototype()->class = 'form-control';

        $this->addPassword('password', 'Heslo:')
            ->setAttribute('placeholder', "Heslo")
            ->addRule(Form::FILLED, 'Zadejte heslo.')
            ->getControlPrototype()->class = 'form-control';

        $this->addCheckbox('remember', 'Zapamatovat si');
        $this->addSubmit('login', 'Přihlásit se')->getControlPrototype()->class = 'btn btn-primary btn-block btn-flat';
        $this->onSuccess[] = array($this, 'formSuccess');

        $this->getElementPrototype()->class = 'margin-bottom-0';
    }


    public function formSuccess(LoginForm $form)
    {
        $presenter = $this->getPresenter();

        try {
            $values = $form->getValues();
            $user = $presenter->getUser();

            if ($values['remember']) {
                $user->setExpiration('7 days', TRUE);
            }

            $user->login($values['username'], $values['password']);
            $this->onLoggedIn($this, $user);

        } catch (\Nette\Security\AuthenticationException $e) {
//            $presenter->flashMessage($e->getMessage(), 'warning');
            $form->addError($e->getMessage());
        }

    }

}