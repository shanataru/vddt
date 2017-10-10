<?php
namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;

class UserQuery extends Kdyby\Doctrine\QueryObject{ //a takovouto classu budeme mit pro kazdy vyhledavany objekt

    /**
     * @var array|\Closure[]
     */
    private $filter = [];

    /**
     * @var array|\Closure[]
     */
    private $select = [];

/* Zde bude mrte moc funkci ktere budou tvorit query

            todo

*/

    /**
     * @param \Kdyby\Persistence\Queryable $repository
     * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
     */
    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicDql($repository)
            ->addSelect('partial u.{id, name}');
        /*todo*/
        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb->addOrderBy('u.id', 'DESC');
    }


    protected function doCreateCountQuery(Queryable $repository)
    {
        return $this->createBasicDql($repository)->select('COUNT(u.id)');
    }



    private function createBasicDql(Queryable $repository)
    {
        $qb = $repository->createQueryBuilder()
            ->select('u')->from(Entities\UserDAO::class, 'u'); //sem dopsat co skutecne vybrat
        /*todo*/

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }
}