<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AppController
 * @package AppBundle\Controller
 */
class AppController extends Controller
{
    /**
     * Home page action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction(Request $request)
    {
        return $this->render('AppBundle:App:home.html.twig');
    }

    public function scienceListAction(Request $request){
        $sciences = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Science')->findBy([]);
        return $this->render('AppBundle:App:sciences.html.twig', [
            'sciences' => $sciences,
        ]);
    }

    public function scienceDetailAction(Request $request){
        $scienceId = $request->attributes->get('scienceId');
        $em = $this->getDoctrine()->getManager();
        $science = $em->getRepository('AppBundle\Entity\Science')->findOneBy(['id' => $scienceId]);
        if (!$science){
            return $this->redirectToRoute('science_list');
        }
        $publications = $em->getRepository('AppBundle\Entity\Publication')->findBy(['science' => $science, 'validated'=> 1], ['publishedAt' => 'DESC'], 3);
        return $this->render('AppBundle:App:science.html.twig',[
            'science' => $science,
            'publications' => $publications,
        ]);

    }

    public function publicationDetailAction(Request $request){
        return new Response('ok');
    }
}
