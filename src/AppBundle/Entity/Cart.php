<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/14/2017
 ********************************************************************************/

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 * @ORM\Table(name="cart")
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $cartCurrency;
    /**
     * @ORM\Column(type="string")
     */
    private $cartAmount;
    /**
     * @ORM\Column(type="integer")
     */
    private $nrItems;
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $ownedBy;

    /**
     * @return mixed
     */
    public function getCartCurrency()
    {
        return $this->cartCurrency;
    }

    /**
     * @param mixed $cartCurrency
     */
    public function setCartCurrency($cartCurrency)
    {
        $this->cartCurrency = $cartCurrency;
    }

    /**
     * @return mixed
     */
    public function getCartAmount()
    {
        return $this->cartAmount;
    }

    /**
     * @param mixed $cartAmount
     */
    public function setCartAmount($cartAmount)
    {
        $this->cartAmount = $cartAmount;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    /**
     * @param mixed $ownedBy
     */
    public function setOwnedBy(User $ownedBy)
    {
        $this->ownedBy = $ownedBy;
    }

    /**
     * @return mixed
     */
    public function getNrItems()
    {
        return $this->nrItems;
    }

    /**
     * @param mixed $nrItems
     */
    public function setNrItems($nrItems)
    {
        $this->nrItems = $nrItems;
    }

}