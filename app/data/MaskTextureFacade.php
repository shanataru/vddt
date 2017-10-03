<?php

namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;
use Nette\Utils\Paginator;

class MaskTextures extends ADBF {

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\MaskTextureDAO::class);
    }

    public function makeNew($mask, $texture)
    {
	   $new = new Entities\MaskTextureDAO($mask, $texture);
	   $this->entityManager->persist($new);
	   $this->entityManager->flush();
	   return $new;
    }

    /**
     * @param $texture
     * @return null|Entities\MaskTextureDAO
     */
    public function findByTexture($texture)
    {
	   return $this->repository->findOneBy(array('texture' => $texture));
    }

    /**
     * @param $mask
     * @return null|Entities\MaskTextureDAO
     */
    public function findByMask($mask)
    {
	   return $this->repository->findOneBy(array('mask' => $mask));
    }

    /**
     * @param $id
     * @return mixed|null|object
     */
    public function findById($id)
    {
	   return $this->repository->findOneBy(array('id' => $id));
    }

    public function countByMaterialId($materialId)
    {
	   $selection = $this->repository->findBy(array('texture' => $materialId));
	   return count($selection);
    }

    public function countByMaskId($maskId)
    {
        $selection = $this->repository->findBy(array('mask' => $maskId));
        return count($selection);
    }

    public function findAllByMask($maskId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('mask' => $maskId), $order, $paginator);
    }
    
    public function findAllByMaterial($materialId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('texture' => $materialId), $order, $paginator);
    }

    public function findByMaskTexture($mask, $texture)
    {
       return $this->repository->findBy(array('mask' => $mask, 'texture' => $texture));
    }

    public function delete($maskTextureId)
    {
        $maskTexture = $this->repository->findBy(array('id' => $maskTextureId));
        $this->entityManager->remove($maskTexture);
        $this->entityManager->flush();
        return true;
    }

}
