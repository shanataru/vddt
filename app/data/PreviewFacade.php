<?php
namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;

class Previews extends ADBF
{

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Entities\PreviewDAO::class);
    }
    
    public function makeNew($path, $note)
    {
        $new = new Entities\PreviewDAO($path, $note);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }

    
    /**
     * @param $path
     * @return null|Entities\PreviewDAO
     */
    public function findByPath($path)
    {
        return $this->repository->findOneBy(array('path' => $path));
    }
    
    /**
     * @param $id
     * @return mixed|null|object
     */
    public function findById($id)
    {
        return $this->repository->findOneBy(array('id' => $id));
    }
    
    public function delete($previewlId)
    {
        $preview = $this->repository->findBy(array('id' => $previewlId));
        $this->entityManager->remove($preview);
        $this->entityManager->flush();
    }


}
