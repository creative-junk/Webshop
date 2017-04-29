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
use AppBundle\Entity\Product;
use AppBundle\Entity\UserOrder;
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
     * @Route("/product/",name="agent_product_list")
     */
    public function listAction(){

        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveProductsOrderByDate();

        return $this->render('agent/product/list.html.twig',[
            'products'=>$products,
        ]);

    }

    /**
     * @Route("/orders/",name="agent_order_list")
     */
    public function ordersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyReceivedOrdersOrderByDate($user);
        return $this->render('grower/order/list.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/orders/my",name="my_agent_order_list")
     */
    public function myOrdersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyOrdersOrderByDate($user);
        return $this->render('grower/order/list.html.twig',[
            'products'=>$products,
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
     * @Route("/auction/",name="auction_product_list")
     */
    public function auctionListAction(){

        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('Auction.php')
            ->findAllActiveAuctionProductsOrderByDate();

        return $this->render('grower/product/list.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/auction/my/products",name="my_grower_auction_list")
     */
    public function myAuctionProductListAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('Auction.php')
            ->findAllMyActiveAuctionProductsOrderByDate($user);

        return $this->render('grower/auction/product/mylist.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/auction/product/new",name="grower_auction_new")
     */
    public function newAuctionProductAction(Request $request)
    {

        $form = $this->createForm(AuctionProductForm::class);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product Posted to Auction Successfully!');

            return $this->redirectToRoute('my_grower_product_list');
        }

        return $this->render('grower/auction/product/new.html.twig',[
            'productForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/auction/product/{id}/edit",name="grower_auction_product_edit")
     */
    public function editAuctionProductAction(Request $request,Auction $product)
    {
        $form = $this->createForm(AuctionProductForm::class,$product);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product in Auction Updated Successfully!');

            return $this->redirectToRoute('my_grower_auction_list');
        }

        return $this->render('grower/auction/product/edit.html.twig',[
            'productForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/growers",name="breeder_growers_list")
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
            'growers' => $result,
        ]);

    }

    /**
     * @Route("/growers/{id}/view",name="grower_profile")
     */
    public function breederProfileAction()
    {
        return $this->render('agent/growers/view.htm.twig');
    }

    /**
     * @Route("/buyers",name="grower_buyer_list")
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



}