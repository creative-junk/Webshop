<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/14/2017
 ********************************************************************************/

namespace AppBundle\Controller\Grower;


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
 * @Route("/grower")
 * @Security("is_granted('ROLE_GROWER')")
 *
 */
class GrowerController extends Controller
{

    /**
     * @Route("/product/",name="grower_product_list")
     */
    public function listAction(){

        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveProductsOrderByDate();

        return $this->render('grower/product/list.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/product/my",name="my_grower_product_list")
     */
    public function myProductListAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllMyActiveProductsOrderByDate($user);

        return $this->render('grower/product/mylist.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/product/new",name="grower_product_new")
     */
    public function newAction(Request $request)
    {

        $form = $this->createForm(ProductFormType::class);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product Created Successfully!');

            return $this->redirectToRoute('my_grower_product_list');
        }

        return $this->render('grower/product/new.html.twig',[
            'productForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/product/{id}/edit",name="grower_product_edit")
     */
    public function editAction(Request $request,Product $product)
    {
        $form = $this->createForm(ProductFormType::class,$product);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product Updated Successfully!');

            return $this->redirectToRoute('my_grower_product_list');
        }

        return $this->render('grower/product/edit.html.twig',[
            'productForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/orders/",name="grower_order_list")
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
     * @Route("/orders/my",name="my_grower_order_list")
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
     * @Route("/orders/{id}/update",name="grower_order_update")
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

            return $this->redirectToRoute('grower_order_list');
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

}