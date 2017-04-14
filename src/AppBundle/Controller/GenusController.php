<?php
/*********************************************************************************
 * Karbon Framework is a PHP5 Framework developed by Maxx Ng'ang'a
 * (C) 2016 Crysoft Dynamics Ltd
 * Karbon V 1.0
 * Maxx
 * 4/12/2017
 ********************************************************************************/

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
     */
    public function newAction(){
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1,100));

        $em= $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->flush();

        return new Response('<html><body>Genus created</body></html>');
    }
    /**
     * @Route("/genus/{genusName}")
     */
    public function showAction($genusName){
        $funFact = "Octopuses can change the color of their body in just *three-tenths* of a second!";
        $funFact = $this->get('markdown.parser')
            ->transform($funFact);
        return $this->render('genus/show.html.twig',
            [
               'name' => $genusName,
                'funFact'=> $funFact,
            ]);


    }

    /**
     * @Route("/genus/{genusName}/notes",name="genus_show_notes")
     * @Method("get")
     */
    public function getNotesAction(){
        $notes = [
           ['id'=>1,'username'=>'AquaPelham','avatarUri'=>'/images/leanna.jpeg','note'=>'Octopus asked me a riddle, outsmarted me'],
           ['id'=>2,'username'=>'AquaWeaver','avatarUri'=>'/images/ryan.jpeg','note'=>'I counted 8 legs...as they wroapped around me'],
           ['id'=>3,'username'=>'AquaPelham','avatarUri'=>'/images/leanna.jpeg','note'=>'Note 3'],
        ];
        $data = [
            'notes'=>$notes,
        ];

        return new JsonResponse($data);
    }
}