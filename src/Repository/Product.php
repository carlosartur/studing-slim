<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class Product extends EntityRepository
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
        $this->createQueryBuilder("product");

        if (isset($searchParameters["name"])) {
            $this->queryBuilder
                ->andWhere($this->expr->like(
                    "product.name",
                    $this->expr->literal("%{$searchParameters["name"]}%")
                ));
        }

        if (isset($searchParameters["slug"])) {
            $this->queryBuilder
                ->andWhere($this->expr->like(
                    "product.slug",
                    $this->expr->literal("%{$searchParameters["slug"]}%")
                ));
        }

        if (isset($searchParameters["maxprice"])) {
            $this->queryBuilder
                ->andWhere($this->expr->lte(
                    "product.price",
                    $this->expr->literal($searchParameters["maxprice"])
                ));
        }

        if (isset($searchParameters["maxstock"])) {
            $this->queryBuilder
                ->andWhere($this->expr->lte(
                    "product.stock",
                    $this->expr->literal($searchParameters["maxstock"])
                ));
        }

        if (isset($searchParameters["minprice"])) {
            $this->queryBuilder
                ->andWhere($this->expr->gte(
                    "product.price",
                    $this->expr->literal($searchParameters["minprice"])
                ));
        }

        if (isset($searchParameters["minstock"])) {
            $this->queryBuilder
                ->andWhere($this->expr->gte(
                    "product.stock",
                    $this->expr->literal($searchParameters["minstock"])
                ));
        }

        return $this->queryBuilder
            ->orderBy('product.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
