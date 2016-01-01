<?php

namespace CmsModule\Presenters;

use CmsModule\Forms\ILoginFormFactory;
use CmsModule\Forms\LoginForm;
use Kdyby\Doctrine\DuplicateEntryException;
use Nette\Utils\ArrayHash;

class LoginPresenter extends BasePresenter
{

    /** @var ILoginFormFactory @inject */
    public $loginFormFactory;

    /** @var IProfileFormFactory @todo inject */
    public $profileFormFactory;


    /**
     * @return LoginForm
     */
    protected function createComponentLoginForm()
    {
        $form = $this->loginFormFactory->create();
        $form->onLoggedIn = array(function(LoginForm $form, $values) {
            $this->flashMessage('Byly jste přihlášeni do systému SmartUP administrace.', 'success');
            $this->redirect(':Cms:Dashboard:');

            /*
             * login event
             */

        });
        return $form;
    }

    public function actionLogOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Byly jste odhlašeni ze systému.');
        $this->redirect(':Cms:Dashboard:');
    }


    public function renderEdit()
    {
        $this->template->userEntity = $this->userEntity;
    }

    protected function createComponentProfileForm()
    {
        $form = $this->profileFormFactory->create();
        $form->injectEntityMapper($this->formMapper);
        $form->bindEntity($this->userEntity);
        $form->onSuccess[] = function(ProfileForm $form, ArrayHash $values) {

            /** @var UserEntity $entity */
            $entity = $form->entity;
            try {
                if ($password = $values['passwordNew']) {
                    $entity->setPassword($password);
                }

                $this->em->persist($form->entity)->flush();
                $this->flashMessage('Váš profil byl aktualizován');

                if ($password) {
                    $this->mailerManager->adminUpdatePassword(
                        $entity->getMail(),
                        array(
                            'newPassword'  => $password,
                        )
                    );
                    $emailSend = 'Na Váš E-mail bylo zasláno nové heslo do administrátorského prostředí.';
                    // $this->template->emailSend = $emailSend;
                    $this->flashMessage($emailSend, 'success');
                }

            } catch (DuplicateEntryException $exc) {
                $this->flashMessage('Litujeme, ale Vámi zadaný E-mail již existuje', 'danger');

            } catch (\Exception $exc) {
                $this->flashMessage("Chyba " . $exc->getMessage(), 'error');
            }

            if ($this->isAjax()) $this->redrawControl(); else $this->redirect('this');
        };

        return $form;
    }
}
