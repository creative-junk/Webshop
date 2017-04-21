<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/14/2017
 ********************************************************************************/

namespace AppBundle\Repository;


use AppBundle\Entity\Auction;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @return Product[]
     */
    public function findAllActiveProductsOrderByDate(){
        return $this->createQueryBuilder('product')
            ->andWhere('product.isActive = :isActive')
            ->setParameter('isActive',true)
            ->orderBy('product.createdAt','DESC')
            ->getQuery()
            ->execute();
    }
    /**
     * @return Product[]
     */
    public function findAllMyActiveProductsOrderByDate(User $user){
        return $this->createQueryBuilder('product')
            ->andWhere('product.isActive = :isActive')
            ->setParameter('isActive',true)
            ->andWhere('product.user= :createdBy')
            ->setParameter('createdBy',$user)
            ->andWhere('product.isSeedling= :isSeedling')
            ->setParameter('isSeedling',false)
            ->orderBy('product.createdAt','DESC')
            ->getQuery()
            ->execute();
    }
    /**
     * @return Product[]
     */
    public function findAllActiveFeaturedProductsOrderByDate(){
        return $this->createQueryBuilder('product')
            ->andWhere('product.isActive = :isActive')
            ->setParameter('isActive',true)
            ->andWhere('product.isFeatured = :isFeatured')
            ->setParameter('isFeatured',true)
            ->orderBy('product.createdAt','DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->execute();
    }
    /**
     * @return Product[]
     */
    public function findAllActiveNewProductsOrderByDate(){
        return $this->createQueryBuilder('product')
            ->andWhere('product.isActive = :isActive')
            ->setParameter('isActive',true)
            ->andWhere('product.createdAt >= :isNew')
            ->setParameter('isNew', 'NOW() - INTERVAL 3 MONTH')
            ->orderBy('product.createdAt','DESC')
            ->getQuery()
            ->execute();
    }

}