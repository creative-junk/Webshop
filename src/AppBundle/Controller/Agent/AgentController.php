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
use AppBundle\Entity\MyList;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\UserOrder;
use AppBundle\Form\addToCartFormType;
use AppBundle\Form\AgentProductForm;
use AppBundle\Form\AuctionProductForm;
use AppBundle\Form\ProductFormType;
use AppBundle\Form\RecommendFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/auction/{id}/recommend",name="agent_auction_recommend")
     */
    public function recommendRosesAction(Request $request, Auction $product)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(RecommendFormType::class);
        $em = $this->getDoctrine()->getManager();

        $agentBuyers = $em->getRepository('AppBundle:BuyerAgent')
            ->getMyAgentBuyers($user);

        //only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ids= $request->request->get('buyer');
            return $this->render('agent/auction/recommend.htm.twig',[
                'product'=>$product,
                'agentBuyers'=>$agentBuyers,
                'form'=>$form->createView(),
                'buyers'=>$ids
            ]);
        }

        return $this->render('agent/auction/recommend.htm.twig',[
            'product'=>$product,
            'agentBuyers'=>$agentBuyers,
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/growers",name="agent_growers_list")
     */
    public function agentGrowersAction(Request $request = null)
    {
        $agent = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $agentGrowers = $em->getRepository('AppBundle:GrowerAgent')
            ->findBy([
                'listOwner' => $agent
            ]);
        $growerIds = array();

        if ($agentGrowers) {

            foreach ($agentGrowers as $agentGrower) {
                $growerIds[] = $agentGrower->getGrower();
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

        return $this->render('agent/growers/list.html.twig', [
            'growers' => $result,
        ]);

    }

    /**
     * @Route("/growers/{id}/view",name="agent_grower_profile")
     */
    public function growerProfileAction(Request $request, User $grower)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();


        $products = $grower->getProducts();
        $nrproducts = $em->getRepository('AppBundle:Product')
            ->findMyActiveProducts($grower);
        $nrAuctionProducts = $em->getRepository('AppBundle:Auction')
            ->findMyActiveAuctionProducts($grower);

        return $this->render('agent/growers/view.htm.twig', [
            'grower' => $grower,
            'products'=>$products,
            'nrProducts' => $nrproducts,
            'nrAuctionProducts' => $nrAuctionProducts
        ]);

    }

    /**
     * @Route("/buyers",name="agent_buyer_list")
     */
    public function buyerListAction(Request $request = null)
    {
        $agent = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $agentBuyers = $em->getRepository('AppBundle:BuyerAgent')
            ->findBy([
                'listOwner' => $agent
            ]);
        $buyerIds = array();

        if ($agentBuyers) {

            foreach ($agentBuyers as $agentBuyer) {
                $buyerIds[] = $agentBuyer->getBuyer();
            }
        }else{
            $buyerIds[] = 1;
        }

        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->andWhere('user.id NOT IN (:buyers)')
            ->setParameter('buyers',$buyerIds)
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
            'buyers' => $result,
        ]);
    }



    public function getTotalBuyerRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();

        $nrAgentRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrBuyerRequests($user);
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getMyTotalBuyerRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();

        $nrAgentRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrMyBuyerRequests($user);
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    /**
     * @Route("/buyers/requests/my",name="my_agent_buyer_requests")
     */
    public function getMyBuyerRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:BuyerAgent')
            ->getMyBuyerRequests($user);
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        return $this->render('agent/buyers/myRequests.htm.twig', [
            'buyerRequests' => $result,
        ]);
    }
    /**
     * @Route("/growers/requests/my",name="my_agent_grower_requests")
     */
    public function getMyGrowerRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:GrowerAgent')
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

        return $this->render('agent/growers/myRequests.htm.twig', [
            'growerRequests' => $result,
        ]);
    }
    /**
     * @Route("/growers/requests/",name="agent_grower_requests")
     */
    public function getGrowerRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $whoseListIds[]=array();
        $whoseListIds[]=$user;
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:GrowerAgent')
            ->createQueryBuilder('user')
            ->andWhere('user.status = :isAccepted')
            ->setParameter('isAccepted', 'Requested')
            ->andWhere('user.agent = :whoIsAgent')
            ->setParameter('whoIsAgent', $user)
            ->andWhere('user.listOwner NOT IN (:agents)')
            ->setParameter('agents',$whoseListIds);

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


        return $this->render('agent/growers/requests.html.twig', [
            'growerRequests' => $result,
        ]);
    }
    public function getMyTotalGrowerRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();

        $nrGrowerRequests = $em->getRepository('AppBundle:GrowerAgent')
            ->getNrMyGrowerRequests($user);
        $totalRequests += $nrGrowerRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    /**
     * @Route("/buyers/requests/",name="agent_buyer_requests")
     */
    public function getBuyerRequestsAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $whoseListIds[]=array();
        $whoseListIds[]=$user;

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:BuyerAgent')
            ->createQueryBuilder('user')
            ->andWhere('user.status = :isAccepted')
            ->setParameter('isAccepted', 'Requested')
            ->andWhere('user.agent = :whoIsAgent')
            ->setParameter('whoIsAgent', $user)
            ->andWhere('user.listOwner NOT IN (:agents)')
            ->setParameter('agents',$whoseListIds);

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


        return $this->render('agent/buyers/requests.html.twig', [
            'buyerRequests' => $result,
        ]);
    }
    /**
     * @Route("/buyers/{id}/view",name="agent_buyer_profile")
     */
    public function buyerProfileAction()
    {
        return $this->render('agent/buyers/view.htm.twig');
    }

    /**
     * @Route("/buyers/my/",name="my-agent-buyers")
     */
    public function myBuyersAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:BuyerAgent')
            ->createQueryBuilder('user')
            ->andWhere('user.status = :isAccepted')
            ->setParameter('isAccepted', 'Accepted')
            ->andWhere('user.agent = :whoIsAgent')
            ->setParameter('whoIsAgent', $user);

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
        return $this->render('agent/buyers/mylist.html.twig', [
            'agentBuyers' => $result,
        ]);
    }
    /**
     * @Route("/growers/my/",name="my-agent-growers")
     */
    public function myGrowersAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:GrowerAgent')
            ->createQueryBuilder('user')
            ->andWhere('user.status = :isAccepted')
            ->setParameter('isAccepted', 'Accepted')
            ->andWhere('user.agent = :whoIsAgent')
            ->setParameter('whoIsAgent', $user);

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
        return $this->render('agent/growers/mylist.html.twig', [
            'agentGrowers' => $result,
        ]);
    }
    public function getTotalGrowerRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();

        $nrAgentRequests = $em->getRepository('AppBundle:GrowerAgent')
            ->getNrGrowerRequests($user);
        $totalRequests += $nrAgentRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getTotalRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;
        $em = $this->getDoctrine()->getManager();
        $nrGrowerRequests = $em->getRepository('AppBundle:GrowerAgent')
            ->getNrGrowerRequests($user);

        $nrBuyerRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrBuyerRequests($user);

        $totalRequests += $nrGrowerRequests;
        $totalRequests += $nrBuyerRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getMyTotalRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $totalRequests = 0;

        $em = $this->getDoctrine()->getManager();

        $nrBuyerRequests = $em->getRepository('AppBundle:BuyerAgent')
            ->getNrMyBuyerRequests($user);

        $nrGrowerRequests = $em->getRepository('AppBundle:GrowerAgent')
            ->getNrMyGrowerRequests($user);

        $totalRequests += $nrBuyerRequests;
        $totalRequests += $nrGrowerRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);

    }
    public function getMyAuctionAgencyRequestsAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $totalRequests = 0;

        $em = $this->getDoctrine()->getManager();

        $nrProductRequests = $em->getRepository('AppBundle:Auction')
            ->findMyActiveProductRequests($user);

        $totalRequests += $nrProductRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);
    }
    public function getMyOrderAssignmentRequestAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;

        $em = $this->getDoctrine()->getManager();

        $nrOrderRequests = $em->getRepository('AppBundle:AuctionOrder')
            ->findMyAuctionAgencyRequests($user);

        $totalRequests += $nrOrderRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);
    }
    public function getMyTotalAgencyRequestsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $totalRequests = 0;

        $em = $this->getDoctrine()->getManager();

        $nrProductRequests = $em->getRepository('AppBundle:Auction')
            ->findMyActiveProductRequests($user);

        $nrOrderRequests = $em->getRepository('AppBundle:AuctionOrder')
            ->findMyAuctionAgencyRequests($user);

        $totalRequests += $nrProductRequests;
        $totalRequests += $nrOrderRequests;

        return $this->render(':partials:totalRequests.html.twig', [
            'nrRequests' => $totalRequests,

        ]);
    }

    /**
     * @Route("/recommend/buyer/{id}/product/{product}",name="agent-recommend-product")
     */
    public function recommendProduct(User $buyer,Product $product){
        $agent = $this->get('security.token_storage')->getToken()->getUser();

        $myList = new MyList();
        $myList->setProduct($product);
        $myList->setRecommendedBy($agent);
        $myList->setListType("Agent Recommendations");
        $myList->setListOwner($buyer);
        $myList->setCreatedAt(new \DateTime());
        $myList->setUpdatedAt(new \DateTime());
        $myList->setProductType("Direct");

        $em = $this->getDoctrine()->getManager();
        $em->persist($myList);
        $em->flush();

        return new Response(null, 204);
    }
    /**
     * @Route("/recommend/buyer/{id}/auction/{auction}",name="agent-recommend-auction")
     */
    public function recommendAuction(User $buyer,Auction $auction){
        $agent = $this->get('security.token_storage')->getToken()->getUser();

        $myList = new MyList();
        $myList->setAuctionProduct($auction);
        $myList->setRecommendedBy($agent);
        $myList->setListType("Agent Recommendations");
        $myList->setListOwner($buyer);
        $myList->setCreatedAt(new \DateTime());
        $myList->setUpdatedAt(new \DateTime());
        $myList->setProductType("Auction");

        $em = $this->getDoctrine()->getManager();
        $em->persist($myList);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/recommendations/my",name="my-agent-recommendations")
     */
    public function myRecommendationsAction(){
        $agent = $this->get('security.token_storage')->getToken()->getUser();

        $em=$this->getDoctrine()->getManager();

        $recommendations = $em->getRepository('AppBundle:MyList')
            ->getMyRecommendations($agent);
        return $this->render(':agent/myList:recommend.htm.twig',[
            'recommendations' => $recommendations
        ]);


    }

}