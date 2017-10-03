<?php
namespace App\Presenters;

use Nette;
use App\Model;


class PasswordPresenter extends BasePresenter {
    /** @var Model\User @inject */
    public $user;



    protected function createComponentChangePasswordForm() {
        $newpass = $this->getHttpRequest()->getUrl()->getQueryParameter("newpass"); // ziska link z url ???

        $form = $this->user->changePasswordForm($newpass);
        $form->onSuccess[] = array($this, 'redirectHome');
        return $form;
    }

    protected function createComponentForgottenPasswordForm() {
        $form = $this->user->forgottenPasswordForm();
        $form->onSuccess[] = array($this, 'redirectHome');
        return $form;
    }

    public function redirectHome($form) {
        // po uspěšném přihlášení přesměrujeme na homepage
        $this->getPresenter()->redirect('Homepage:default');
    }
}