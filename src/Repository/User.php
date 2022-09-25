<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class User extends EntityRepository
{
    private QueryBuilder $queryBuilder;

    private Expr $expr;

    public function createQueryBuilder($alias, $indexBy = null)
    {
        $this->queryBuilder = parent::createQueryBuilder($alias);
        $this->expr = $this->queryBuilder->expr();
    }

    public function findByQueryParams($searchParameters)
    {
        $this->createQueryBuilder("user");
        if (isset($searchParameters["email"])) {
            $this->queryBuilder
                ->andWhere($this->expr->like(
                    "user.email",
                    $this->expr->literal("%{$searchParameters["email"]}%")
                ));
        }
        return $this->queryBuilder
            ->orderBy('user.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
