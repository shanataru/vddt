<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use App\Article;
use Nette;
use App\Data;
use App\Data\BaseFacade as ADBF;


class ArticlesFacade extends ADBF {
    private $articles;

    public function __construct(\Kdyby\Doctrine\EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->articles = $em->getDao(\App\Article::getClassName());
    }

    public function findBySlug($slug)
    {
        return $this->articles->findOneBy(array('id' => $slug));
    }
}