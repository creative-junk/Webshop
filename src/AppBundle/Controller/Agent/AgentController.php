<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/14/2017
 ********************************************************************************/

namespace AppBundle\Controller\Agent;


use AppBundle\Entity\Auction;
use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\UserOrder;
use AppBundle\Form\addToCartFormType;
use AppBundle\Form\AgentProductForm;
use AppBundle\Form\AuctionProductForm;
use AppBundle\Form\ProductFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/agent")
 * @Security("is_granted('ROLE_AGENT')")
 *
 */
class AgentController extends Controller
{

    /**
     * @Route("/",name="agent_dashboard")
     */
    public function dashboardAction()
    {

        return $this->render(':agent:home.htm.twig');
        //dump($products);die;
        //return new Response('Product Saved');
    }

    /**
     * @Route("/product/my",name="my_assigned_product_list")
     */

    public function myAssignedProductListAction(Request $request = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Auction')
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

        return $this->render('agent/product/mylist.html.twig', [
            'products' => $result,
        ]);

    }

    /**
     * @Route("/product/{id}/edit",name="assigned_product_edit")
     */
    public function editAssignedProductAction(Request $request, Auction $product)
    {
        $form = $this->createForm(AgentProductForm::class, $product);

        //only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product in Auction Updated Successfully!');

            return $this->redirectToRoute('my_assigned_product_list');
        }

        return $this->render('agent/product/edit.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/orders/",name="agent_order_list")
     */
    public function ordersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $orders = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyReceivedOrdersOrderByDate($user);
        return $this->render('agent/order/list.html.twig', [
            'orders' => $orders,
        ]);

    }
    /**
     * @Route("/orders/my",name="my_agent_order_list")
     */
    public function myOrdersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $orders = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyOrdersOrderByDate($user);
        return $this->render('agent/order/mylist.html.twig', [
            'orders' => $orders,
        ]);

    }
    /**
     * @Route("/orders/{id}/update",name="agent_order_update")
     */
    public function updateOrderStatusAction(Request $request,UserOrder $order)
    {
        $form = $this->createForm(ProductFormType::class,$order);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Order Status Updated Successfully!');

            return $this->redirectToRoute('agent_order_list');
        }

        return $this->render('grower/order/update.html.twig',[
            'productForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/auction/",name="agent_auction_product_list")
     */
    public function auctionListAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Auction')
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

        return $this->render('agent/auction/list.htm.twig', [
            'products' => $result,
        ]);


    }
    /**
     * @Route("/auction/{id}/view",name="agent_auction_product_details")
     */
    public function auctionProductDetailsAction(Request $request, Auction $product)
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

            return $this->redirectToRoute('agent_auction_product_list');
        }
        return $this->render('agent/auction/product-details.htm.twig', [
            'product' => $product,
            'form' => $form->createView()

        ]);
    }
    /**
     * @Route("/auction/{id}/buy",name="agent_auction_buy")
     */
    public function buyAction(Request $request, Product $product)
    {
        return $this->render('agent/auction/buy.htm.twig');
    }
    /**
     * @Route("/growers",name="agent_growers_list")
     */
    public function agentGrowersAction(Request $request = null)
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

        return $this->render('agent/growers/list.html.twig', [
            'agents' => $result,
        ]);

    }

    /**
     * @Route("/growers/{id}/view",name="agent_grower_profile")
     */
    public function growerProfileAction()
    {
        return $this->render('agent/growers/view.htm.twig');
    }

    /**
     * @Route("/buyers",name="agent_buyer_list")
     */
    public function buyerListAction(Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->andWhere('user.isActive = :isActive')
            ->setParameter('isActive', true)
            ->andWhere('user.userType = :userType')
            ->setParameter('userType', 'buyer');

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

        return $this->render('agent/buyers/list.html.twig', [
            'agents' => $result,
        ]);
    }

    /**
     * @Route("/buyers/{id}/view",name="agent_buyer_profile")
     */
    public function buyerProfileAction()
    {
        return $this->render('agent/buyers/view.htm.twig');
    }


}