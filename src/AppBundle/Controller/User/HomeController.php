<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/27/2017
 ********************************************************************************/

namespace AppBundle\Controller\User;


use AppBundle\Entity\Cart;
use AppBundle\Entity\CartItems;
use AppBundle\Entity\Product;
use AppBundle\Form\addToCartFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/home")
 * @Security("is_granted('ROLE_BUYER')")
 *
 */
class HomeController extends Controller
{
    /**
     * @Route("/",name="home")
     */
    public function userHomeAction()
    {
        return $this->render('home/home.htm.twig');
    }

    /**
     * @Route("/home/profile",name="my_profile")
     */
    public function profileAction()
    {
        return $this->render('home.htm.twig');
    }

    /**
     * @Route("/home/wishlist",name="my_wishlist")
     */
    public function wishlistAction()
    {
        return $this->render('home.htm.twig');
    }

    /**
     * @Route("/home/compare",name="my_compare")
     */
    public function compareAction()
    {
        return $this->render('home.htm.twig');
    }

    /**
     * @Route("/market/",name="buyer_shop")
     */
    public function buyerShopGridAction(Request $request = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $cart = new Cart();

        $cart->setOwnedBy($user);

        $form = $this->createForm(addToCartFormType::class, $cart);

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Product')
            ->createQueryBuilder('product')
            ->andWhere('product.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('product.createdAt', 'DESC');
        $query = $queryBuilder->getQuery();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );


        return $this->render('home/shop.htm.twig', [
            'products' => $result,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/market/{id}/view",name="product_details")
     */
    public function showAction(Request $request, Product $product)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $cart = new Cart();

        $cart->setOwnedBy($user);

        $form = $this->createForm(addToCartFormType::class, $cart);

        //only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $existingCart = $em->getRepository('AppBundle:Cart')
                ->findMyCart($user);
            $quantity = $request->request->get('quantity');
            $price = $request->request->get('productPrice');
            $currency = $request->request->get('productCurrency');

            //Create The cart Item
            $cartItem = new CartItems();
            $cartItem->setQuantity($quantity);
            $cartItem->setUnitPrice($price);
            $cartItem->setProduct($product);
            $lineTotal = ($price) * ($quantity);
            $cartItem->setLineTotal($lineTotal);

            //Update the Cart
            if ($existingCart) {
                $existingCart[0]->setCartAmount(($existingCart[0]->getCartAmount()) + ($lineTotal));
                $existingCart[0]->setNrItems(($existingCart[0]->getNrItems()) + $quantity);
                $cartItem->setCart($existingCart[0]);
                $em->persist($existingCart[0]);
            } else {
                $cart->setCartAmount($lineTotal);
                $cart->setNrItems($quantity);
                $cart->setCartCurrency($currency);
                $cartItem->setCart($cart);
                $em->persist($cart);
            }
            $em->persist($cartItem);
            $em->flush();

            $this->addFlash('success', 'Product Successfully Added to Cart!');

            return $this->redirectToRoute('buyer_shop');
        }
        return $this->render('home/product-details.htm.twig', [
            'product' => $product,
            'form' => $form->createView()

        ]);
    }

    /**
     * @Route("/auction",name="buyer_auction")
     */
    public function buyerAuctionAction()
    {
        return $this->render('home/home.htm.twig');
    }

    /**
     * @Route("/roses",name="buyer_roses")
     */
    public function buyerRosesAction()
    {
        return $this->render('home/home.htm.twig');
    }

    /**
     * @Route("/growers",name="buyer_growers")
     */
    public function buyerGrowersAction(Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->andWhere('user.isActive = :isActive')
            ->setParameter('isActive', true)
            ->andWhere('user.userType = :userType')
            ->setParameter('userType', 'grower');

        $query = $queryBuilder->getQuery();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        return $this->render('home/growers/list.html.twig', [
            'growers' => $result,
        ]);

    }

    /**
     * @Route("/agents",name="buyer_agents")
     */
    public function buyerAgentsAction(Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->andWhere('user.isActive = :isActive')
            ->setParameter('isActive', true)
            ->andWhere('user.userType = :userType')
            ->setParameter('userType', 'agent');

        $query = $queryBuilder->getQuery();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        return $this->render('home/agents/list.html.twig', [
            'agents' => $result,
        ]);

    }

    /**
     * @Route("/agents/{id}/view",name="view_agent")
     */
    public function agentProfileActionAction()
    {

        return $this->render('home/agents/view.htm.twig');

    }

    /**
     * @Route("/growers/{id}/view",name="view_grower")
     */
    public function growerProfileActionAction()
    {

        return $this->render('home/growers/view.htm.twig');

    }
    /**
     * @Route("/orders/",name="my_order_list")
     */
    public function ordersListAction()
    {

        return $this->render(':home:order.htm.twig');

    }

    /**
     * @Route("/orders/my",name="order_list")
     */
    public function myOrdersListAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyOrdersOrderByDate($user);
        return $this->render('home/order/list.html.twig', [
            'orders' => $orders,
        ]);

    }
}