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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartItemsRepository")
 * @ORM\Table(name="cart_items")
 */
class CartItems
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
    private $quantity;
    /**
     * @ORM\Column(type="string")
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="string")
     */
    private $lineTotal;
    /**
     * @ORM\ManyToOne(targetEntity="Cart",inversedBy="cartItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cart;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param mixed $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return mixed
     */
    public function getLineTotal()
    {
        return $this->lineTotal;
    }

    /**
     * @param mixed $lineTotal
     */
    public function setLineTotal($lineTotal)
    {
        $this->lineTotal = $lineTotal;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}