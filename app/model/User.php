<?php

namespace App\Model;

use Nette;
use Nette\Security as NS;
use App\Forms as FO;
use App\Data as AD;
use Nette\Application\UI\Form;
use App\Model\Model as AM;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;



class User extends AM
{

    /** @var FO\SignInForm */
    private $signInForm;

    /** @var FO\SignUpForm */
    private $signUpForm;

    /** @var FO\ForgottenPasswordForm */
    private $forgottenPasswordForm;

    /** @var FO\ChangePasswordForm */
    private $changePasswordForm;

    /** @var FO\ModifyProfileForm */
    private $modifyProfileForm;


    /** @var AD\Users */
    private $userFacade;

    /** @var AD\Users */
    private $binaryFacade;

    /** @var AD\Users */
    private $processingFacade;

    /** @var AD\Users */
    private $materialFacade;

    /** @var AD\Users */
    private $maskFacade;

    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var Nette\Application\UI\ITemplateFactory */
    private $templateFactory;

    public function __construct( NS\User $user, FO\SignInForm $signIn,
                                FO\SignUpForm $signUp, FO\ChangePasswordForm $changePassword, FO\ForgottenPasswordForm $forgottenPassword,
                                 FO\ModifyProfileForm $modifyProfile, AD\Users $users, AD\Masks $masks,
                                 AD\Materials $materials, AD\Processings $processings, AD\Binaries $binaries,
                                 Nette\Application\UI\ITemplateFactory $templateFactory,
                                 Nette\Application\LinkGenerator $linkGenerator)
    {
        parent::__construct($user);
        $this->userFacade = $users;
        $this->maskFacade = $masks;
        $this->binaryFacade = $binaries;
        $this->processingFacade = $processings;
        $this->materialFacade = $materials;
        $this->signInForm = $signIn;
        $this->signUpForm = $signUp;
        $this->forgottenPasswordForm = $forgottenPassword;
        $this->changePasswordForm = $changePassword;
        $this->modifyProfileForm = $modifyProfile;
        $this->templateFactory = $templateFactory;
        $this->linkGenerator = $linkGenerator;
    }

    public function getData()
    {
        $user = $this->userFacade->findById($this->user->id);

        if ($this->user) {
            return array(
                "name" => $user->getName(),
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "processingCount" => $this->processingFacade->countByAuthorId($user->getId()),
                "binaryCount" => $this->binaryFacade->countByAuthorId($user->getId()),
                "maskgCount" => $this->maskFacade->countByAuthorId($user->getId()),
                "materialCount" => $this->materialFacade->countByAuthorId($user->getId()));
        } else {
            // přesměřuj na přihlášení...
        }
    }

    public function getIdentity()
    {
        return $this->user->getIdentity();
    }

    public function getId()
    {
        return $this->user->getId();
    }

    public function isLoggedIn()
    {
        return $this->user->isLoggedIn();
    }

    /* Login uživatelů */

    public function signInForm()
    {
        $form = $this->signInForm->create();
        $form->onSuccess[] = array($this, 'signIn');
        return $form;
    }

    public function activateAccount($link){
        $foundUser = $this->userFacade->findByLink($link);
        if(!$foundUser)
        {
            throw new NS\AuthenticationException('Such user was not found in the database', self::IDENTITY_NOT_FOUND);
            return false;
        }
        $this->userFacade->activateByLink($link);
        return true;
    }

    public function signIn(Form $form, $values)
    {
        /* pokud je ucet vubec v db*/
        try {
            $active = $this->isActive($values->email);
        } catch (NS\AuthenticationException $e) {
            $form->addError($e->getMessage());
            $this->flashMessage('Zadané uživatelské jméno v databázi neexistuje.');
        }

        /* pokud jeste nebyl aktivovan */
        if ($active == FALSE) {
            $form->addError('Nejprve si aktivujte účet pomocí emailu.');
        } else {
            try {
                $this->user->login($values->email, $values->password);
            } catch (NS\AuthenticationException $e) {
                $this->flashMessage('Login se nepovedl ale ucet je aktivovan (asi).');
                $form->addError($e->getMessage());
            }
        }
    }

    public function isActive($email){

        $foundUser = $this->userFacade->findByEmail($email);
        if(!$foundUser)
        {
            throw new NS\AuthenticationException('Such user was not found in the database', self::IDENTITY_NOT_FOUND);
        }
        return $foundUser->getActiveStatus();
    }


    /* Vytvoření uživatelů */

    public function signUpForm()
    {
        $form = $this->signUpForm->create();
        $form->onSuccess[] = array($this, 'signUp');
        return $form;
    }

    /* Změna hesla */

    public function forgottenPasswordForm()
    {
        $form = $this->forgottenPasswordForm->create();
        $form->onSuccess[] = array($this, 'forgottenPassword');
        return $form;
    }

    public function changePasswordForm($newpass)
    {
        $form = $this->changePasswordForm->create();
        $form->addHidden('newpass', $newpass);
        $form->onSuccess[] = array($this, 'changePassword');
        return $form;
    }

