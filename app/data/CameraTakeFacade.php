<?php
namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;

class CameraTakes extends ADBF
{
    
    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Entities\CameraTakeDAO::class);
    }
    
    public function makeNew($take)
    {
        $new = new Entities\CameraTakeDAO($take);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }
    
    /**
     * @param $take
     * @return null|Entities\CameraTakeDAO
     */
    public function findByTake($take)
    {
        return $this->repository->findOneBy(array('take' => $take));
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
