<?php
namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;

class Users extends ADBF
{

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Entities\UserDAO::class);
    }
    
    public function makeNew($email, $name, $surname, $degree, $password, $link, $affiliation = NULL,  $role = 1)
    {
        $new = new Entities\UserDAO($email, $name, $surname, $degree, $password, $link, $affiliation, $role);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }
    
    /**
     * @param $link
     * vrati entitu User nebo NULL
     * @return mixed|null|object
     */
    public function activateByLink($link)
    {
        $userToActivate = $this->findByLink($link);
        if($userToActivate){
            $userToActivate->activate();
            $this->entityManager->persist($userToActivate);
            $this->entityManager->flush();
        }
        return $userToActivate;
    }
    
    /**
     * @param $link
     * vrati entitu User nebo NULL
     * @return mixed|null|object
     */
    public function findByLink($link)
    {
        return $this->repository->findOneBy(array('link' => $link));
    }

    /**
     * @param $newpasslink email sent identificator
     * @param $newpassword new password
     * vrati entitu User nebo NULL
     * @return mixed|null|object
     */
    public function changePassword($newpasslink, $newpassword)
    {
        $userNewPass = $this->repository->findOneBy(array('newpass' => $newpasslink));
        if($userNewPass){
            $userNewPass->changePassword($newpassword);
            $this->entityManager->persist($userNewPass);
            $this->entityManager->flush();
        }
        return $userNewPass;
    }

    /**
     * @param $newpasslink email sent identificator
     * @param $newpassword new password
     * vrati entitu User nebo NULL
     * @return mixed|null|object
     */
    public function forgottenPassword($newpasslink, $email)
    {
        $userNewPass = $this->findByEmail($email);
        if($userNewPass){
            $userNewPass->forgottenPassword($newpasslink);
            $this->entityManager->persist($userNewPass);
            $this->entityManager->flush();
        }
        return $userNewPass;
    }

    /**
     * @param $email
     * vrati entitu User nebo NULL
     * @return null|Entities\User
     */
    public function findByEmail($email)
    {
        return $this->repository->findOneBy(array('email' => $email));
    }

    /**
     * @param $id user identificator
     * @param $values form values
     * vrati entitu User nebo NULL
     * @return mixed|null|object
     */
    public function modifyProfile($id, $degree, $name, $surname, $email)
    {
        $userNew = $this->findById($id);
        $userNew->updateUser($degree, $name, $surname, $email);
        $this->entityManager->persist($userNew);
        $this->entityManager->flush();
        return $userNew;
    }

    /**
     * @param $id
     * vrati entitu User nebo NULL
     * @return mixed|null|object
     */
    public function findById($id)
    {
        return $this->repository->findOneBy(array('id' => $id));
    }


}
