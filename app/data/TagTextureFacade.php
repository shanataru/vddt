<?php

namespace App\Data;
use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;

class TagTextures extends ADBF {
	
    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\TagTextureDAO::class);
    }

    public function makeNew($tag, $texture)
    {
        $new = new Entities\TagTextureDAO($tag, $texture);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }

    /**
     * @param $id
     * @return mixed|null|object
     */
    public function findById($id)
    {
	   return $this->repository->findOneBy(array('id' => $id));
    }

    /**
     * @param $tag
     * @return mixed|null|object
     */
    public function findByTag($tag)
    {
	   return $this->repository->findBy(array('tag' => $tag));
    }

    /**
     * @param $texture
     * @return mixed|null|object
     */
    public function findByTexture($texture)
    {
	   return $this->repository->findBy(array('texture' => $texture));
    }
    
    /**
     * @param $tag
     * @param $texture
     * @return mixed|null|object
     */
    public function findByTagTexture($tag, $texture)
    {
	   return $this->repository->findBy(array('tag' => $tag, 'texture' => $texture));
    }

    public function delete($tagTextureId)
    {
        $tagTexture = $this->repository->findBy(array('id' => $tagTextureId));
		$this->entityManager->remove($tagTexture);
		$this->entityManager->flush();
		return true;
    }

    
}
