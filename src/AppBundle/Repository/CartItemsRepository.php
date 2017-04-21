<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/14/2017
 ********************************************************************************/

namespace AppBundle\Repository;


use AppBundle\Entity\Cart;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class CartItemsRepository extends EntityRepository
{


    public function findAllItemsInMyCartOrderByDate(Cart $cart){
        return $this->createQueryBuilder('cartitems')
            ->andWhere('cartitems.cart= :ownedBy')
            ->setParameter('createdBy',$cart)
            ->orderBy('product.createdAt','DESC')
            ->getQuery()
            ->execute();
    }


}