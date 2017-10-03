<?php
namespace App\Model;

use Nette;
use Kdyby;
use App\Data as U;


class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{

    private $users;
	
    public function __construct(U\Users $users)
    {
        $this->users = $users;
    }

    public function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;
        /**
         * @var Entities\User $user
         */
        $user = $this->users->findByEmail($email);
        # nenalezli jsem uživatele
        if (!$user) {
            throw new Nette\Security\AuthenticationException("Uživatel '$email' nenalezen.", self::IDENTITY_NOT_FOUND);
        }
        #nesedí hesla
        if (!Nette\Security\Passwords::verify($password,$user->getPassword())) {
            throw new Nette\Security\AuthenticationException("Neplatné heslo.", self::INVALID_CREDENTIAL);
        }
        # vrátíme identitu
        return new Nette\Security\Identity($user->getId(), $user->getRole(), array('email' => $user->getEmail()));
    }
}