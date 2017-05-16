<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/27/2017
 ********************************************************************************/

namespace AppBundle\Controller\User;


use AppBundle\Entity\Auction;
use AppBundle\Entity\BillingAddress;
use AppBundle\Entity\Cart;
use AppBundle\Entity\CartItems;
use AppBundle\Entity\GrowersList;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\AddGrowerForm;
use AppBundle\Form\addToCartFormType;
use AppBundle\Form\CheckoutForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            return new Response(null, 204);
            //return $this->redirectToRoute('buyer_shop');
        }
        return $this->render('home/product-details.htm.twig', [
            'product' => $product,
            'form' => $form->createView()

        ]);
    }

    /**
     * @Route("/auction/",name="buyer_auction")
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

        return $this->render('buyer/auction/list.htm.twig', [
            'products' => $result,
        ]);


    }

    /**
     * @Route("/auction/{id}/view",name="buyer_auction_product_details")
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
        return $this->render('buyer/auction/product-details.htm.twig', [
            'product' => $product,
            'form' => $form->createView()

        ]);
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
        $grower = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $buyerGrowers = $em->getRepository('AppBundle:BuyerGrower')
            ->findBy([
                'listOwner' => $grower
            ]);
        $growerIds = array();

        if ($buyerGrowers) {

            foreach ($buyerGrowers as $buyerGrower) {
                $growerIds[] = $buyerGrower->getGrower();
            }
        }else{
            $growerIds[] = 1;
        }

        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->andWhere('user.id NOT IN (:growers)')
            ->setParameter('growers',$growerIds)
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
     * @Route("/growers/{id}/view",name="view_growers")
     */
    public function addGrowerAction(Request $request, User $grower)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $growersList = new GrowersList();
        $growersList->setWhoseList($user);
        $growersList->setStatus("Requested");
        $growersList->setGrower($grower);

        $form = $this->createForm(addGrowerForm::class, $growersList);

        //only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ($this->listNotExists($user, $grower))) {
            $growersList = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($growersList);
            $em->flush();

            $this->addFlash('success', 'Grower Successfully Requested!');
            return $this->redirectToRoute('buyer_growers');
        } else {
            $this->addFlash('success', 'Grower Already Requested!');

        }
        return $this->render('home/growers/grower-details.htm.twig', [
            'grower' => $grower,
            'form' => $form->createView()

        ]);

    }
    /**
     * @Route("/agents",name="buyer_agents")
     */
    public function buyerAgentsAction(Request $request = null)
    {
        $buyer = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $buyerAgents = $em->getRepository('AppBundle:BuyerAgent')
            ->findBy([
                'listOwner' => $buyer
            ]);
        $agentIds = array();

        if ($buyerAgents) {

            foreach ($buyerAgents as $buyerAgent) {
                $agentIds[] = $buyerAgent->getAgent();
            }
        }else{
            $agentIds[] = 1;
        }
        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->andWhere('user.id NOT IN (:agents)')
            ->setParameter('agents',$agentIds)
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
     * @Route("/agents/roses/{id}",name="buyer_view_agent_roses")
     */
    public function agentAuctionRosesAction(){
        return $this->render('home/agents/auction.html.twig');
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
    /**
     * @Route("/requests/my/growers",name="my_buyer_grower_requests")
     */
    public function getMyGrowerRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:BuyerGrower')
            ->createQueryBuilder('user')
            ->andWhere('user.status = :isAccepted')
            ->setParameter('isAccepted', 'Requested')
            ->andWhere('user.listOwner = :whoOwnsList')
            ->setParameter('whoOwnsList', $user);

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

        return $this->render('home/growers/myRequests.htm.twig', [
            'growerRequests' => $result,
        ]);
    }
    /**
     * @Route("/requests/my/agents",name="my_buyer_agent_requests")
     */
    public function getMyAgentRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:BuyerAgent')
                                ->getMyAgentRequests($user);
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        return $this->render('home/agents/myRequests.htm.twig', [
            'agentRequests' => $result,
        ]);
    }
    /**
     * @Route("/requests/growers",name="buyer_grower_requests")
     */
    public function getGrowerRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:BuyerGrower')
                    ->getGrowerRequestsQuery($user);
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        return $this->render('home/growers/requests.html.twig', [
            'breederRequests' => $result,
        ]);
    }
    /**
     * @Route("/requests/agents",name="buyer_agent_requests")
     */
    public function getAgentRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:BuyerAgent')
                        ->getAgentRequestsQuery($user);

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        return $this->render('home/agents/requests.html.twig', [
            'agentRequests' => $result,
        ]);
    }
    /**
     * @Route("/cart",name="buyer-cart")
     */
    public function growerCartAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('AppBundle:Cart')
            ->findMyCart($user);
        if ($cart) {
            $cartItems = $em->getRepository('AppBundle:CartItems')
                ->findAllItemsInMyCartOrderByDate($cart[0]);
        } else {
            $cartItems = "";
        }
        return $this->render('cart.htm.twig', [
            'cartItems' => $cartItems,
            'cart' => $cart[0]
        ]);
    }

    /**
     * @Route("/checkout",name="buyer_checkout")
     */
    public function checkoutAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $billingAddress = new BillingAddress();
        $billingAddress->setUser($user);
        $billingAddress->setFirstName($user->getFirstName());
        $billingAddress->setLastName($user->getLastName());
        $billingAddress->setEmailAddress($user->getUserName());

        $form = $this->createForm(CheckoutForm::class, $billingAddress);

        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('AppBundle:Cart')
            ->findMyCart($user);


        return $this->render(':partials/iflora/user:checkout.htm.twig', [
            'buyerCheckoutForm' => $form->createView(),
            'cart' => $cart[0]
        ]);
    }
    public function getTotalRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();
        $nrBreederRequests = $em->getRepository('AppBundle:BuyerGrower')
            ->getNrGrowerRequests($user);

        $nrAgentRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrAgentRequests($user);
        $totalRequests += $nrBreederRequests;
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getTotalGrowerRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();
        $nrGrowerRequests = $em->getRepository('AppBundle:BuyerGrower')
            ->getNrGrowerRequests($user);

        $totalRequests += $nrGrowerRequests;


        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getTotalAgentRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();

        $nrAgentRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrAgentRequests($user);
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getMyTotalRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();
        $nrBreederRequests = $em->getRepository('AppBundle:BuyerGrower')
            ->getNrMyGrowerRequests($user);

        $nrAgentRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrMyAgentRequests($user);
        $totalRequests += $nrBreederRequests;
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getMyTotalGrowerRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();
        $nrBreederRequests = $em->getRepository('AppBundle:BuyerGrower')
            ->getNrMyGrowerRequests($user);

        $totalRequests += $nrBreederRequests;


        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getMyTotalAgentRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();

        $nrAgentRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrMyAgentRequests($user);
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }

}