<?php
namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;

/**
 * @ORM\Entity
 * @ORM\Table(name="user",    options={"collate"="utf8_slovak_ci"})
 */
class UserDAO extends \Kdyby\Doctrine\Entities\BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $surname;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $degree;

    /**
     * @ORM\Column(type="string")
     */
    protected $passhash;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $link;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $newpass;
    /**
     * @ORM\Column(type="string")

     */
    protected $role;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $active;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $affiliation;

    
    function __construct($email, $name, $surname, $degree, $password, $link, $affiliation = NULL,  $role = 1)
    {
        $this->name = $name;
        $this->setPassword($password);
        $this->role = $role;
        $this->email = $email;
        $this->surname= $surname;
        $this->link = $link;
        $this->degree = $degree;
	    $this->affiliation = $affiliation;
    }
	
	public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->role;
    }
	
	public function getName()
    {
        return $this->degree . " " . $this->name . " " . $this->surname;
    }

    public function getDegree()
    {
        return $this->degree;
    }

    public function getFirstName()
    {
        return $this->name;
    }
    public function getLastName()
    {
        return $this->surname;
    }
	
	public function getEmail()
    {
        return $this->email;
    }
    
    	public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->passhash = Passwords::hash($password);
    }
    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->passhash;
    }

    /**
     * @return string
     */
    public function getActiveStatus()
    {
        return $this->active;
    }
    
    public function activate()
    {
        $this->active = 1;
    }

    public function changePassword($password)
    {
        $this->passhash = Passwords::hash($password);
        $this->newpass = NULL;
    }

    public function forgottenPassword($newpass)
    {
        $this->newpass = $newpass;
    }

    public function comparePasswords($password)
    {
        return Passwords::verify($password, $this->passhash);
    }

    public function updateUser($degree, $name, $surname, $email)
    {
        $this->degree = $degree;
        $this->name = $name;
        $this->email = $email;
        $this->surname = $surname;
    }

}
