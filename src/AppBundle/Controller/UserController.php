<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register",name="user_register")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserRegistrationForm::class);


        $form->handleRequest($request);
        if($form->isValid()){
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success','Welcome '.$user->getEmail().' to Iflora');

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }
        return $this->render('user/register.htm.twig',[
            'form' =>$form->createView()
        ]);
    }
    /**
     * @Route("/forgot-password",name="password_restore")
     */
    public function forgotPasswordAction(){
        return $this->render('home.htm.twig');
    }
    /**
     * @Route("/",name="homepage")
     */
    public function homeAction(){
        return $this->render('home.htm.twig');
    }
    /**
     * @Route("/about",name="about")
     */
    public function aboutAction(){
        return $this->render('about.htm.twig');
    }
    /**
     * @Route("/contact",name="contact")
     */
    public function contactAction(){
        return $this->render('contact.htm.twig');
    }

    /**
     * @Route("/home",name="home")
     */
    public function userHomeAction(){
        return $this->render('home/home.htm.twig');
    }
    /**
     * @Route("/home/profile",name="my_profile")
     */
    public function profileAction(){
        return $this->render('home.htm.twig');
    }
    /**
     * @Route("/home/orders/",name="my_order_list")
     */
    public function myOrdersListAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em=$this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:UserOrder')
            ->findAllMyOrdersOrderByDate($user);
        return $this->render('home/order/list.html.twig',[
            'products'=>$products,
        ]);

    }
}
