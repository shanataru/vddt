<?php
namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;

class CameraMoves extends ADBF {
    
    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Entities\CameraMoveDAO::class);
    }
    
    public function makeNew($move)
    {
        $new = new Entities\CameraMoveDAO($move);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }
    
    /**
     * @param $move
     * @return null|Entities\CameraMoveDAO
     */
    public function findByMove($move)
    {
        return $this->repository->findOneBy(array('move' => $move));
    }
    
    /**
     * @param $id
     * @return mixed|null|object
     */
    public function findById($id)
    {
        return $this->repository->findOneBy(array('id' => $id));
    }
    


}
