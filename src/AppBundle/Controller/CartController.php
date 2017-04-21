<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\addToCartFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    /**
     * @Route("/cart",name="cart_list")
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em=$this->getDoctrine()->getManager();
        $cart = $em->getRepository('AppBundle:Cart')
            ->findMyCart($user);
        if($cart) {
            $cartItems = $em->getRepository('AppBundle:CartItems')
                ->findAllItemsInMyCartOrderByDate($cart);
        }else{
            $cartItems="";
        }
        return $this->render('cart.htm.twig',[
            'cartItems'=>$cartItems,
        ]);
    }

    /**
     * @Route("/mini",name="mini-cart")
     */
    public function cartAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em=$this->getDoctrine()->getManager();
        $cart = $em->getRepository('AppBundle:Cart')
            ->findMyCart($user);
        return $this->render(':partials:minicart.html.twig',[
            'cart'=>$cart,
        ]);
    }

    /**
     * @Route("/shop/add-to-cart/",name="add-to-cart")
     */
    public function addToCartAction(Request $request){
        $form = $this->createForm(addToCartFormType::class);

        //only handles data on POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           /* $cart = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($cartItem);
            $em->flush();

            $this->addFlash('success','Product Created, Yaay!');

            return $this->redirectToRoute('admin_product_list');*/
        }

        return $this->render(':partials:add-to-cart.htm.twig',[
            'form' => $form->createView()
        ]);

    }

}
