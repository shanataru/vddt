<?php

namespace App\Data;
use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;

class Tags extends ADBF {
	
    private $tagTextureFacade;
    
    public function __construct(Kdyby\Doctrine\EntityManager $entityManager, TagTextures $tagTexture)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\TagDAO::class);
        $this->tagTextureFacade = $tagTexture;
    }

    /* Vytvoří nový záznam o tagu */
    public function makeNew($name)
    {
        $new = new Entities\TagDAO($name);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }
    
    /* Přidá tag k textuře, pokud již je přiřazena, nic nedělá */
    public function addTagToTexture($tagId, $textureId)
    {
        $tagTextures = $this->tagTextureFacade->findByTagTexture($tagId, $textureId);
        if (!$tagTextures)
        {
            $this->tagTextureFacade->makeNew($tagId, $textureId);
        }
    }
    
    /* Odebere tag textuře, pokud ho textura nemá, pak nic nedělá */
    public function removeTagFromTexture($tagId, $textureId)
    {
        $tagTextures = $this->tagTextureFacade->findByTagTexture($tagId, $textureId);
        if ($tagTextures)
        {
            foreach($tagTextures as $tagTexture)
            {
                $this->tagTextureFacade->delete($tagTexture->id);   
            }
        }
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
     * @param $name
     * @return mixed|null|object
     */
    public function findByName($name)
    {
	   return $this->repository->findOneBy(array('name' => $name));
    }
    
    /**
     * @param $materialId
     * @return mixed|null|object
     */
    public function findByTexture($textureId)
    {
        $tagTextures = $this->tagTextureFacade->findByTexture($textureId);
        $tags = [];
        foreach($tagTextures as $tagTexture)
        {
            $tags[] = $this->repository->findOneBy(array('id' => $tagTexture->tag));
        }
        return $tags;
    }

}
