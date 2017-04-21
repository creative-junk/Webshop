<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/shop/",name="products")
     *
     */
    public function indexAction()
    {
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveProductsOrderByDate();

        return $this->render('shop.htm.twig',[
            'products'=>$products,
        ]);
    }
    /**
     * @Route("/product/grid",name="grid_products")
     *
     */
    public function gridProductsAction()
    {
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveProductsOrderByDate();

        return $this->render(':partials:shop-grid-view.htm.twig',[
            'products'=>$products,
        ]);
    }
    /**
     * @Route("/product/list",name="list_products")
     *
     */
    public function listProductsAction()
    {
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveProductsOrderByDate();

        return $this->render(':partials:shop-list-view.htm.twig',[
            'products'=>$products,
        ]);
    }
    /**
     * @Route("product/featured",name="featured_products")
     */
    public function featuredProductsAction(){
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveFeaturedProductsOrderByDate();

        return $this->render('product/featured.html.twig',[
            'featuredProducts'=>$products,
        ]);
    }
    /**
     * @Route("product/new",name="new_products")
     */
    public function newProductsAction(){
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAllActiveNewProductsOrderByDate();

        return $this->render('product/featured.html.twig',[
            'featuredProducts'=>$products,
        ]);
    }
    /**
     * @Route("/shop/{id}/view")
     */
    public function showAction(Product $product){
        return $this->render('::product-details.htm.twig',[
            'product' => $product
        ]);
    }



}
