<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/14/2017
 ********************************************************************************/

namespace AppBundle\Controller\Breeder;


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
 * @Route("/breeder")
 * @Security("is_granted('ROLE_BREEDER')")
 *
 */
class BreederController extends Controller
{

    /**
     * @Route("/",name="breeder_dashboard")
     */
    public function dashboardAction(){

        return $this->render(':breeder:dashboard.htm.twig');
        //dump($products);die;
        //return new Response('Product Saved');
    }
    /**
     * @Route("/product/",name="breeder_product_list")
     */
    public function listAction(){

        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveProductsOrderByDate();

        return $this->render('breeder/product/list.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/seedling/my",name="my_breeder_seedling_list")
     */
    public function myProductListAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllMyActiveProductsOrderByDate($user);

        return $this->render('breeder/seedlings/mylist.html.twig',[
            'products'=>$products,
        ]);

    }
    /**
     * @Route("/seedling/new",name="breeder_seedlings_new")
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $product->setUser($this->get('security.token_storage')->getToken()->getUser());
        $product->setIsActive(true);
        $product->setIsAuthorized(true);
        $product->setIsFeatured(false);
        $product->setIsOnSale(false);
        $product->setIsSeedling(true);

        $form = $this->createForm(ProductFormType::class, $product);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product Created Successfully!');

            return $this->redirectToRoute('my_breeder_seedling_list');
        }

        return $this->render('breeder/seedlings/new.html.twig',[
            'productForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/seedling/{id}/edit",name="breeder_seedling_edit")
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

            return $this->redirectToRoute('my_breeder_seedling_list');
        }

        return $this->render('breeder/seedlings/edit.html.twig',[
            'productForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/orders/",name="breeder_order_list")
     */
    public function ordersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $orders = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyReceivedOrdersOrderByDate($user);
        return $this->render('breeder/order/list.html.twig', [
            'orders' => $orders,
        ]);

    }
    /**
     * @Route("/orders/my",name="my_seedlings_order_list")
     */
    public function myOrdersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $orders = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyOrdersOrderByDate($user);
        return $this->render('breeder/order/mylist.html.twig', [
            'orders' => $orders,
        ]);

    }
    /**
     * @Route("/orders/{id}/update",name="breeder_order_update")
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


}