    public function forgottenPassword(Form $form, $values)
    {
        if($this->userFacade->forgottenPassword($values->newpass, $values->email)){
            $this->sendEmail($values, __DIR__ .'/../templates/Email/changePassword.latte');
            $form->getPresenter()->flashMessage('Email s odkazem pro změnu hesla byl odeslán.');
        } else {
            $form->getPresenter()->flashMessage('Email se nepodařilo odeslat.');
        }
    }

    public function sendEmail($values, $path){
        $message = new Message;
        $message->setFrom('VDDT <admin@vddt.com>')
            ->addTo($values->email);
            //->addBcc('anna.moudra@gmail.com');

        $templateEmail = $this->templateFactory->createTemplate();
        $templateEmail->getLatte()->addProvider('uiControl', $this->linkGenerator);

        $templateEmail->setFile($path);
        $templateEmail->values=$values;
        $message->setHtmlBody($templateEmail);
        $mailer = new SendmailMailer;
        $mailer->send($message);
    }


    /* Zmena profilu */

    public function changePassword(Form $form, $values)
    {
        if($this->userFacade->changePassword($values->newpass, $values->password)){
            $form->getPresenter()->flashMessage('Heslo bylo změněno. Nyní se můžete přihlásit.');
        } else {
            $form->getPresenter()->flashMessage('Heslo se nepodařilo změnit.');
        }
    }

    public function modifyProfileForm()
    {
        $userToModify = $this->userFacade->findById($this->user->id);
        $profileData["degree"] = $userToModify->getDegree();
        $profileData["email"] = $userToModify->getEmail();
        $profileData["name"] = $userToModify->getFirstName();
        $profileData["surname"] = $userToModify->getLastName();
        $form = $this->modifyProfileForm->modify($profileData);
        $form->onSuccess[] = array($this, 'modifyProfile');
        return $form;
    }

    public function modifyProfile(Form $form, $values)
    {
        //najdeme uzivatele podle id a pokusime se o upravu udaju
        $userNew = $this->userFacade->findById($this->user->id);
        if(!$userNew){
            $form->getPresenter()->flashMessage('Uživatel nenalezen');
            $form->getPresenter()->redirect('Profile:modify');
        }

        if($userNew->comparePasswords($values->password)) {//vraci true/false
            $existingEmail = $this->userFacade->findByEmail($values->email);
            if ($existingEmail && $existingEmail->getId() != $this->user->id) {
                $form->getPresenter()->flashMessage('Údaje se nepodařilo změnit, nově zadaný email už má registrovaný účet.');
                $form->getPresenter()->redirect('Profile:modify');
            }
        }
        else{
            # odešleme zprávu o neúspěchu
            $form->getPresenter()->flashMessage('Údaje se nepodařilo změnit, zadali jste správné heslo? '.$values->password);
            $form->getPresenter()->redirect('Profile:modify');
        }

        $userToModify = $this->userFacade->modifyProfile( $this->user->id, $values->degree, $values->name, $values->surname, $values->email);
        if($userToModify){
            # odešleme zprávu o úspěchu
            $form->getPresenter()->flashMessage('Údaje byly změněny.');
            $form->getPresenter()->redirect('Profile:detail');
        }
        else{
            # odešleme zprávu o neúspěchu
            $form->getPresenter()->flashMessage('Údaje se nepodařilo změnit, zadali jste správné heslo? Také se může stát, že nově zadaný email už má registrovaný účet.');
            $form->getPresenter()->redirect('Profile:modify');
        }

    }

    public function signUp(Form $form, $values)
    {
        try {
            #pokud uživatel neexistuje vytvoříme ho
            if (!$user = $this->userFacade->findByEmail($values->email)) {
                #nový uživatel nesmí mít prázdné heslo!
                if (empty($values->password)) {
                    throw new \InvalidArgumentException('nový uživatel nemůže mít prázdné heslo');
                }

                try{
                    #vytvoření nového uživatele
                    $this->userFacade->makeNew($values->email, $values->name, $values->surname, $values->degree, $values->password, $values->link);
                    $this->sendEmail($values, __DIR__ .'/../templates/Email/activateAccount.latte');
                }
                catch(\Exception $e){
                    $form->getPresenter()->flashMessage('Email nebyl odeslan.');
                }

                //$user = new UserDAO($values->email, $values->name, $values->surname, $values->degree, $values->password);
                # odešleme zprávu o úspěchu
                $form->getPresenter()->flashMessage('Uživatel byl vytvořen. Na zadanou emailovou adresu Vám přijde odkaz k aktivaci účtu.');
            } else {
                $form->getPresenter()->flashMessage('Uživatel již existuje');
            }

            $form->getPresenter()->redirect('Homepage');
        } catch (\Exception $e) {
            $form->addError($e->getMessage());
        }
    }



    public function logOut()
    {
        if ($this->user->isLoggedIn()) {
            $this->user->logout();
        }
    }

}
