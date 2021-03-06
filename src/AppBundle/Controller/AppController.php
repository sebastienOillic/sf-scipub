<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\Publication;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $publications = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Publication')->findBy(['validated' => 1], [], 3);
        return $this->render('AppBundle:App:home.html.twig', [
            'publications' => $publications,
        ]);
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
            return $this->redirectToRoute('public_science_list');
        }
        $publications = $em->getRepository('AppBundle\Entity\Publication')->findBy(['science' => $science, 'validated'=> 1], ['publishedAt' => 'DESC'], 3);
        return $this->render('AppBundle:App:science.html.twig',[
            'science' => $science,
            'publications' => $publications,
        ]);

    }

    public function publicationDetailAction(Request $request){
        $scienceId = $request->attributes->get('scienceId');
        $publicationId = $request->attributes->get('publicationId');
        $em = $this->getDoctrine()->getManager();
        $science = $em->getRepository('AppBundle\Entity\Science')->findOneBy(['id' => $scienceId]);
        if (!$science){
            return $this->redirectToRoute('public_science_list');
        }
        $publication = $em->getRepository('AppBundle\Entity\Publication')->findOneBy(['science' => $science, 'id' => $publicationId, 'validated'=> 1]);
        if (!$publication){
            return $this->redirectToRoute('public_science_detail', ['scienceId' => $science->getId()]);
        }

        return $this->render('AppBundle:App:publication.html.twig', [
            'publication' => $publication,
        ]);
    }

    /**
     * Creates a new publication entity.
     *
     */
    public function publishAction(Request $request)
    {
        $publication = new Publication();
        $form = $this
                ->createForm('AppBundle\Form\PublicationType', $publication, ['admin_mode'=> false])
                ->add('save', new SubmitType(), [
                    'attr' => [
                        'class' => 'btn btn-sm btn-success',
                    ]
                ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setPublishedAt(new \DateTime());
            $publication->setValidated(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $em->flush($publication);

            return $this->redirectToRoute('public_science_detail', ['scienceId' => $publication->getScience()->getId()]);
        }

        return $this->render('AppBundle:App:publier.html.twig', array(
            'publication' => $publication,
            'form' => $form->createView(),
        ));
    }
}
