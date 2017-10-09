<?php

namespace App\Presenters;

use Nette;
use App\Model;

class SignPresenter extends BasePresenter {

    /** @var Model\User @inject */
    public $user;

    protected function createComponentSignInForm() {
        $form = $this->user->signInForm();
        $form->onSuccess[] = array($this, 'redirectProfile');
        return $form;
    }

    protected function createComponentSignUpForm() {
        $form = $this->user->signUpForm();
        $form->onSuccess[] = array($this, 'redirectHome');
        return $form;
    }

    public function actionActivate() {
        $link = $this->getHttpRequest()->getUrl()->getQueryParameter("link"); // ziska link z url ???

        if($this->user->activateAccount($link)) {
            $this->flashMessage('Váš účet byl aktivován. Nyní se můžete přihlásit.'); //???
            $this->redirect('in');
        }
        else{
            $this->flashMessage('Something went very wrong.'); //???
            $this->redirect('Homepage:default');
        }
    }

    public function actionOut() {
        $this->user->logOut();
        $this->redirect('Homepage:default');
    }

    public function redirectHome($form) {
        $this->getPresenter()->redirect('Homepage:default');
    }

    public function redirectProfile($form) {
        $this->getPresenter()->redirect('Profile:detail');
    }

}
